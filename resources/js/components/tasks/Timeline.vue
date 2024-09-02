<template>
    <div >
        <!-- Timeline time label -->
       <div class="post small" v-for="subscription in activity.subscriptions">
            <!-- Before each timeline item corresponds to one icon on the left scale -->
            <span  v-if="subscription.subscribable_type === 'App\\User'">
                <i class="fas fa-user pr-1"></i>
                <a :href="'/users/'+subscription.subscribable.id" class="link-muted">
                    {{ trans('global.user.title_singular')}}: {{ ((subscription.subscribable.title) ? subscription.subscribable.title+" ("+subscription.owner.firstname+" "+subscription.owner.lastname+")" : subscription.subscribable.firstname+' '+subscription.subscribable.lastname ) }}
                </a>
            </span>
            <span  v-else-if="subscription.subscribable_type === 'App\\Group'">
                <i  class="fas fa-users pr-1"></i>
                <a :href="'/groups/'+subscription.subscribable.id" class="link-muted">
                    {{ trans('global.group.title_singular')}}: {{ ((subscription.subscribable.title) ? subscription.subscribable.title+" ("+subscription.owner.firstname+" "+subscription.owner.lastname+")" : subscription.subscribable.firstname+' '+subscription.subscribable.lastname ) }}
                </a>
            </span>
            <span v-else-if="subscription.subscribable_type === 'App\\LogbookEntry'">
                <i class="fas fa-book pr-1"></i>
                <a :href="'/logbooks/'+subscription.subscribable.id" class="link-muted">
                    {{ trans('global.logbookEntry.title_singular')}}: {{ ((subscription.subscribable.title) ? subscription.subscribable.title+" ("+subscription.owner.firstname+" "+subscription.owner.lastname+")" : subscription.subscribable.firstname+' '+subscription.subscribable.lastname ) }}
                </a>
            </span>
            <span v-else-if="subscription.subscribable_type === 'App\\Plan'">
                <i  class="far fa-clipboard pr-1"></i>
                <a :href="'/plans/'+subscription.subscribable.id" class="link-muted">
                    {{ trans('global.plan.title_singular')}}: {{ ((subscription.subscribable.title) ? subscription.subscribable.title+" ("+subscription.owner.firstname+" "+subscription.owner.lastname+")" : subscription.subscribable.firstname+' '+subscription.subscribable.lastname ) }}
                </a>
            </span>
            <span v-else-if="subscription.subscribable_type === 'App\\KanbanItem'">
                <i class="fa fa-columns pr-1"></i>
                <a :href="'/kanbanItems/'+subscription.subscribable.id" class="link-muted">
                    {{ trans('global.kanban.title_singular')}}: {{ ((subscription.subscribable.title) ? subscription.subscribable.title+" ("+subscription.owner.firstname+" "+subscription.owner.lastname+")" : subscription.subscribable.firstname+' '+subscription.subscribable.lastname ) }}
                </a>
            </span>
            <!-- Time -->
            <span class="pull-right "><i class="fa fa-link pr-1"></i> <span v-dompurify-html="subscription.created_at"></span></span>
        </div>
    </div>

</template>


<script>

    export default {
        props: ['task'],
        data: function() {
            return {
              activity: null,
            }
        },
        methods: {
            loadData: function () {
                axios.get('/tasks/'+this.task.id+'/activity').then(response => {
                    this.activity = response.data.activity;
                }).catch(e => {
                    this.form.errors = error.response.data.errors;
                });
            },
        },
        created() {
            this.loadData();
        },


        }
</script>
