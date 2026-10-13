<template>
    <div class="d-flex flex-column">
        <div class="p-3">
            <div>
                <h5>{{ trans('global.exam.add_remove_users.students_exam_title') }} {{ exam.test_name }}</h5>
                <DataTable
                    ref="enrolled-datatable"
                    id="exam-enrolled-user-datatable"
                    :columns="columns"
                    :options="$dtOptions"
                    :ajax="'/exams/' + exam.exam_id + '/list'"
                />
            </div>

            <button
                type="button"
                class="btn btn-primary float-end mt-3"
                @click="expelFromExam()"
            >
                <i class="fa fa-minus me-2"></i>
                {{ trans('global.exam.expel_user') }}
            </button>
            <button v-if="exam.status !== 0"
                type="button"
                class="btn btn-primary float-start mt-3 me-2"
                @click="getReport()"
            >
                <i class="fa fa-download me-2"></i>
                {{ trans('global.exam.download_report') }}
            </button>
        </div>

        <hr/>

        <div class="py-3">
            <div>
                <h5>{{ trans('global.exam.add_remove_users.users_group_title')}}</h5>
                <DataTable
                    ref="expelled-datatable"
                    id="exam-expelled-user-datatable"
                    :columns="columns2"
                    :options="$dtOptions"
                    :ajax="'/exams/' + exam.exam_id + '/users/list'"
                />
            </div>
            <button
                type="button"
                class="btn btn-primary float-end mt-3 me-3"
                @click="enrolIntoExam()"
            >
                <i class="fa fa-plus me-2"></i>
                {{ trans('global.exam.enrol_user') }}
            </button>
        </div>
    </div>
</template>
<script>
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import 'datatables.net-select-bs5';
import {useDatatableStore} from "../../store/datatables.js";
DataTable.use(DataTablesCore);

export default {
    name: "Exam",
    components: { DataTable },
    props: {
        exam: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            componentId: this.$.uid,
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
            dtEnrolled: null,
            dtExpelled: null,
        }
    },
    setup() {
        return { store: useDatatableStore() }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.resetExamEnrolledUserDatatable();
        this.resetExamExpelledUserDatatable();

        this.dtEnrolled = this.$refs['enrolled-datatable'].dt;
        this.dtExpelled = this.$refs['expelled-datatable'].dt;
        this.dtEvents();

        this.$eventHub.on('filter', filter => this.dtExpelled.search(filter.searchString).draw());
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
        dtEvents() {
            this.dtEnrolled.order([ // name-attribute needs to be the same as in the columns-array
                { name: 'lastname', dir: 'asc' },
                { name: 'firstname', dir: 'asc' },
            ]);

            this.dtEnrolled.on('select', (e, dt, type, indexes) => {
                let selection = this.dtEnrolled.rows('.selected').data().toArray()

                selection.forEach(function (user) {
                    if (user.pivot.exam_started) {
                        dt.row(indexes[0]).deselect();
                        this.toast.error(window.trans.global.exam.error_messages.remove_users);
                        throw new Error('Students selected cannot be removed');
                    }
                }.bind(this))

                selection = this.dtEnrolled.rows('.selected').data().toArray()
                this.store.setSelectedIds('exam-enrolled-user-datatable', selection);
            });

            this.dtExpelled.order([ // name-attribute needs to be the same as in the columns-array
                { name: 'lastname', dir: 'asc' },
                { name: 'firstname', dir: 'asc' },
            ]);

            this.dtExpelled.on('select', () => {
                let selection = this.dtExpelled.rows('.selected').data().toArray()

                this.store.setSelectedIds('exam-expelled-user-datatable', selection);
            });
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