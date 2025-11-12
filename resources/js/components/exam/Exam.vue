<template>
    <div class="row">
        <div class="col-sm-12 py-4">
            <div
                id="exam-enrolled-user-datatable-wrapper"
                class="w-100 dataTablesWrapper"
            >
                <h5>{{ trans('global.exam.add_remove_users.students_exam_title') }} {{ exam.test_name }}</h5>
                <DataTable
                    id="exam-enrolled-user-datatable"
                    class="p-0"
                    :columns="columns"
                    :options="options"
                    :ajax="'/exams/' + exam.exam_id + '/list'"
                    :search="search"
                    width="100%"
                />
            </div>

            <button
                id="expelFromCurricula"
                type="button"
                name="expelFromCurricula"
                class="btn btn-primary pull-right mt-3"
                @click="expelFromExam()"
            >
                <i class="fa fa-minus mr-2"></i>
                {{ trans('global.exam.expel_user') }}
            </button>
            <button v-if="exam.status !== 0"
                :id="'edit-exam-' + exam.id"
                type="button"
                :name="'edit-exam-' + exam.id"
                class="btn btn-primary pull-left mt-3 mr-2"
                @click="getReport()"
            >
                <i class="fa fa-download mr-2"></i>
                {{ trans('global.exam.download_report') }}
            </button>
        </div>

        <hr style="width: 100%; border: 1px solid lightgray !important;">

        <div class="col-sm-12 pt-4">
            <div
                id="exam-expelled-user-datatable-wrapper"
                class="w-100 dataTablesWrapper"
            >
                <h5>{{ trans('global.exam.add_remove_users.users_group_title')}}</h5>
                <DataTable
                    id="exam-expelled-user-datatable"
                    class="p-0"
                    :columns="columns2"
                    :options="options"
                    :ajax="'/exams/' + exam.exam_id + '/users/list'"
                    :search="search"
                    width="100%"
                />
            </div>
            <button
                id="expelFromCurricula"
                type="button"
                name="expelFromCurricula"
                class="btn btn-primary pull-right mt-3"
                @click="enrolIntoExam()"
            >
                <i class="fa fa-plus mr-2"></i>
                {{ trans('global.exam.enrol_user') }}
            </button>
        </div>

        <Teleport to="body">
            <ExamModal
                :show="showExamModal"
                :params="currentExam"
                @close="this.showExamModal = false"
            />
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="confirmTitle"
                :description="confirmDescription"
                @close="() => {
                    this.showConfirm = false;
                }"
                @confirm="() => {
                    this.showConfirm = false;
                    this.confirmFunction;
                }"
            />
        </Teleport>
    </div>
</template>
<script>
import ExamModal from "../exam/ExamModal.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import 'datatables.net-select-bs5';
import {useDatatableStore} from "../../store/datatables.js";
import {useGlobalStore} from "../../store/global.js";
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import {useToast} from "vue-toastification";
import axios from "axios";
DataTable.use(DataTablesCore);

export default {
    name: "exam",
    components: {
        ConfirmModal,
        ExamModal,
        DataTable,
    },
    props: {
        exam: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            componentId: this.$.uid,
            showExamModal: false,
            currentExam: {},
            search: '',
            columns: [
                { title: window.trans.global.firstname, name: 'firstname', data: 'firstname' },
                { title: window.trans.global.lastname, name: 'lastname', data: 'lastname' },
                {
                    title: window.trans.global.exam.fields.status,
                    data: 'pivot.exam_started',
                    render: function (data, type, row) {
                        if (type === 'display') {
                            return row.pivot.exam_started && row.pivot.exam_completed_at
                                ? '<i class="fa-solid fa-circle" style="color: limegreen"></i>'
                                : row.pivot.exam_started
                                    ? '<i class="fa-solid fa-circle" style="color: orange"></i>'
                                    : '<i class="fa-solid fa-circle" style="color: red"></i>';
                        }
                        return data;
                    },
                },
                {
                    title: window.trans.global.exam.fields.completed_at,
                    data: 'pivot.exam_completed_at',
                    render: function (data, type, row) {
                        if (type === 'display') {
                            if (row.pivot.exam_completed_at) {
                                var myDate = new Date(row.pivot.exam_completed_at)
                                return myDate.toLocaleDateString('de');
                            }
                        }
                        return data;
                    }
                },
            ],
            columns2: [
                { title: window.trans.global.user.fields.username, data: 'username' },
                { title: window.trans.global.firstname, name: 'firstname', data: 'firstname' },
                { title: window.trans.global.lastname, name: 'lastname', data: 'lastname' },
                { title: window.trans.global.user.fields.email, data: 'email' },
            ],
            options : this.$dtOptions,
            dt: null,
            dt2: null,
            showConfirm: false,
            confirmTitle: '',
            confirmDescription: '',
            confirmFunction: null,
        }
    },
    setup() {
        const store = useDatatableStore();
        const globalStore = useGlobalStore();
        const toast = useToast();
        return {
            store,
            globalStore,
            toast
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.resetExamEnrolledUserDatatable();
        this.resetExamExpelledUserDatatable();

        this.loaderEvent();

        this.currentExam = this.exam;
        this.$eventHub.on('exam-updated', (exam) => {
            this.currentExam = exam;
            this.showExamModal = false;
        });
    },
    methods: {
        resetExamEnrolledUserDatatable() {
            this.store.addToDatatables(
                {
                    datatable: 'exam-enrolled-user-datatable',
                    select: (this.store.getDatatable('exam-enrolled-user-datatable')?.select) ? false : true,
                    selectedItems: [],
                }
            );
        },
        resetExamExpelledUserDatatable() {
            this.store.addToDatatables(
                {
                    datatable: 'exam-expelled-user-datatable',
                    select: (this.store.getDatatable('exam-expelled-user-datatable')?.select) ? false : true,
                    selectedItems: [],
                }
            );
        },
        loaderEvent() {
            this.dt = $('#exam-enrolled-user-datatable').DataTable();
            this.dt.order([ // name-attribute needs to be the same as in the columns-array
                { name: 'lastname', dir: 'asc' },
                { name: 'firstname', dir: 'asc' },
            ]);

            this.dt.on('select', (e, dt, type, indexes) => {
                let selection = this.dt.rows('.selected').data().toArray()

                selection.forEach(function (user) {
                    if (user.pivot.exam_started) {
                        dt.row(indexes[0]).deselect();
                        this.toast.error(window.trans.global.exam.error_messages.remove_users);
                        throw new Error('Students selected cannot be removed');
                    }
                }.bind(this))

                selection = this.dt.rows('.selected').data().toArray()
                this.store.setSelectedIds('exam-enrolled-user-datatable', selection);
            });
            this.dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.exams = this.dt.rows({page: 'current'}).data().toArray();
            });

            this.dt2 = $('#exam-expelled-user-datatable').DataTable();
            this.dt2.order([ // name-attribute needs to be the same as in the columns-array
                { name: 'lastname', dir: 'asc' },
                { name: 'firstname', dir: 'asc' },
            ]);

            this.dt2.on('select', () => {
                let selection = this.dt2.rows('.selected').data().toArray()

                this.store.setSelectedIds('exam-expelled-user-datatable', selection);
            });
            this.dt2.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.exams = this.dt2.rows({page: 'current'}).data().toArray();
            });
            this.$eventHub.on('filter', (filter) => {
                this.dt2.search(filter).draw();
            });
        },
        editExam() {
            this.showExamModal = true;
        },
        enrolIntoExam() {
            let ids = this.store.getSelectedValuesByField('exam-expelled-user-datatable', 'user_id');

            if (ids.length == 0) {
                this.toast.error(window.trans.global.datatables.zero_selected);
            } else {
                axios.post('/exams/' + this.exam.exam_id + '/users/enrol', {
                    tool: this.exam.tool,
                    enrollment_list: ids,
                    _method: 'POST',
                })
                .then(r => { location.reload(); })
                .catch(e => { console.log(e.response); });
            }
        },
        expelFromExam() {
            let ids = this.store.getSelectedIds('exam-enrolled-user-datatable');

            if (ids.length == 0) {
                this.toast.error(window.trans.global.datatables.zero_selected);
            } else {
                axios.delete('/exams/' + this.exam.exam_id + '/users/expel', {
                    data: {
                        tool: this.exam.tool,
                        expel_list: ids,
                    },
                })
                .then(r => { location.reload(); })
                .catch(e => { console.log(e.response); });
            }
        },
        getReport() {
            axios.post('/exams/' + this.exam.exam_id + '/report', {tool: this.exam.tool}, {responseType: 'arraybuffer'})
                .then(response => {
                    var blob = new Blob([response.data]);
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = this.exam.test_name+'.pdf';
                    link.click();
                })
                .catch(errors => {
                    console.log(errors)
                });
        },
    },
}
</script>