<template>
    <div>
        <div class="plan-content">
            <div class="card pb-3">
                <div class="card-header">
                    <div class="card-title">{{ plan.title }}</div>
                    <div
                        v-if="$userId == plan.owner_id"
                        v-can="'plan_edit'"
                        class="card-tools pr-2 no-print"
                    >
                        <a v-if="!this.disabled" @click="openModal()" class="link-muted mr-3 px-1 pointer">
                            <i class="fa fa-chart-simple"></i>
                        </a>
                        <a onclick="window.print()" class="link-muted mr-3 px-1 pointer">
                            <i class="fa fa-print"></i>
                        </a>
                        <a :href="plan.id + '/edit'" class="link-muted px-1">
                            <i class="fa fa-pencil-alt"></i>
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <span class="col-12">
                            {{ htmlToText(plan.description) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 pt-2">
                    <draggable
                        :disabled="this.disabled"
                        v-bind="columnDragOptions"
                        v-model="entries"
                        @start="drag=true"
                        @end="handleEntryOrder"
                    >
                        <PlanEntry
                            v-for="(entry, index) in entries"
                            :key="entries[index].id"
                            :editable="editable"
                            :entry="entry"
                            :plan="plan"
                        ></PlanEntry>
                    </draggable>
                </div>

                <div class="col-12">
                    <!--<Calendar></Calendar>-->
                    <PlanEntry
                        v-if="$userId == plan.owner_id"
                        :plan="plan"
                        create="true">
                    </PlanEntry>
                </div>
            </div>
            <!-- * REFERENCE: overlay button in bottom right corner * -->
            <!-- <div
                id="corner-button"
                class="position-sticky d-flex justify-content-center align-items-center float-right mb-3"
                role="button"
                @click="open()"
            >
                <i class="fa fa-users"></i>
            </div> -->
            <set-achievements-modal
                :users="users"
            ></set-achievements-modal>
            <note-modal></note-modal>
            <select-users-modal
                :users="users"
                title="plan.evaluate_user"
            ></select-users-modal>
        </div>
        <!-- this element is hidden and should only be shown when printing a user-evaluation -->
        <div class="d-none print-achievements">
            <h2 id="user-name"></h2>
            <table class="table">
                <tbody></tbody>
            </table>
        </div>
    </div>
</template>

<script>
import draggable from 'vuedraggable';

const Calendar =
    () => import('../calendar/Calendar');
const PlanEntry =
    () => import('./PlanEntry');

export default {
    props: {
        plan: [],
        editable: {
            type: Boolean,
            default: false,
        },
        users: [],
    },
    data() {
        return {
            entries: [],
            entry_order: [],
            subscriptions: {},
            search: '',
            disabled: false,
            errors: {},
        }
    },
    methods: {
        loaderEvent() {
            axios.get('/planEntries?plan_id=' + this.plan.id)
                .then(response => {
                    if (this.plan.entry_order != null) {
                        this.entry_order = this.plan.entry_order;
                        // rearrange entries to the specified order by their ID
                        // since this is O[n^2], it could become a performance issue in the future
                        this.entries = this.entry_order.map(
                            id => response.data.entries.find(entry => entry.id === id)
                        );
                    } else {
                        this.entries = response.data.entries;
                    }
                })
                .catch(e => {
                    console.log(e);
                });
        },
        handleEntryAdded(entry) {
            this.entries.push(entry);
            this.entry_order.push(entry.id);
            this.updateEntryOrder();
        },
        handleEntryDeleted(entry) {
            let index = this.entries.indexOf(entry);
            this.entries.splice(index, 1);
            this.entry_order.splice(index, 1);
            this.updateEntryOrder();
        },
        handleEntryOrder(e) {
            if (e.newIndex === e.oldIndex) return;
            this.entry_order = this.entries.map(entry => entry.id);
            this.updateEntryOrder();
        },
        updateEntryOrder() {
            // Send the current order of entries to the server
            axios.put("/plans/" + this.plan.id + "/syncEntriesOrder", {entry_order: this.entry_order})
                .catch(err => {
                    console.log(err.response);
                    alert(err.response.statusText);
                });
        },
        updateAchievements(objective_id) {
            // go through every entry
            this.$children[0].$children.forEach((planEntry) => {
                const objectives = planEntry.$children[0].$children; // get all objective-components

                for (let i = 0; i < objectives.length; i++) {
                    const objective = objectives[i];
                    // filter for EnablingObjectives
                    if (objective.$options._componentTag !== 'EnablingObjectives') continue;
                    // try to find if one of its ObjectiveBoxes has the corresponding objective_id
                    const enabling = objective.$children.find(child => child.objective_id == objective_id);
                    if (enabling !== undefined) {
                        enabling.updateAchievements(this.users); // call the update-function inside this component
                        break;
                    }
                }
            });
        },
        openModal() {
            this.$modal.show('select-users-modal', { users: this.users });
        },
        async handleUserModalClose(user) {
            const table = document.querySelector('.print-achievements tbody');
            // in case the preview got closed accidentally, don't reload everything
            if (user.id != table.id) {
                table.id = user.id;
                table.innerHTML = ''; // empty table body
                await this.fillTable(user);
            }
            this.printAchievements();
        },
        printAchievements() {
            const plan = document.getElementsByClassName('plan-content')[0];
            const table = document.getElementsByClassName('print-achievements')[0];

            // only show the achievement-table for this print
            plan.classList.add('d-print-none');
            table.classList.add('d-print-block');
            window.print();

            // immediately reset the print styles
            plan.classList.remove('d-print-none');
            table.classList.remove('d-print-block');
        },
        /**
         * fill the achievements-table with all objectives that are subscribed to this plan
         * @param {Object} user user for which the evaluation should be created
         */
        async fillTable(user) {
            const data = await axios.get('/plans/' + this.plan.id + '/getUserAchievements/' + user.id)
                .then(response => response.data.achievements);

            const table = document.querySelector('.print-achievements tbody');
            document.getElementById('user-name').innerText = `${user.firstname} ${user.lastname}`;

            // create a new row for each terminal-objective
            data.terminal.forEach(terminal => {
                const ter_obj = terminal.terminal_objective;
                const tr = document.createElement('tr');

                // the first cell should be the title of the terminal-objective
                this.addCell(tr, ter_obj.title);

                // every following cell in this row is an enabling-objective
                ter_obj.enabling_objectives.forEach(enabling => {
                    this.addCell(tr, enabling.title, (enabling.achievements[0]?.status[1] ?? '0'));
                });

                table.appendChild(tr);
            });

            let objectives = {};
            // first connect enabling-objectives with the same terminal-objective into the same category
            data.enabling.forEach((enabling, index) => {
                const ter_id = enabling.enabling_objective.terminal_objective.id;

                if (objectives[ter_id] === undefined) {
                    objectives[ter_id] = [index];
                } else {
                    objectives[ter_id].push(index);
                }
            });

            for (const [ter_id, enabling_indices] of Object.entries(objectives)) {
                const ter_obj = data.enabling[enabling_indices[0]].enabling_objective.terminal_objective;
                const tr = document.createElement('tr');

                // the first cell should be the title of the terminal-objective
                this.addCell(tr, ter_obj.title);

                // add enabling-objectives from the response-data, based of the corresponding indices
                enabling_indices.forEach(index => {
                    const enabling = data.enabling[index].enabling_objective;
                    this.addCell(tr, enabling.title, (enabling.achievements[0]?.status[1] ?? '0'));
                });

                table.appendChild(tr);
            }
        },
        /**
         * creates a td-element and appends it to the given tr-element
         * @param {HTMLTableRowElement} tr tr-element of which the td-cell should be appended to
         * @param {String} title text for innerHTML
         * @param {String} status status-number of the achievement
         */
        addCell(tr, title, status = false) {
            const td = document.createElement('td');
            td.innerHTML = title; // title is wrapped around a p-tag

            if (status !== false) {
                // the background should be colored based on the teacher-side of the achievement
                td.classList.add('status-' + status); // classname => status-0/1/2/3
            }

            tr.appendChild(td);
        },
    },
    mounted() {
        localStorage.removeItem('user-datatable-selection'); // reset selection to prevent wrong inputs
        this.disabled = this.$userId != this.plan.owner_id;
        this.loaderEvent();
        this.$eventHub.$on('plan_entry_added', (entry) => {
            this.handleEntryAdded(entry);
        });
        this.$eventHub.$on('plan_entry_updated', (e) => {
            this.loaderEvent();
        });
        this.$eventHub.$on('plan_entry_deleted', (entry) => {
            this.handleEntryDeleted(entry);
        });
    },
    computed: {
        columnDragOptions() {
            return {
                animation: 200,
                // checks if a mobile-browser is used and if true, add delay
                ...(/Mobi/i.test(window.navigator.userAgent) && {delay: 200}),
            };
        },
    },
    components: {
        Calendar,
        PlanEntry,
        draggable,
    },
}
</script>
<style scoped>
/* #corner-button {
    color: white;
    background-color: #333;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    bottom: 25px;
} */
@media print {
    .print-achievements { print-color-adjust: exact; }
    .print-achievements >>> p { margin: 0px; }
    .print-achievements >>> td { padding: 10px; }
    .print-achievements >>> tr:first-child,
    .print-achievements >>> tr:first-child td { border-top: 0px; }
    .print-achievements >>> .status-0 { background-color: #d2d6de !important; }
    .print-achievements >>> .status-1 { background-color: #00a65a !important; }
    .print-achievements >>> .status-2 { background-color: #fd7e14 !important; }
    .print-achievements >>> .status-3 { background-color: #dd4b39 !important; }
}
</style>