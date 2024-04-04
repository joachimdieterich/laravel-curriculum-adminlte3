<template >
    <div>
        <div class="card pb-3">
            <div class="card-header">
                <div class="card-title">{{ plan.title }}</div>
                <div
                    v-if="$userId == plan.owner_id"
                    v-can="'plan_edit'"
                    class="card-tools pr-2 no-print"
                >
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

        <div class="row ">
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
        <!-- overlay button in bottom right corner -->
        <!-- <div
            id="corner-button"
            class="position-sticky d-flex justify-content-center align-items-center float-right mb-3"
            role="button"
            @click="open()"
        >
            <i class="fa fa-users"></i>
        </div> -->
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
        loaderEvent(){
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
        handleEntryDeleted(entry){
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
    },
    mounted() {
        localStorage.removeItem('user-datatable-selection'); // reset selection to prevent wrong inputs
        this.disabled = this.$userId != this.plan.owner_id;
        this.loaderEvent();
        this.$eventHub.$on('plan_entry_added', (e) => {
            this.handleEntryAdded(e);
        });
        this.$eventHub.$on('plan_entry_updated', (e) => {
            this.loaderEvent();
        });
        this.$eventHub.$on('plan_entry_deleted', (e) => {
            this.handleEntryDeleted(e);
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
        draggable
    },
}
</script>
<!-- <style scoped>
#corner-button {
    color: white;
    background-color: #333;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    bottom: 25px;
}
</style> -->