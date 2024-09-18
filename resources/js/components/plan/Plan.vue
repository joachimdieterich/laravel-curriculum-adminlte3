<template>
    <div>
        <div class="plan-content">
            <div class="card pb-3">
                <div class="card-header">
                    <div class="card-title">{{ plan.title }}</div>
                    <div
                        v-if="editable"
                        v-can="'plan_edit'"
                        class="card-tools pr-2 no-print user-select-none"
                    >
                        <span
                            class="mr-3"
                            :title="mode_toggle ? 'Kompetenz anklicken um mehrere Personen einzuschätzen' : 'Einschätzungen für einzelne Person abgeben'"
                        >
                            <span>
                                <button
                                    @click="openUserModal()"
                                    class="btn btn-tool text-dark px-0"
                                    :class="mode_toggle ? 'disabled' : ''"
                                    style="margin-top: -15px;"
                                >
                                    {{
                                        this.selected_user == null
                                            ? 'Kein Benutzer ausgew&auml;hlt'
                                            : this.selected_user?.firstname + ' ' + this.selected_user?.lastname
                                    }}
                                </button>
                            </span>
                            <a class="link-muted pl-1" style="padding-right: 2px;">
                                <i class="fa fa-user"></i>
                            </a>
                            <span class="custom-switch custom-switch-on-green" style="margin-right: -6px;">
                                <input type="checkbox" id="mode_toggle" class="custom-control-input" v-model="mode_toggle">
                                <label for="mode_toggle" class="custom-control-label pointer"></label>
                            </span>
                            <a class="link-muted pr-1">
                                <i class="fa fa-users"></i>
                            </a>
                        </span>
                        <a @click="openUserModal(!mode_toggle)" class="link-muted mr-3 px-1 pointer">
                            <i class="fa fa-chart-simple"></i>
                        </a>
                        <a onclick="window.print()" class="link-muted mr-3 px-1 pointer">
                            <i class="fa fa-print"></i>
                        </a>
                        <a v-if="editable" class="link-muted px-1">
                            <i class="fa fa-pencil-alt"></i>
                            <span class="custom-switch custom-switch-on-green pull-right" style="margin-right: -6px;">
                                <input type="checkbox" id="edit_toggle" class="custom-control-input" v-model="edit_toggle">
                                <label for="edit_toggle" class="custom-control-label pointer"></label>
                            </span>
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
                            :editable="editable && edit_toggle"
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
                        create="true"
                    ></PlanEntry>
                </div>
            </div>
            <!-- * REFERENCE: overlay button in bottom right corner * -->
            <div
                id="corner-button"
                class="position-sticky d-flex align-items-center float-right px-3"
                :style="mode_toggle ? 'display: none !important' : ''"
                role="button"
                @click="openUserModal()"
            >
                <span class="pr-2">
                    {{
                        this.selected_user == null
                            ? trans('global.select_users')
                            : this.selected_user?.firstname + ' ' + this.selected_user?.lastname
                    }}
                </span>
                <i class="fa fa-user"></i>
            </div>
            <set-achievements-modal
                :users="users"
            ></set-achievements-modal>
            <note-modal></note-modal>
            <select-users-modal
                :users="users"
                :multiple="mode_toggle"
                :title="mode_toggle ? 'global.plan.evaluate_user' : 'global.select_users'"
                :submitText="mode_toggle ? 'global.open' : 'global.save'"
            ></select-users-modal>
        </div>
        <PlanIndexAddWidget
            :visible="false"
        />
    </div>
</template>

<script>
import draggable from 'vuedraggable';

const Calendar = () => import('../calendar/Calendar');
const PlanEntry = () => import('./PlanEntry');
const PlanIndexAddWidget = () => import('./PlanIndexAddWidget.vue');

export default {
    props: {
        plan: null,
        editable: { // true => subscriber with edit-rights | plan-owner
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
            disabled: true, // false => only plan-owner
            mode_toggle: true, // true => all users | false => single user
            edit_toggle: true,
            selected_user: null,
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
        openUserModal(showAchievements) {
            // if the user-modal should be skipped and only one user is selected
            if (showAchievements && this.selected_user !== null) {
                // instantly open the achievements-overview tab
                this.handleUserModalClose([this.selected_user], true);
            // EDGE CASE: on show-achievements and no selected user, the select-user modal still opens without opening the overview-tab
            } else {
                this.$modal.show('select-users-modal');
            }
        },
        async handleUserModalClose(users, skip = false) {
            if (!this.mode_toggle && !skip) {
                this.selected_user = users;
                localStorage.setItem('user-datatable-selection', [users.id]); // used by AchievementIndicator.vue
            } else {
                const ids = users.map(user => user.id);
                window.open('/plans/' + this.plan.id + '/getUserAchievements/' + ids);
            }
        },
    },
    mounted() {
        localStorage.removeItem('user-datatable-selection'); // reset selection to prevent wrong inputs
        this.loaderEvent();

        if (this.$userId == this.plan.owner_id) {
            this.disabled = false;
            // only listen to events if plan-owner
            this.$eventHub.$on('plan_entry_added', (entry) => {
                this.handleEntryAdded(entry);
            });
            this.$eventHub.$on('plan_entry_updated', (e) => {
                this.loaderEvent();
            });
            this.$eventHub.$on('plan_entry_deleted', (entry) => {
                this.handleEntryDeleted(entry);
            });
            this.$eventHub.$on('update_users', () => {
                axios.get('/plans/' + this.plan.id + '/getUsers')
                    .then(response => this.users = response.data);
            });
        }
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
        PlanIndexAddWidget,
        draggable,
    },
}
</script>
<style scoped>
#corner-button {
    color: white;
    background-color: #333;
    border-radius: 25px;
    min-width: 50px;
    height: 50px;
    bottom: 25px;
}
</style>