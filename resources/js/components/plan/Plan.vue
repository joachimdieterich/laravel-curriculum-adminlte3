<template >
    <div class="row ">


        <div class="col-12 pt-2">
            <!-- Bewegungsfeld -->
            <draggable
                v-can="'plan_edit'"
                v-model="entry_order"
                @start="drag=true"
                @end="handleEntryOrder"
            >
                <PlanEntry
                    v-for="(value, index) in entry_order"
                    v-bind:key="entries[index].id"
                    :entry="entries[index]"
                    :plan="plan"
                ></PlanEntry>
            </draggable>
        </div>

        <div class="col-12">
            <!--            <Calendar></Calendar>-->
            <PlanEntry
                v-if="$userId == plan.owner_id "
                :plan="plan"
                create="true">
            </PlanEntry>

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
    },
    data() {
        return {
            entries: [],
            entry_order: [],
            subscriptions: {},
            search: '',
            errors: {},
        }
    },
    methods: {
        loaderEvent(){
            axios.get('/planEntries?plan_id=' + this.plan.id)
                .then(response => {
                    if (this.plan.entry_order != null) {
                        this.entry_order = this.plan.entry_order
                        this.entries = this.entry_order.map(
                            order_id => response.data.entries.find(entry => entry.id === order_id)
                        );
                    } else {
                        this.entries = response.data.entries;
                        this.entry_order = this.entries.map(entry => entry.id);
                    }
                })
                .catch(e => {
                    console.log(e);
                });
        },
        handleEntryAdded(entry) {
            this.entries.push(entry);
            this.entry_order.push(entry.id);
            this.handleEntryOrder();
        },
        handleEntryDeleted(entry){
            let index = this.entries.indexOf(entry);
            this.entries.splice(index, 1);
            this.entry_order.splice(index, 1);
            this.updateEntryOrder();
        },
        //? maybe put event in Objectives.vue
        handleObjectiveAdded(objective, entryId) {
            const entry = this.entries.find(entry => entry.id === entryId);
        },
        handleEntryOrder() {
            // rearrange entries to the specified order by ID
            // since this is O[n^2], it could be a performance problem in the future
            this.entries = this.entry_order.map(
                order_id => this.entries.find(entry => entry.id === order_id)
            );
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
        localStorage.removeItem('user-datatable-selection'); //reset selection to prevent wrong inputs
        this.loaderEvent();
        this.entries = this.plan.entries;
        this.$eventHub.$on('plan_entry_added', (e) => {
            this.handleEntryAdded(e);
        });
        this.$eventHub.$on('plan_entry_updated', (e) => {
            this.loaderEvent();
        });
        this.$eventHub.$on('plan_entry_deleted', (e) => {
            this.handleEntryDeleted(e);
        });
        this.$eventHub.$on('objective_added', (objective, entryId) => {
            this.handleObjectiveAdded(objective, entryId);
        });
    },
    components: {
        Calendar,
        PlanEntry,
        draggable
    },
}
</script>
