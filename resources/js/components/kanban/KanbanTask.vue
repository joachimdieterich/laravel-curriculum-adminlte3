<template>
    <div >                  
        <div v-for="task in tasks">
            <div class="py-1">
                <input 
                    type="checkbox" 
                    value="" 
                    name="todo1" 
                    id="todoCheck1" 
                    class="pull-right my-2"
                    @click="complete(task.task.id)"
                    v-bind:checked="isCompleted(task)">
                <a class="text-muted small " :href="'/tasks/'+task.task.id" v-html="task.task.title"></a>
            </div>
           
            <small class="badge badge-primary pull-left mt-1 mb-2">
                <i class="far fa-clock"></i>
                <span v-html="date(task.task.due_date)"></span>
            </small>
            
        </div>

    </div>
</template>

<script>
    export default {
        props: {
            'tasks': ''
        },
        data() {
            return {
                status: null,
                task: null
            };
        },
        
        methods: {
            async complete(id) {
                try {  
                    this.status = (await axios.patch('/tasks/'+id+'/complete')).data.status;
                } catch(error) {
                    this.errors = error.response.data.errors;
                } 
                //alert(this.status);
            },
             isCompleted(task) {
                var returnvalue = false;
                if (typeof task.task.subscriptions[0] !== 'undefined'){
                    if (task.task.subscriptions[0].completion_date !== null){
                        returnvalue = true;
                    }
                }
                
                return returnvalue;
            },
            date(date) {
                var value = new Date(date.replace(/-/g, "/"));
                var dateFormat = { weekday: 'short', day: '2-digit', month: '2-digit', year: 'numeric'};

                return value.toLocaleString([], dateFormat);
            },
        },
        
        mounted(){
            
        }
        
    }
</script>
