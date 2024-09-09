<template >
    <div class="row">
        <div id="exam-content"
             class="col-md-12 m-0">
            <IndexWidget
                v-permission="'exam_create'"
                key="'examCreate'"
                modelName="Exam"
                url="/exams"
                :create=true
                :createLabel="trans('global.exam.create')">
            </IndexWidget>
            <IndexWidget
                v-for="exam in exams"
                :key="'examIndex'+exam.id"
                :model="exam"
                titleField="test_name"
                descriptionField="subject"
                modelName= "exam"
                url="/exams">
                <template v-slot:icon>
                    <i class="fa-solid fa-ranking-star pt-2"></i>
                </template>

                <template
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
                        <hr class="my-1">
                        <button
                            v-permission="'exam_delete'"
                            :id="'delete-exam-' + exam.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(exam)">
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.exam.delete') }}
                        </button>
                    </div>
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
            <ExamModal
                :show="this.showExamModal"
                @close="this.showExamModal = false"
                :params="currentExam"
            ></ExamModal>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.exam.delete')"
                :description="trans('global.exam.delete_helper')"
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
import ExamModal from "../exam/ExamModal";
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
            component_id: this.$.uid,
            exams: null,
            search: '',
            showExamModal: false,
            showConfirm: false,
            url: '/exams/list',
            errors: {},
            currentExam: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'test_name', data: 'test_name', searchable: true},
                { title: 'subject', data: 'subject', searchable: true},
            ],
            options : this.$dtOptions,
            modalMode: 'edit'
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('exam-added', (exam) => {
            this.showExamModal = false;
            this.exams.push(exam);
        });

        this.$eventHub.on('exam-updated', (exam) => {
            this.showExamModal = false;
            this.update(exam);
        });
        this.$eventHub.on('createExam', () => {
            this.currentExam = {};
            this.showExamModal = true;
        });
    },
    methods: {
        editExam(exam){
            this.currentExam = exam;
            this.showExamModal = true;
        },
        loaderEvent(){
            const dt = $('#exam-datatable').DataTable();
            dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.exams = dt.rows({page: 'current'}).data().toArray();

                $('#exam-content').insertBefore('#exam-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                dt.search(filter).draw();
            });
        },
        confirmItemDelete(exam){
            this.currentExam = exam;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/exams/' + this.currentExam.id)
                .then(res => {
                    let index = this.exams.indexOf(this.currentExam);
                    this.exams.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        update(exam) {
            const index = this.exams.findIndex(
                vc => vc.id === exam.id
            );

            for (const [key, value] of Object.entries(exam)) {
                this.exams[index][key] = value;
            }
        }
    },
    components: {
        ConfirmModal,
        DataTable,
        ExamModal,
        IndexWidget
    },
}
</script>
