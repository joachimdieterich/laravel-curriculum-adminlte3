<template >
    <div class="row">
        <div
            id="exam-content"
            class="col-md-12 m-0"
        >
            <ul v-if="subscribable"
                v-permission="'is_teacher'"
                class="nav nav-pills py-2"
                role="tablist"
            >
                <li class="nav-item">
                    <a
                        id="exam-filter-all"
                        class="nav-link pointer"
                        :class="filter === 'all' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('all')"
                    >
                        <i class="fas fa-th pr-2"></i>
                        {{ trans('global.all') }} {{ trans('global.exam.title') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a
                        id="custom-filter-by-student"
                        class="nav-link pointer"
                        :class="filter === 'student' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('student')"
                    >
                        <i class="fas fa-university pr-2"></i>
                        {{ trans('global.my') }} {{ trans('global.exam.title_singular') }}
                    </a>
                </li>
            </ul>

            <IndexWidget
                v-permission="'exam_create'"
                key="examCreate"
                modelName="Exam"
                url="/exams"
                :create="!subscribable"
                :subscribe="subscribable"
                :subscribable_id="subscribable_id"
                :subscribable_type="subscribable_type"
                :label="trans('global.exam.' + create_label_field)"
            >
                <template v-slot:itemIcon>
                    <i v-if="create_label_field == 'enrol'"
                       class="fa fa-2x fa-link text-muted"
                    ></i>
                </template>
            </IndexWidget>
            <IndexWidget v-for="exam in exams"
                :key="'examIndex' + exam.id"
                :model="exam"
                titleField="test_name"
                descriptionField="subject"
                modelName="Exam"
                :url="getLoginUrl(exam)"
                :urlOnly="true"
                urlTarget="_blank"
                :active="isActive(exam)"
                info_deactivated="Test wurde bereits abgeschlossen."
                :showSubscribable="subscribable"
            >
                <template v-slot:icon>
                    <i class="fa-solid fa-ranking-star pt-2"></i>
                </template>

                <template v-slot:owner>
                    <div v-if="!isActive(exam)"
                        style="position: absolute; top: 100px; left: 0;"
                        class="badge-primary px-2"
                    >
                        <i class="fa fa-calendar-check"></i>
                        {{ exam.pivot.exam_completed_at }}
                    </div>
                </template>

                <template v-if="subscribable"
                    v-slot:dropdown
                >
                    <div
                        class="dropdown-menu dropdown-menu-right"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            class="dropdown-item text-secondary"
                            @click="openExam(exam)"
                        >
                            <i class="fa fa-ranking-star mr-2"></i>
                            <span>Zur &Uuml;bersicht</span>
                        </button>
                        <!-- <button
                            v-permission="'exam_edit'"
                            :name="'edit-exam-' + exam.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editExam(exam)"
                        >
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.exam.edit') }}
                        </button> -->
                        <button v-if="exam.status !== 0"
                            :name="'edit-exam-' + exam.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="getReport(exam)"
                        >
                            <i class="fa fa-download mr-2"></i>
                            {{ trans('global.exam.download_report') }}
                        </button>
                        <hr v-permission="'exam_delete'" class="my-1">
                        <button
                            v-permission="'exam_delete'"
                            :id="'delete-exam-' + exam.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(exam)"
                        >
                            <span v-if="create_label_field == 'enrol'">
                                <i class="fa fa-unlink mr-2"></i>
                                {{ trans('global.kanban.expel') }}
                            </span>
                            <span v-else>
                                <i class="fa fa-trash mr-2"></i>
                                {{ trans('global.kanban.delete') }}
                            </span>
                        </button>
                    </div>
                </template>
                <template v-slot:content>
                    <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                        <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                            {{ exam.test_name }}
                        </h1>
                        <p class="text-muted small">
                            {{ exam.group?.title }}<br/>
                            {{ exam.group?.organization?.title }}
                        </p>
                        <progress
                            id="status"
                            :value="exam.status"
                            max="100"
                        >
                           {{ exam.status }}%
                        </progress>
                    </span>
                </template>
            </IndexWidget>
        </div>
        <div
            id="exam-datatable-wrapper"
            class="w-100 dataTablesWrapper"
        >
            <DataTable
                id="exam-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display: none;"
            />
        </div>

        <Teleport to="body">
            <SubscribeExamModal v-if="subscribable && this.checkPermission('is_teacher')"/>
            <ExamModal v-if="!subscribable"
                :show="showExamModal"
                @close="this.showExamModal = false"
                :params="currentExam"
            />
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.exam.delete')"
                :description="trans('global.exam.delete_helper')"
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
import ExamModal from "../exam/ExamModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import SubscribeExamModal from "../exam/SubscribeExamModal.vue";
import {useGlobalStore} from "../../store/global.js";
import {useToast} from "vue-toastification";
DataTable.use(DataTablesCore);

export default {
    props: {
        subscribable: {
            type: Boolean,
            default: false,
        },
        create_label_field: {
            type: String,
            default: 'create',
        },
        delete_label_field: {
            type: String,
            default: 'delete',
        },
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
        const toast = useToast();
        const globalStore = useGlobalStore();
        return {
            globalStore,
            toast
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            exams: null,
            search: '',
            showExamModal: false,
            showConfirm: false,
            errors: {},
            currentExam: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'test_name', data: 'test_name', searchable: true },
                { title: 'subject', data: 'subject', searchable: true },
            ],
            options : this.$dtOptions,
            filter: this.checkPermission('is_teacher') ? 'all' : 'student', // 'student' => only returns entries if user has student-role
            dt: null,
            url: this.urlOnLoad(),
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('exam-added', (exam) => {
            this.exams.push(exam);
        });

        this.$eventHub.on('exam-updated', (updatedExam) => {
            let exam = this.exams.find(e => e.id === exam.id);

            Object.assign(exam, updatedExam);
        });

        this.$eventHub.on('exam-subscription-added', () => {
            this.loaderEvent();
        });

        this.$eventHub.on('filter', (filter) => {
            this.dt.search(filter).draw();
        });
    },
    methods: {
        urlOnLoad() {
            let url = '/exams/list';
            
            if (this.subscribable_id) {
                let filter = this.checkPermission('is_teacher') ? 'all' : 'student'; // this.filter isn't defined at this point
                url += '?group_id=' + this.subscribable_id + '&filter=' + filter;
            }
    
            return url;
        },
        getLoginUrl(exam) {
            return exam.login_url ?? '/exams/' +  exam.exam_id + '/edit';
        },
        openExam(exam) {
            window.open('/exams/' + exam.exam_id + '/edit', '_blank');
        },
        editExam(exam) {
            this.currentExam = exam;
            this.showExamModal = true;
        },
        loaderEvent() {
            this.dt = $('#exam-datatable').DataTable();
            this.dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.exams = this.dt.rows({page: 'current'}).data().toArray();

                $('#exam-content').insertBefore('#exam-datatable-wrapper');
            });
        },
        confirmItemDelete(exam) {
            this.currentExam = exam;
            this.showConfirm = true;
        },
        destroy() {
            if (this.subscribable) {
                axios.delete('/exams/' + this.currentExam.exam_id + '?tool=' + this.currentExam.tool)
                    .then(response => {
                        if (response.status === 200) {
                            this.exams.splice(this.exams.indexOf(this.currentExam), 1);
                            this.toast.success(this.trans('global.exam.success_messages.exam_removed'));
                        }
                    })
                    .catch(errors => {
                        console.log(errors);
                        this.toast.error(this.trans('global.exam.error_messages.remove_exam'));
                    })
            } else {
                axios.delete('/exams/' + this.currentExam.id)
                    .then(res => {
                        let index = this.exams.indexOf(this.currentExam);
                        this.exams.splice(index, 1);
                    })
                    .catch(err => {
                        console.log(err.response);
                    });
            }
        },
        isActive(completed) {
            return !completed.pivot?.exam_completed_at;
        },
        setFilter(filter) {
            this.url = (this.subscribable_id) ? '/exams/list?group_id=' + this.subscribable_id + '&filter=' + filter : '/exams/list';
            this.dt.ajax.url(this.url).load();
        },
        getReport(exam) {
            axios.post('/exams/' + exam.exam_id + '/report', {tool: exam.tool}, {responseType: 'arraybuffer'})
                .then(response => {
                    var blob = new Blob([response.data]);
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = exam.test_name + '.pdf';
                    link.click();
                })
                .catch(errors => {
                    console.log(errors)
                })
        },
    },
    components: {
        SubscribeExamModal,
        ConfirmModal,
        DataTable,
        ExamModal,
        IndexWidget,
    },
}
</script>