<template >
    <div class="row">
        <div
            id="course-content"
            class="col-md-12 m-0"
        >
            <IndexWidget
                v-permission="'group_enrolment'"
                key="courseCreate"
                modelName="Course"
                url="/courses"
                :subscribe="true"
                :subscribable_id="group.id"
                :subscribable_type="'App\\Group'"
                :label="trans('global.course.' + create_label_field)"
            >
                <template v-slot:itemIcon>
                    <i v-if="create_label_field == 'enrol'"
                        class="fa fa-2x fa-link text-muted"
                    ></i>
                </template>
            </IndexWidget>
            <IndexWidget v-for="course in courses"
                :key="'courseIndex' + course.id"
                :model="course"
                modelName="Course"
                url="/courses"
            >
                <template v-slot:icon>
                    <i v-if="course.type_id === 1"
                        class="fas fa-globe pt-2"
                    ></i>
                    <i v-else-if="course.type_id === 2"
                        class="fas fa-university pt-2"
                    ></i>
                    <i v-else-if="course.type_id === 3"
                        class="fa fa-users pt-2"
                    ></i>
                    <i v-else
                        class="fa fa-user pt-2"
                    ></i>
                </template>

                <template v-slot:dropdown
                    v-permission="'course_delete'"
                >
                    <div
                        class="dropdown-menu dropdown-menu-right"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            v-permission="'course_delete'"
                            :id="'delete-course-' + course.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(course)"
                        >
                            <span>
                                <i class="fa fa-unlink mr-2"></i>
                                {{ trans('global.course.expel') }}
                            </span>
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div
            id="course-datatable-wrapper"
            class="w-100 dataTablesWrapper"
        >
            <DataTable
                id="course-datatable"
                :columns="columns"
                :options="options"
                :ajax="'/courses/?group_id=' + group.id"
                :search="search"
                width="100%"
                style="display: none;"
            />
        </div>

        <Teleport to="body">
            <SubscribeCourseModal/>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.course.expel')"
                :description="trans('global.course.expel_helper')"
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
import IndexWidget from "../uiElements/IndexWidget.vue";
import SubscribeCourseModal from "./SubscribeCourseModal.vue";
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
            default: 'create',
        },
        delete_label_field: {
            type: String,
            default: 'delete',
        },
    },
    setup() {
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
            errors: {},
            currentCourse: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
                { title: 'description', data: 'description', searchable: true },
            ],
            options : this.$dtOptions,
            dt: null,
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('course-added', (courses) => {
            Object.assign(this.courses, courses);
        });

        this.$eventHub.on('filter', (filter) => {
            this.dt.search(filter).draw();
        });
    },
    methods: {
        loaderEvent() {
            this.dt = $('#course-datatable').DataTable();
            this.dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.courses = this.dt.rows({page: 'current'}).data().toArray();

                $('#course-content').insertBefore('#course-datatable-wrapper');
            });
        },
        confirmItemDelete(course){
            this.currentCourse = course;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/curricula/expel', {
                data: {
                    expel_list: {
                        0: {
                            group_id : this.group.id,
                            curriculum_id: {
                                0: this.currentCourse.id,
                            },
                        },
                    },
                },
            })
            .then(res => {
                let index = this.courses.indexOf(this.currentCourse);
                this.courses.splice(index, 1);
            })
            .catch(err => {
                console.log(err.response);
            });
        },
    },
    components: {
        ConfirmModal,
        SubscribeCourseModal,
        DataTable,
        IndexWidget,
    },
}
</script>