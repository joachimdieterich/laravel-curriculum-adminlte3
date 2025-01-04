<template >
    <div class="row">
        <div id="exam-content"
             class="col-md-12 m-0">
            <ul v-if="subscribable"
                v-permission="'exam_edit'"
                class="nav nav-pills py-2" role="tablist">
                <li class="nav-item">
                    <a class="nav-link "
                       :class="filter === 'all' ? 'active' : ''"
                       id="curriculum-filter-all"
                       @click="setFilter('all')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fas fa-th pr-2"></i>
                        {{ trans('global.all') }} {{ trans('global.exam.title') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'student' ? 'active' : ''"
                       id="custom-filter-by-student"
                       @click="setFilter('student')"
                       data-toggle="pill"
                       role="tab"
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
                :create=true
                :createLabel="trans('global.exam.' + create_label_field)">
                <template v-slot:itemIcon>
                    <i v-if="create_label_field == 'enrol'"
                       class="fa fa-2x fa-link text-muted"
                    ></i>
                </template>
            </IndexWidget>
            <IndexWidget
                v-for="exam in exams"
                :key="'examIndex'+exam.id"
                :model="exam"
                titleField="test_name"
                descriptionField="subject"
                modelName= "exam"
                :url="getLoginUrl(exam)"
                url-only="true"
                urlTarget="_blank"
                :active="isActive(exam)"
                info_deactivated="Test wurde bereits abgeschlossen."
            >
                <template v-slot:icon>
                    <i class="fa-solid fa-ranking-star pt-2"></i>
                </template>

                <template v-slot:owner>
                    <div v-if="!isActive(exam)"
                        style="position:absolute; top:100px; left: 0;"
                         class="badge-primary px-2">
                        <i class="fa fa-calendar-check"></i>
                        {{ exam.pivot.exam_completed_at }}
                    </div>
                </template>

                <template
                    v-if="subscribable"
                    v-permission="'exam_edit, exam_delete'"
                    v-slot:dropdown>
                    <div class="dropdown-menu dropdown-menu-right"
                         style="z-index: 1050;"
                         x-placement="left-start">
                        <button
                            v-permission="'exam_edit'"
                            :name="'edit-exam-' + exam.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editExam(exam)">
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.exam.edit') }}
                        </button>
                        <button
                            v-permission="'exam_edit'"
                            v-if="exam.status !== 0"
                            :name="'edit-exam-' + exam.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="getReport(exam)">
                            <i class="fa fa-download mr-2"></i>
                            {{ trans('global.exam.download_report') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'exam_delete'"
                            :id="'delete-exam-' + exam.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(exam)">
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
                           {{ exam.group?.title }}
                       </p>
                        <progress
                            id="status"
                            :value="exam.status"
                            max="100">
                           {{ exam.status }}%
                       </progress>

                    </span>
                </template>
            </IndexWidget>
        </div>
        <div id="exam-datatable-wrapper"
             class="w-100 dataTablesWrapper">
            <DataTable
                id="exam-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display:none; "
            ></DataTable>
        </div>

        <Teleport to="body">
            <SubscribeExamModal
                v-if="subscribable">
            </SubscribeExamModal>
            <ExamModal
                v-if="!subscribable"
                :show="this.showExamModal"
                @close="this.showExamModal = false"
                :params="currentExam"
            ></ExamModal>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.exam.delete')"
                :description="trans('global.exam.delete_helper')"
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
            default: false
        },
        create_label_field: {
            type: String,
            default: 'create'
        },
        delete_label_field: {
            type: String,
            default: 'delete'
        },
        subscribable_type: '',
        subscribable_id: '',
    },
    setup () {
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
                { title: 'test_name', data: 'test_name', searchable: true},
                { title: 'subject', data: 'subject', searchable: true},
            ],
            options : this.$dtOptions,
            modalMode: 'edit',
            filter: 'student',
            dt: null,
            url: (this.subscribable_id) ? '/exams/list?group_id=' + this.subscribable_id +'&filter=student' : '/exams/list',
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('exam-added', (exam) => {
            if (!this.subscribable) {
                this.globalStore?.closeModal('exam-modal');
            } else {
                this.globalStore?.closeModal('subscribe-exam-modal');
            }
            this.exams.push(exam);
        });

        this.$eventHub.on('exam-updated', (exam) => {
            this.globalStore?.closeModal('exam-modal');
            this.update(exam);
        });

        this.$eventHub.on('exam-subscription-added', () => {
            this.globalStore?.closeModal('subscribe-exam-modal');
            this.loaderEvent();
        });

        this.$eventHub.on('createExam', () => {
            if (!this.subscribable) {
                this.globalStore?.showModal('exam-modal', {});
            } else {
                this.globalStore?.showModal('subscribe-exam-modal', {
                    'reference': {},
                    'subscribable_type': this.subscribable_type,
                    'subscribable_id': this.subscribable_id,
                });
            }

        });
    },
    methods: {
        getLoginUrl(exam){
            return exam.login_url ?? '/exams/' +  exam.exam_id + '/edit';
        },
        editExam(exam){
            this.currentExam = exam;
            this.showExamModal = true;
        },
        loaderEvent(){
            this.dt = $('#exam-datatable').DataTable();
            this.dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.exams = this.dt.rows({page: 'current'}).data().toArray();

                $('#exam-content').insertBefore('#exam-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                this.dt.search(filter).draw();
            });
        },
        confirmItemDelete(exam){
            this.currentExam = exam;
            this.showConfirm = true;
        },
        destroy() {
            if (this.subscribable === true)
            {
                axios.delete('/exams/' + this.currentExam.exam_id + '?tool=' + this.currentExam.tool)
                    .then(response => {
                    if (response.status === 200) {
                        //Vue.delete(this.data.data, key);
                        this.toast.success(Vue.prototype.trans('global.exam.success_messages.exam_removed'))
                    }
                })
                .catch(errors => {
                    var message = errors.response.status === 403 ? Vue.prototype.trans('global.exam.error_messages.remove_exam') : errors.message
                    this.toast.error(message);
                })

               /* axios.post('/examSubscriptions/expel', {
                    'model_id' : this.currentExam.id,
                    'subscribable_type' : this.subscribable_type,
                    'subscribable_id' : this.subscribable_id,
                })
                    .then(r => {
                        let index = this.exams.indexOf(this.currentExam);
                        this.exams.splice(index, 1);
                    })
                    .catch(e => {
                        console.log(e);
                    });*/
            }
            else
            {
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
        update(exam) {
            const index = this.exams.findIndex(
                vc => vc.id === exam.id
            );

            for (const [key, value] of Object.entries(exam)) {
                this.exams[index][key] = value;
            }
        },
        isActive(completed){
            //console.log(completed);

            if (!completed.pivot?.exam_completed_at){
                return true;
            } else {
                return false;
            }
        },
        setFilter(filter){
            this.url =  (this.subscribable_id) ? '/exams/list?group_id=' + this.subscribable_id +'&filter=' + filter: '/exams/list';
            this.dt.ajax.url(this.url).load();
        },
        getReport(exam) {
            axios.post('/exams/' + exam.exam_id + '/report', {tool: exam.tool}, {responseType: 'arraybuffer'})
                .then(response => {
                    var blob = new Blob([response.data]);
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = exam.test_name+'.pdf';
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
        IndexWidget
    },
}
</script>
