<template >
    <div class="d-flex flex-column">
        <div
            id="exam-content"
            class="px-3 m-0"
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
                        <i class="fas fa-th pe-2"></i>
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
                        <i class="fas fa-university pe-2"></i>
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
                :label="trans('global.exam.enrol')"
            >
                <template #itemIcon>
                    <i class="fa fa-2x fa-link text-muted"></i>
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
                <template #icon>
                    <i class="fa fa-ranking-star pt-2"></i>
                </template>

                <template #owner>
                    <div v-if="!isActive(exam)"
                        class="position-absolute badge-primary px-2"
                        style="top: 100px; left: 0;"
                    >
                        <i class="fa fa-calendar-check"></i>
                        {{ exam.pivot.exam_completed_at }}
                    </div>
                </template>

                <template #dropdown>
                    <div class="dropdown-menu dropdown-menu-end">
                        <button
                            type="button"
                            class="dropdown-item"
                            @click="openExam(exam)"
                        >
                            <i class="fa fa-ranking-star"></i>
                            <span>Zur &Uuml;bersicht</span>
                        </button>
                        <button v-if="exam.status !== 0"
                            type="button"
                            class="dropdown-item"
                            @click="getReport(exam)"
                        >
                            <i class="fa fa-download"></i>
                            {{ trans('global.exam.download_report') }}
                        </button>

                        <hr v-permission="'exam_delete'" class="my-1">

                        <button
                            v-permission="'exam_delete'"
                            type="submit"
                            class="dropdown-item text-danger"
                            @click="confirmItemDelete(exam)"
                        >
                            <i class="fa fa-unlink"></i>{{ trans('global.expel') }}
                        </button>
                    </div>
                </template>
                <template #content>
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

        <DataTable
            ref="datatable"
            id="exam-datatable"
            :columns="columns"
            :options="options"
            :ajax="url"
            class="d-none"
            @xhr="(e, settings, json) => exams = json.data"
        />

        <Teleport to="body">
            <SubscribeExamModal v-if="subscribable && this.checkPermission('is_teacher')"
                :group_id="subscribable_id"
            />
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.exam.delete')"
                :description="trans('global.exam.delete_helper')"
                @close="showConfirm = false"
                @confirm="destroy()"
            />
        </Teleport>
    </div>
</template>
<script>
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import SubscribeExamModal from "../exam/SubscribeExamModal.vue";
DataTable.use(DataTablesCore);

export default {
    components: {
        SubscribeExamModal,
        ConfirmModal,
        DataTable,
        IndexWidget,
    },
    props: {
        subscribable: {
            type: Boolean,
            default: false,
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
    data() {
        return {
            component_id: this.$.uid,
            exams: null,
            showConfirm: false,
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
        this.globalStore['showSearchbar'] = true;

        this.dt = this.$refs.datatable.dt;

        this.$eventHub.on('exam-added', exam => {
            this.exams.push(exam);
        });

        this.$eventHub.on('filter', filter => {
            this.dt.search(filter.searchString).draw();
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
        confirmItemDelete(exam) {
            this.currentExam = exam;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/exams/' + this.currentExam.exam_id + '?tool=' + this.currentExam.tool)
                .then(response => {
                    this.showConfirm = false;
                    this.exams.splice(this.exams.indexOf(this.currentExam), 1);
                })
                .catch(e => {
                    console.log(e);
                    this.showConfirm = false;
                    this.toast.error(this.errorMessage(e, 'global.exam.error_messages.remove_exam'));
                })
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
                .catch(e => {
                    console.log(e)
                })
        },
    },
}
</script>