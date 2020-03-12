<template>
    <div >
            <!-- Timeline time label -->
           <div class="post" v-for="subscription in activity.subscriptions">
               
               
                    <!-- Before each timeline item corresponds to one icon on the left scale -->
                    <span>
                        <i v-if="subscription.subscribable_type === 'App\\User'" class="fas fa-user"></i>
                        <i v-else-if="subscription.subscribable_type === 'App\\Group'" class="fas fa-users "></i> 
                        <i v-else-if="subscription.subscribable_type === 'App\\LogbookEntry'" class="fas fa-book "></i> 
                        <a href="#" class="text-md"> 
                            {{ ((subscription.subscribable.title) ? subscription.subscribable.title : subscription.subscribable.username) }} ({{subscription.owner.username}})
                        </a>
                    </span>
                    <!-- Time -->
                    <span class="pull-right "><i class="fas fa-clock"></i> <span v-html="subscription.created_at"></span></span>
                    
                
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
