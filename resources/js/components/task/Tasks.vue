<template >
    <div class="row">
        <div
            id="task-content"
            class="col-md-12 m-0"
        >
            <ul
                class="todo-list pb-1"
                data-widget="todo-list"
            >
                <li
                    class="bg-gray-light text-muted pointer"
                    @click="openModal()"
                >
                    <i class="fa fa-plus pr-2"></i>
                    {{ trans('global.task.create') }}
                </li>

                <li v-for="task in tasks"
                    class="bg-light"
                >
                    <div class="icheck-primary d-inline ml-2">
                        <input
                            type="checkbox"
                            :id="'todoCheck_' + task.id"
                            :name="'todoCheck_' + task.id"
                            @click.prevent="complete( task )"
                            :checked="isCompleted(task)"
                        />
                        <label for="todoCheck1"></label>
                    </div>

                    <span class="ml-3 text">
                        <a
                            :href="'/tasks/' + task.id"
                            class="link-muted"
                        >
                            {{ task.title }}
                        </a>
                    </span>

                    <small v-if="task.due_date"
                        class="badge badge-secondary pull-right"
                    >
                        <i class="fa fa-calendar-check"></i>
                        {{ task.due_date }}
                    </small>

                    <div class="tools">
                        <a @click.prevent="openModal(task)">
                            <i class="fa fa-pencil-alt mr-3 text-muted"></i>
                        </a>
                        <a @click.prevent="confirmItemDelete(task)">
                            <i class="fas fa-trash text-danger"></i>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
        <div
            id="task-datatable-wrapper"
            class="w-100 dataTablesWrapper"
        >
            <DataTable
                :id="'task-datatable_' + component_id"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display: none;"
            />
        </div>

        <Teleport to="body">
            <TaskModal/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.task.delete')"
                :description="trans('global.task.delete_helper')"
                @close="() => {
                    this.showConfirm = false;
                }"
                @confirm="() => {
                    this.showConfirm = false;
                    this.destroy();
                }"
            />
        </Teleport>
    </div>
</template>
<script>
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import {useGlobalStore} from "../../store/global";
import TaskModal from "./TaskModal.vue";
DataTable.use(DataTablesCore);

export default {
    props: {
        subscribable_type: {
            type: String,
            default: null,
        },
        subscribable_id: {
            type: Number,
            default: null,
        },
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            tasks: null,
            search: '',
            showConfirm: false,
            url: this.subscribable_type && this.subscribable_id
                ? '/tasksSubscriptions?subscribable_type=' + this.subscribable_type + '&subscribable_id=' + this.subscribable_id
                : '/tasks/list?filter=' + this.filter,
            currentTask: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
                { title: 'subscriptions', data: 'subscriptions'},
            ],
            options : this.$dtOptions,
            dt: null,
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('task-added', (task) => {
            this.tasks.push(task);
        });

        this.$eventHub.on('task-updated', (updatedTask) => {
            let task = this.tasks.find(t => t.id === updatedTask.id);

            Object.assign(task, updatedTask);
        });
    },
    methods: {
        openModal(task = {}) {
            this.globalStore?.showModal('task-modal', task)
        },
        loaderEvent() {
            // console.log(this.subscribable_id, this.subscribable_type)
            // console.log(typeof (this.subscribable_type) !== 'undefined' && typeof(this.subscribable_id) !== 'undefined')
            // if (typeof (this.subscribable_type) !== 'undefined' && typeof(this.subscribable_id) !== 'undefined') {
            //     this.url = '/tasksSubscriptions?subscribable_type=' + this.subscribable_type + '&subscribable_id=' + this.subscribable_id
            // } else {
            //     this.url = '/tasks/list?filter=' + this.filter;
            // }

            this.dt = $('#task-datatable_' + this.component_id).DataTable();
            this.dt.on('draw.dt', () => {
                this.tasks = this.dt.rows({page: 'current'}).data().toArray();

                $('#task-content').insertBefore('#task-datatable-wrapper');
            });

            this.$eventHub.on('filter', (filter) => {
                this.dt.search(filter).draw();
            });
        },
        confirmItemDelete(task) {
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
                    console.log(err);
                });
        },
        complete(task) {
            axios.patch('/tasks/' + task.id + '/complete')
                .then(res => {
                    task.subscriptions[0] = res.data;
                })
                .catch(err => {
                    console.log(err);
                });
        },
        isCompleted(task) {
            let subscription = task.subscriptions
            return ( subscription[0]?.completion_date != null && typeof (subscription[0]?.completion_date) !== 'undefined' ) ? true : false;
        },
    },
    components: {
        ConfirmModal,
        DataTable,
        IndexWidget,
        TaskModal,
    },
}
</script>