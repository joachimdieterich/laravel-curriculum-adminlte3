<template >
    <div class="row">
        <div id="course-content"
             class="col-md-12 m-0">
            <IndexWidget
                v-permission="'group_enrolment'"
                key="'courseCreate'"
                modelName="Course"
                url="/courses"
                :create=true
                :createLabel="trans('global.course.create')">
                <template v-slot:itemIcon>
                    <i v-if="create_label_field == 'enrol'"
                       class="fa fa-2x p-5 fa-link nav-item-text text-muted"></i>
                    <i v-else
                       class="fa fa-2x p-5 fa-plus nav-item-text text-muted"></i>
                </template>
            </IndexWidget>
            <IndexWidget
                v-for="course in courses"
                :key="'courseIndex'+course.id"
                :model="course"
                modelName= "course"
                url="/courses">
                <template v-slot:icon>
                    <i class="fas fa-layer-course pt-2"></i>
                </template>

                <template
                    v-permission="'course_delete'"
                    v-slot:dropdown>
                    <div class="dropdown-menu dropdown-menu-right"
                         style="z-index: 1050;"
                         x-placement="left-start">
                        <button
                            v-permission="'course_delete'"
                            :id="'delete-course-' + course.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(course)">
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.course.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div id="course-datatable-wrapper"
             class="w-100 dataTablesWrapper">
            <DataTable
                id="course-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display:none; "
            ></DataTable>
        </div>

        <Teleport to="body">
            <CourseModal></CourseModal>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.course.delete')"
                :description="trans('global.course.delete_helper')"
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
import IndexWidget from "../uiElements/IndexWidget.vue";
import CourseModal from "./CourseModal.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import {useGlobalStore} from "../../store/global";
DataTable.use(DataTablesCore);

export default {
    props: {
        group: Object,
        create_label_field: {
            type: String,
            default: 'create'
        },
        delete_label_field: {
            type: String,
            default: 'delete'
        },
    },
    setup () {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            courses: null,
            search: '',
            showConfirm: false,
            url: '/courses/?group_id=' + this.group.id,
            errors: {},
            currentCourse: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
                { title: 'description', data: 'description', searchable: true},
                { title: 'medium_id', data: 'medium_id',},

            ],
            options : this.$dtOptions,
            modalMode: 'edit',
            dt: $('#course-datatable').DataTable()
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('createCourse', () => {
            this.globalStore?.showModal('course-modal', {});
        });

        this.$eventHub.on('course-added', (course) => {
            this.globalStore?.closeModal('course-modal');
            this.dt.ajax.reload();
        });

    },
    methods: {
        editCourse(course){ //todo: Not used?
            this.globalStore?.showModal('course-modal', course);
        },
        loaderEvent(){
            this.dt = $('#course-datatable').DataTable();
            this.dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.courses = this.dt.rows({page: 'current'}).data().toArray();

                $('#course-content').insertBefore('#course-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                this.dt.search(filter).draw();
            });
        },
        confirmItemDelete(course){
            this.currentCourse = course;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/curricula/expel',{
                    data :{
                        'expel_list' : {
                            0: {
                                'group_id' : this.group.id,
                                'curriculum_id': {
                                    0 : this.currentCourse.curriculum.id
                                }
                            }
                        }
                    }
                } )
                .then(res => {
                    let index = this.courses.indexOf(this.currentCourse);
                    this.courses.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        update(course) {
            const index = this.courses.findIndex(
                vc => vc.id === course.id
            );

            for (const [key, value] of Object.entries(course)) {
                this.courses[index][key] = value;
            }
        }
    },
    components: {
        ConfirmModal,
        CourseModal,
        DataTable,
        IndexWidget
    },
}
</script>
