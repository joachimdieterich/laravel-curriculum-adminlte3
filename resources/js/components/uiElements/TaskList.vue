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

            <span class="text">
                <a class="link-muted text-decoration-none"
                   :href="'/tasks/'+task.task.id"
                   v-html="task.task.title"></a>
            </span>

            <small class="badge badge-primary pull-right p-1 mt-1">
                <i class="far fa-clock"></i>
                <span v-html="task.task.due_date"></span>
            </small>
            <!-- General tools such as edit or delete-->
            <div class="tools">
                <a onclick="deleteTask(task.task)" >
                    <i class="fas fa-trash"></i>
                </a>
            </div>
        </li>
        <li class="pointer bg-white">
            <a @click.prevent="open('task-modal');">
                <i class="px-2 fa fa-plus text-muted"></i> {{ trans('global.task.create')}}
            </a>
        </li>
    </ul>
</template>

<script>
    export default {
        props: {
            'tasks': '',
            'subscribable_type': String,
            'subscribable_id': Number,
        },
        data() {
            return {
                status: null,
                task: null
            };
        },

        methods: {
            open(modal, relationKey) {
                this.$modal.show(modal, { 'subscribable_type': this.subscribable_type, 'subscribable_id': this.subscribable_id });
            },
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
