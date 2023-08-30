<template >
    <div class="row ">


        <div class="col-12 pt-2">
            <!-- Bewegungsfeld -->
            <PlanEntry
                v-for="(entry, index) in entries"
                v-bind:key="entry.id"
                :entry="entry"
                :plan="plan"
            ></PlanEntry>
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
                plans: [],
                entries: [],
                subscriptions: {},
                search: '',
                errors: {}
            }
        },
        methods: {
            loaderEvent(){
                axios.get('/planEntries?plan_id=' + this.plan.id)
                    .then(response => {
                        this.entries = response.data.entries;
                    })
                    .catch(e => {
                        console.log(e);
                    });
            },

            handleEntryDeleted(entry){
                let index = this.entries.indexOf(entry);
                this.entries.splice(index, 1);
            },
        },

        mounted() {
            this.loaderEvent();
            this.entries = this.plan.entries;
            this.$eventHub.$on('plan_entry_added', (e) => {
                this.loaderEvent();
            });
            this.$eventHub.$on('plan_entry_updated', (e) => {
                this.loaderEvent();
            });
            this.$eventHub.$on('plan_entry_deleted', (e) => {
                this.handleEntryDeleted();
            });
        },
        components: {
            Calendar,
            PlanEntry
        },
    }
</script>
