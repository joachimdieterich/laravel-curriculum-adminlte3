<template>
    <ul class="todo-list" data-widget="todo-list">

        <li v-for="task in tasks" >
            <!-- drag handle -->
<!--               <span class="handle">
                <i class="fas fa-ellipsis-v"></i>
                <i class="fas fa-ellipsis-v"></i>
            </span>-->
            <!-- checkbox -->
            <div  class="icheck-primary d-inline ml-2">
                <input
                    type="checkbox"
                    value=""
                    name="todo1"
                    id="todoCheck1"
                    @click="complete(task.task.id)"
                    v-bind:checked="isCompleted(task)">
                <label for="todoCheck1"></label>
            </div>
            <!-- todo text -->
            <span class="text"><a :href="'/tasks/'+task.task.id" v-html="task.task.title"></a></span>
            <!-- Emphasis label -->
            <small class="badge badge-primary pull-right"><i class="far fa-clock"></i> <span v-html="task.task.due_date"></span></small>
            <!-- General tools such as edit or delete-->
            <div class="tools">
                <a onclick="deleteTask(task.task)" >
                    <i class="fas fa-trash"></i>
                </a>
            </div>
        </li>
    </ul>
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
            },
             isCompleted(task) {
                var returnvalue = false;
                if (typeof task.task.subscriptions[0] !== 'undefined'){
                    if (task.task.subscriptions[0].completion_date !== null){
                        returnvalue = true;
                    }
                }

                return returnvalue;
            }
        },

        mounted(){

        }

    }
</script>
