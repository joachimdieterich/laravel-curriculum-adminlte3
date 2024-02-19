<template >
    <div class="row ">


        <div class="col-12 pt-2">
            <!-- Bewegungsfeld -->
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
                    :entry="entry"
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
        group: [],
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
                        this.entry_order = this.plan.entry_order
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
