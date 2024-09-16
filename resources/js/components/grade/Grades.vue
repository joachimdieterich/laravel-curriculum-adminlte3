<template >
    <div class="row">
        <div id="grade-content"
             class="col-md-12 m-0">
            <IndexWidget
                v-permission="'grade_create'"
                key="'gradeCreate'"
                modelName="Grade"
                url="/grades"
                :create=true
                :createLabel="trans('global.grade.create')">
            </IndexWidget>
            <IndexWidget
                v-for="grade in grades"
                :key="'gradeIndex'+grade.id"
                :model="grade"
                modelName= "grade"
                url="/grades">
                <template v-slot:icon>
                    <i class="fas fa-layer-group pt-2"></i>
                </template>

                <template
                    v-permission="'grade_edit, grade_delete'"
                    v-slot:dropdown>
                    <div class="dropdown-menu dropdown-menu-right"
                         style="z-index: 1050;"
                         x-placement="left-start">
                        <button
                            v-permission="'grade_edit'"
                            :name="'edit-grade-' + grade.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editGrade(grade)">
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.grade.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'grade_delete'"
                            :id="'delete-grade-' + grade.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(grade)">
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.grade.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div id="grade-datatable-wrapper"
             class="w-100 dataTablesWrapper">
            <DataTable
                id="grade-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display:none; "
            ></DataTable>
        </div>

        <Teleport to="body">
            <GradeModal
                :show="this.showGradeModal"
                @close="this.showGradeModal = false"
                :params="currentGrade"
            ></GradeModal>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.grade.delete')"
                :description="trans('global.grade.delete_helper')"
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
import GradeModal from "../grade/GradeModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
DataTable.use(DataTablesCore);


export default {
    props: {

    },
    data() {
        return {
            component_id: this.$.uid,
            grades: null,
            search: '',
            showGradeModal: false,
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
            modalMode: 'edit'
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('grade-added', (grade) => {
            this.showGradeModal = false;
            this.grades.push(grade);
        });

        this.$eventHub.on('grade-updated', (grade) => {
            this.showGradeModal = false;
            this.update(grade);
        });
        this.$eventHub.on('createGrade', () => {
            this.currentGrade = {};
            this.showGradeModal = true;
        });
    },
    methods: {
        editGrade(grade){
            this.currentGrade = grade;
            this.showGradeModal = true;
        },
        loaderEvent(){
            const dt = $('#grade-datatable').DataTable();
            dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.grades = dt.rows({page: 'current'}).data().toArray();

                $('#grade-content').insertBefore('#grade-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                dt.search(filter).draw();
            });
        },
        confirmItemDelete(grade){
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
        update(grade) {
            const index = this.grades.findIndex(
                vc => vc.id === grade.id
            );

            for (const [key, value] of Object.entries(grade)) {
                this.grades[index][key] = value;
            }
        }
    },
    components: {
        ConfirmModal,
        DataTable,
        GradeModal,
        IndexWidget
    },
}
</script>
