<template>
    <div>
        <Absence
            v-for="absence in absences"
            v-bind:data="absence"
            v-bind:key="'absence_container'+absence.id"
            :absence="absence">
        </Absence>

        <table
            class="table table-hover datatable media_table">
            <tr>
                <td
                    class="py-2 link-muted text-sm pointer"
                    v-permission="'lms_create'"
                    @click.prevent="open('absence-modal', 'referenceable');">
                    <i class="fa fa-plus px-2 "></i> {{ trans('global.absences.create') }}
                </td>
            </tr>
        </table>
    </div>
</template>

<script>
const Absence =
    () => import('../absence/Absence.vue');
    //import Absence from '../absence/Absence.vue'
    export default {
        props: {
            'absences': Array,
            'subscribable_type': String,
            'subscribable_id': Number,
            'logbook': {},
            'entry': {},
        },
        data() {
            return {
                errors: {}
            }
        },

        methods: {
            open(modal, relationKey) {
                if (modal === 'absence-modal'){
                    this.$modal.show(modal, JSON.stringify(_.merge({ 'referenceable_type': 'App\\LogbookEntry', 'referenceable_id': this.entry.id}, this.logbook)));
                }

            },
            /*loaderEvent(){
                axios.get('/absences?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id)
                    .then(response => {
                        this.subscriptions = response.data.message;
                    })
                    .catch(e => {
                        //this.errors = e.data.errors;
                    });
            }*/
        },

        mounted(){

        },
        components: {
            Absence
        }

    }
</script>
