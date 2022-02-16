<template>

    <ul class="todo-list" data-widget="todo-list">
        <Absence
            v-for="absence in absences"
             v-bind:data="absence"
            v-bind:key="'absence_container'+absence.id"
            :absence="absence"></Absence>
        <li class="pointer bg-white">
            <a @click.prevent="open('absence-modal', 'referenceable');">
                <i class="px-2 fa fa-plus text-muted"></i> {{ trans('global.absences.create')}}
            </a>
        </li>
    </ul>

</template>

<script>
    import Absence from '../absence/Absence.vue'
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
