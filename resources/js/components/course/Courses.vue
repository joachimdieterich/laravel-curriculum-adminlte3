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
            <CourseModal
                :show="this.showCourseModal"
                @close="this.showCourseModal = false"
                :params="this.group"
            ></CourseModal>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.course.delete')"
                :description="trans('global.course.delete_helper')"
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
import IndexWidget from "../uiElements/IndexWidget";
import CourseModal from "./CourseModal";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal";
DataTable.use(DataTablesCore);

export default {
    props: {
        group: Object,
    },
    data() {
        return {
            component_id: this._uid,
            courses: null,
            search: '',
            showCourseModal: false,
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
            this.currentCourse = {};
            this.showCourseModal = true;
        });

        this.$eventHub.on('course-added', (course) => {
            this.showCourseModal = false;
            this.dt.ajax.reload();
        });

    },
    methods: {
        editCourse(course){
            this.currentCourse = course;
            this.showCourseModal = true;
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
