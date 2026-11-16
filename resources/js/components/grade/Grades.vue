<template>
    <div class="d-flex flex-column">
        <div
            id="grade-content"
            class="px-3"
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
                <template #icon>
                    <i class="fa fa-layer-group"></i>
                </template>

                <template #dropdown
                    v-permission="'grade_edit, grade_delete'"
                >
                    <div class="dropdown-menu dropdown-menu-end">
                        <button
                            v-permission="'grade_edit'"
                            type="button"
                            class="dropdown-item"
                            @click="editGrade(grade)"
                        >
                            <i class="fa fa-pencil-alt"></i>
                            {{ trans('global.grade.edit') }}
                        </button>

                        <hr class="my-1">

                        <button
                            v-permission="'grade_delete'"
                            type="submit"
                            class="dropdown-item text-danger"
                            @click="confirmItemDelete(grade)"
                        >
                            <i class="fa fa-trash"></i>
                            {{ trans('global.grade.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <DataTable
            ref="datatable"
            id="grade-datatable"
            :columns="columns"
            :options="$dtOptions"
            ajax="/grades/list"
            class="d-none"
            @xhr="(e, settings, json) => grades = json.data"
        />

        <Teleport to="body">
            <GradeModal/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.grade.delete')"
                :description="trans('global.grade.delete_helper')"
                @close="showConfirm = false"
                @confirm="destroy()"
            />
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
    components: {
        ConfirmModal,
        DataTable,
        GradeModal,
        IndexWidget,
    },
    data() {
        return {
            component_id: this.$.uid,
            grades: null,
            showConfirm: false,
            currentGrade: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
            ],
            dt: null,
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.dt = this.$refs.datatable.dt;

        this.$eventHub.on('grade-added', grade => {
            this.grades.push(grade);
        });

        this.$eventHub.on('grade-updated', updatedGrade => {
            let grade = this.grades.find(g => g.id === updatedGrade.id);
            Object.assign(grade, updatedGrade);
        });

        this.$eventHub.on('filter', filter => {
            this.dt.search(filter.searchString).draw();
        });
    },
    methods: {
        editGrade(grade) {
            this.globalStore.showModal('grade-modal', grade);
        },
        confirmItemDelete(grade) {
            this.currentGrade = grade;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/grades/' + this.currentGrade.id)
                .then(r => {
                    this.showConfirm = false;
                    let index = this.grades.indexOf(this.currentGrade);
                    this.grades.splice(index, 1);
                })
                .catch(e => {
                    console.log(e);
                    this.showConfirm = false;
                    this.toast.error(this.errorMessage(e));
                });
        },
    },
}
</script>