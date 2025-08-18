<template>
    <div>
        <ul class="todo-list" data-widget="todo-list">

            <li v-for="task in tasks">
                <!-- drag handle -->
                <!--               <span class="handle">
                                <i class="fas fa-ellipsis-v"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </span>-->
                <!-- checkbox -->
                <div class="icheck-primary d-inline ml-2">
                    <input
                        type="checkbox"
                        value=""
                        name="todo1"
                        :id="'todo_checkbox'+task.task.id"
                        @click="complete(task.task.id)"
                        v-bind:checked="isCompleted(task)">
                    <label :for="'todo_checkbox'+task.task.id" aria-label=""></label>
                </div>

                <span class="text">
                <a
                    :href="'/tasks/' + task.task.id"
                    class="link-muted text-decoration-none"
                >
                    {{ task.task.title }}
                </a>
            </span>

                <small class="badge badge-secondary pull-right p-1 mt-1">
                    <i class="far fa-clock"></i>
                    {{ task.task.due_date }}
                </small>
                <!-- General tools such as edit or delete-->
                <div class="tools">
                    <a v-permission="'task_delete, ' + subscribable_type + '_task_delete'"
                       onclick="deleteTask(task.task)">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </li>
        </ul>
        <table
            class="table table-hover datatable media_table">
            <tr>
                <td
                    class="py-2 link-muted text-sm pointer"
                    v-permission="'lms_create'"
                    @click.prevent="open('task-modal');">
                    <i class="fa fa-plus px-2 "></i> {{ trans('global.task.create') }}
                </td>
            </tr>
        </table>
    </div>
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
