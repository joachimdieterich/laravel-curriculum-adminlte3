<template >
    <div class="row">
        <div
            id="grade-content"
            class="col-md-12 m-0"
        >
            <IndexWidget
                v-permission="'grade_create'"
                key="'gradeCreate'"
                modelName="Grade"
                url="/grades"
                :create=true
                :label="trans('global.grade.create')"
            />
            <IndexWidget v-for="grade in grades"
                :key="'gradeIndex' + grade.id"
                :model="grade"
                modelName="Grade"
                url="/grades"
            >
                <template v-slot:icon>
                    <i class="fas fa-layer-group"></i>
                </template>

                <template v-slot:dropdown
                    v-permission="'grade_edit, grade_delete'"
                >
                    <div
                        class="dropdown-menu dropdown-menu-right"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            v-permission="'grade_edit'"
                            :name="'edit-grade-' + grade.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editGrade(grade)"
                        >
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.grade.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'grade_delete'"
                            :id="'delete-grade-' + grade.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(grade)"
                        >
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.grade.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div
            id="grade-datatable-wrapper"
            class="w-100 dataTablesWrapper"
        >
            <DataTable
                id="grade-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display: none;"
            />
        </div>

        <Teleport to="body">
            <GradeModal/>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.grade.delete')"
                :description="trans('global.grade.delete_helper')"
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
import GradeModal from "../grade/GradeModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import {useGlobalStore} from "../../store/global";
import ConfirmModal from "../uiElements/ConfirmModal.vue";
DataTable.use(DataTablesCore);

export default {
    props: {},
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            grades: null,
            search: '',
            showConfirm: false,
            url: '/grades/list',
            errors: {},
            currentGrade: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
                { title: 'external_begin', data: 'external_begin'},
                { title: 'external_end', data: 'external_end', searchable: true},
                { title: 'organization_type', data: 'organization_type', searchable: true},
            ],
            options : this.$dtOptions,
            dt: null,
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('grade-added', (grade) => {
            this.grades.push(grade);
        });

        this.$eventHub.on('grade-updated', (updatedGrade) => {
            let grade = this.grades.find(g => g.id === updatedGrade.id);

            Object.assign(grade, updatedGrade);
        });

        this.$eventHub.on('filter', (filter) => {
            this.dt.search(filter).draw();
        });
    },
    methods: {
        editGrade(grade) {
            this.globalStore?.showModal('grade-modal', grade);
        },
        loaderEvent() {
            this.dt = $('#grade-datatable').DataTable();
            this.dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.grades = this.dt.rows({page: 'current'}).data().toArray();

                $('#grade-content').insertBefore('#grade-datatable-wrapper');
            });
        },
        confirmItemDelete(grade) {
            this.currentGrade = grade;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/grades/' + this.currentGrade.id)
                .then(r => {
                    let index = this.grades.indexOf(this.currentGrade);
                    this.grades.splice(index, 1);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
    },
    components: {
        ConfirmModal,
        DataTable,
        GradeModal,
        IndexWidget,
    },
}
</script>