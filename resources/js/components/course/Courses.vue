<template >
    <div class="d-flex flex-column">
        <div
            id="course-content"
            class="px-3"
        >
            <IndexWidget
                v-permission="'group_enrolment'"
                key="courseCreate"
                modelName="Course"
                url="/courses"
                :subscribe="true"
                :subscribable_id="group.id"
                :subscribable_type="'App\\Group'"
                :label="trans('global.course.enrol')"
            >
                <template #itemIcon>
                    <i class="fa fa-2x fa-link text-muted"></i>
                </template>
            </IndexWidget>
            <IndexWidget v-for="course in courses"
                :key="'courseIndex' + course.id"
                :model="course"
                modelName="Course"
                url="/courses"
                :showSubscribable="true"
            >
                <template #icon>
                    <i v-if="course.type_id === 1"
                        class="fa fa-globe pt-2"
                    ></i>
                    <i v-else-if="course.type_id === 2"
                        class="fa fa-university pt-2"
                    ></i>
                    <i v-else-if="course.type_id === 3"
                        class="fa fa-users pt-2"
                    ></i>
                    <i v-else
                        class="fa fa-user pt-2"
                    ></i>
                </template>

                <template #dropdown
                    v-permission="'course_delete'"
                >
                    <div class="dropdown-menu dropdown-menu-end">
                        <button
                            v-permission="'course_delete'"
                            type="submit"
                            class="dropdown-item text-danger"
                            @click="confirmItemDelete(course)"
                        >
                            <i class="fa fa-unlink"></i>
                            {{ trans('global.course.expel') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <DataTable
            ref="datatable"
            id="course-datatable"
            :columns="columns"
            :options="$dtOptions"
            :ajax="'/courses/?group_id=' + group.id"
            class="d-none"
            @xhr="(e, settings, json) => courses = json.data"
        />

        <Teleport to="body">
            <SubscribeCourseModal :group_id="group.id"/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.course.expel')"
                :description="trans('global.course.expel_helper')"
                @close="showConfirm = false"
                @confirm="() => {
                    showConfirm = false;
                    destroy();
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
DataTable.use(DataTablesCore);

export default {
    components: {
        ConfirmModal,
        SubscribeCourseModal,
        DataTable,
        IndexWidget,
    },
    props: {
        group: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            courses: null,
            showConfirm: false,
            currentCourse: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
                { title: 'description', data: 'description', searchable: true },
            ],
            dt: null,
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.dt = this.$refs.datatable.dt;

        this.$eventHub.on('course-added', courses => {
            Object.assign(this.courses, courses);
        });

        this.$eventHub.on('filter', filter => {
            this.dt.search(filter.searchString).draw();
        });
    },
    methods: {
        confirmItemDelete(course){
            this.currentCourse = course;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/curricula/expel', {
                data: {
                    expel_list: [
                        {
                            group_id : this.group.id,
                            curriculum_id: [this.currentCourse.curriculum_id],
                        },
                    ],
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
}
</script>