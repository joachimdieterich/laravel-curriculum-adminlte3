<template >
    <div class="row">
        <div id="task-content"
             class="col-md-12 m-0">

            <ul class="todo-list pb-1"
                data-widget="todo-list">
                <li class="bg-gray-light text-muted"
                @click="this.$eventHub.emit('createTask', true);">
                    <i class="fa fa-plus pr-2"></i>
                    {{ trans('global.task.create') }}
                </li>

                <li  v-for="task in tasks"
                     class="bg-light">

                    <div class="icheck-primary d-inline ml-2">
                        <input
                            type="checkbox"
                            :id="'todoCheck_'+ task.id"
                            :name="'todoCheck_'+ task.id"
                            @click.prevent="complete( task )"
                            :checked="isCompleted(task)"
                        >
                        <label for="todoCheck1"></label>
                    </div>

                    <span class="ml-3 text">
                        <a class="link-muted"
                           :href="'/tasks/' + task.id" >
                            {{ task.title }}
                        </a>
                    </span>

                    <small
                        v-if="task.due_date"
                        class="badge badge-secondary pull-right">
                        <i class="fa fa-calendar-check"></i>
                        {{ task.due_date }}
                    </small>


                    <div class="tools">
                        <a @click.prevent="editTask(task)">
                            <i class="fa fa-pencil-alt mr-3 text-muted"></i>
                        </a>
                        <a @click.prevent="confirmItemDelete(task)" >
                            <i class="fas fa-trash text-danger"></i>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
        <div id="task-datatable-wrapper"
             class="w-100 dataTablesWrapper">
            <DataTable
                id="task-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display:none; "
            ></DataTable>
        </div>

        <Teleport to="body">
            <TaskModal
                :show="this.showTaskModal"
                @close="this.showTaskModal = false"
                :params="currentTask"
            ></TaskModal>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.task.delete')"
                :description="trans('global.task.delete_helper')"
                css= 'danger'
                :ok_label="trans('trans.global.ok')"
                :cancel_label="trans('trans.global.cancel')"
                @close="() => {
                    this.showConfirm = false;
                }"
                @confirm="() => {
                    this.showConfirm = false;
                    this.destroy();
                }"
            ></ConfirmModal>
        </Teleport>
    </div>
</template>


<script>
import TaskModal from "../task/TaskModal";
import IndexWidget from "../uiElements/IndexWidget";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal";
DataTable.use(DataTablesCore);

export default {
    props: {

    },
    data() {
        return {
            component_id: this._uid,
            tasks: null,
            search: '',
            showTaskModal: false,
            showConfirm: false,
            url: '/tasks/list',
            errors: {},
            currentTask: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
                { title: 'subscriptions', data: 'subscriptions'},
            ],
            options : this.$dtOptions,
            modalMode: 'edit'
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('task-added', (task) => {
            this.showTaskModal = false;
            this.tasks.push(task);
        });

        this.$eventHub.on('task-updated', (task) => {
            this.showTaskModal = false;
            this.update(task);
        });
        this.$eventHub.on('createTask', () => {
            console.log('ddd');
            this.currentTask = {};
            this.showTaskModal = true;
        });
    },
    methods: {
        editTask(task){
            this.currentTask = task;
            this.showTaskModal = true;
        },
        loaderEvent(){
            const dt = $('#task-datatable').DataTable();
            dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.tasks = dt.rows({page: 'current'}).data().toArray();

                $('#task-content').insertBefore('#task-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                dt.search(filter).draw();
            });
        },
        confirmItemDelete(task){
            this.currentTask = task;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/tasks/' + this.currentTask.id)
                .then(res => {
                    let index = this.tasks.indexOf(this.currentTask);
                    this.tasks.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        update(task) {
            const index = this.tasks.findIndex(
                vc => vc.id === task.id
            );

            for (const [key, value] of Object.entries(task)) {
                this.tasks[index][key] = value;
            }
        },
        complete(task) {
            console.log(task.subscriptions);
            axios.patch('/tasks/' + task.id + '/complete')
                .then(res => {
                    //this.loaderEvent();
                    task.subscriptions[0] = res.data;
                    console.log(res.data);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        isCompleted(task){
            let subscription = task.subscriptions
            return ( subscription[0]?.completion_date != null && typeof (subscription[0]?.completion_date) !== 'undefined' ) ? true : false;
        }

    },
    components: {
        ConfirmModal,
        DataTable,
        TaskModal,
        IndexWidget
    },
}
</script>
