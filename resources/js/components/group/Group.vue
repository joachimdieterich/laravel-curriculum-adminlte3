<template>
    <div class="row">
        <div class="col-12">
            <ul
                class="nav nav-tabs"
                role="tablist"
            >
                <!-- 1 Curricula -->
                <li
                    class="nav-item"
                    role="presentation"
                    @click="setGlobalStorage('#group_' + group.id, '#group_curriculum_' + group.id)"
                >
                    <button
                        id="curricula-tab"
                        class="nav-link link-muted active"
                        data-toggle="tab"
                        data-target="#curricula"
                        type="button"
                        role="tab"
                        aria-controls="curricula"
                        aria-selected="true"
                    >
                        <i class="fas fa-th"></i>
                        <span v-if="help">{{ trans('global.curriculum.title') }}</span>
                    </button>
                </li>
                <!-- 2 Users -->
                <li
                    v-permission="'group_enrolment'"
                    class="nav-item"
                    role="presentation"
                    @click="setGlobalStorage('#group_' + group.id, '#group_users_' + group.id);"
                >
                    <button
                        id="users-tab"
                        class="nav-link link-muted"
                        data-toggle="tab"
                        data-target="#users"
                        type="button"
                        role="tab"
                        aria-controls="users"
                        aria-selected="false"
                    >
                        <i class="fa fa-users"></i>
                        <span v-if="help">{{ trans('global.user.title') }}</span>
                    </button>
                </li>
                <!-- 3 Logbooks -->
                <li
                    v-permission="'logbook_access'"
                    class="nav-item"
                    role="presentation"
                    @click="setGlobalStorage('#group_' + group.id, '#group_logbooks_' + group.id);"
                >
                    <button
                        id="logbooks-tab"
                        class="nav-link link-muted"
                        data-toggle="tab"
                        data-target="#logbooks"
                        type="button"
                        role="tab"
                        aria-controls="logbooks"
                        aria-selected="false"
                    >
                        <i class="fas fa-book"></i>
                        <span v-if="help">{{ trans('global.logbook.title') }}</span>
                    </button>
                </li>
                <!-- 4 Kanbans -->
                <li
                    v-permission="'kanban_access'"
                    class="nav-item"
                    role="presentation"
                    @click="setGlobalStorage('#group_' + group.id, '#group_kanbans_' + group.id);"
                >
                    <button
                        id="kanbans-tab"
                        class="nav-link link-muted"
                        data-toggle="tab"
                        data-target="#kanbans"
                        type="button"
                        role="tab"
                        aria-controls="kanbans"
                        aria-selected="false"
                    >
                        <i class="fa fa-columns"></i>
                        <span v-if="help">{{ trans('global.kanban.title') }}</span>
                    </button>
                </li>
                <!-- 5 Tasks -->
                <!-- <li
                    v-permission="'task_access'"
                    class="nav-item"
                    role="presentation"
                    @click="setGlobalStorage('#group_' + group.id, '#group_tasks_' + group.id);"
                >
                    <button
                        id="tasks-tab"
                        class="nav-link link-muted"
                        data-toggle="tab"
                        data-target="#tasks"
                        type="button"
                        role="tab"
                        aria-controls="tasks"
                        aria-selected="false"
                    >
                        <i class="fas fa-tasks"></i>
                        <span v-if="help">{{ trans('global.task.title') }}</span>
                    </button>
                </li> -->
                <!-- 6 Plans -->
                <li
                    v-permission="'plan_access'"
                    class="nav-item"
                    role="presentation"
                    @click="setGlobalStorage('#group_' + group.id, '#group_plans_' + group.id);"
                >
                    <button
                        id="plans-tab"
                        class="nav-link link-muted"
                        data-toggle="tab"
                        data-target="#plans"
                        type="button"
                        role="tab"
                        aria-controls="plans"
                        aria-selected="false"
                    >
                        <i class="fa fa-clipboard-list"></i>
                        <span v-if="help">{{ trans('global.plan.title') }}</span>
                    </button>
                </li>
                <!-- 7 Exams -->
                <li
                    v-permission="'exam_access'"
                    class="nav-item"
                    role="presentation"
                    @click="setGlobalStorage('#group_' + group.id, '#group_exams_' + group.id);"
                >
                    <button
                        id="exams-tab"
                        class="nav-link link-muted"
                        data-toggle="tab"
                        data-target="#exams"
                        type="button"
                        role="tab"
                        aria-controls="exams"
                        aria-selected="false"
                    >
                        <i class="fa-solid fa-ranking-star"></i>
                        <span v-if="help">{{ trans('global.exam.title') }}</span>
                    </button>
                </li>
                <!-- 8 Videoconferences -->
                <li
                    v-permission="'videoconference_access'"
                    class="nav-item"
                    role="presentation"
                    @click="setGlobalStorage('#group_' + group.id, '#group_videoconference_' + group.id);"
                >
                    <a
                        id="videoconferences-tab"
                        class="nav-link link-muted"
                        data-toggle="tab"
                        data-target="#videoconferences"
                        type="button"
                        role="tab"
                        aria-controls="videoconferences"
                        aria-selected="false"
                    >
                        <i class="fa-solid fa-video"></i>
                        <span v-if="help">{{ trans('global.videoconference.title') }}</span>
                    </a>
                </li>
                <!-- 9 Glossar -->
                <!-- <li
                    class="nav-item"
                    role="presentation"
                >
                    <button v-if="group.glossar != null"
                        id="glossar-tab"
                        class="nav-link link-muted"
                        data-toggle="tab"
                        data-target="#glossar"
                        type="button"
                        role="tab"
                        aria-controls="glossar"
                        aria-selected="false"
                    >
                        <i class="fa fa-book-open pr-2"></i>
                        <span v-if="help">{{ trans('global.glossar.title_singular') }}</span>
                    </button>
                    <button v-else
                        v-permission="'glossar_create'"
                        class="nav-link link-muted"
                        id="create-glossar"
                        :href="'/glossar/create?subscribable_type=App\\Group&subscribable_id=' + group.id"
                    >
                        <i class="fa fa-book-open pr-2"></i>
                        {{ trans('global.glossar.create') }}
                    </button>
                </li> -->
                <!-- 10 Media -->
                <!-- <li
                    class="nav-item"
                    role="presentation"
                >
                    <button
                        id="media-tab"
                        class="nav-link link-muted"
                        data-toggle="tab"
                        data-target="#media"
                        type="button"
                        role="tab"
                        aria-controls="media"
                        aria-selected="false"
                    >
                        <i class="fa fa-folder-open pr-2"></i>
                        {{ trans('global.medium.title') }}
                    </button>
                </li> -->
                <!-- Help -->
                <li class="nav-item ml-auto pull-right">
                    <button
                        class="nav-link small link-muted pointer"
                        style="line-height: 24px;"
                        @click="help = !help"
                    >
                        <i class="fa fa-question pr-1" style="font-size: 16px;"></i>
                    </button>
                </li>
                <!-- Edit -->
                <li
                    v-permission="'group_edit'"
                    class="nav-item"
                >
                    <button
                        class="nav-link link-muted"
                        @click="editGroup()"
                    >
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- 1 Curricula -->
                <div
                    id="curricula"
                    class="tab-pane fade show active"
                    role="tabpanel"
                    aria-labelledby="curriculum-tab"
                >
                    <Courses
                        ref="Courses"
                        :group="group"
                        create_label_field="enrol"
                        delete_label_field="expel"
                    />
                </div>
                <!-- 2 Users -->
                <div v-if="checkPermission('group_enrolment')"
                    id="users"
                    class="tab-pane fade"
                    role="tabpanel"
                    aria-labelledby="users-tab"
                >
                    <Users
                        ref="Users"
                        :reference="group"
                        delete_label_field="expel"
                        subscribable_type="App\Group"
                        :subscribable_id="group.id"
                        :subscribable="true"
                        create_label_field="enrol"
                    />
                </div>
                <!-- 3 Logbooks -->
                <div v-if="checkPermission('logbook_access')"
                    id="logbooks"
                    class="tab-pane fade"
                    role="tabpanel"
                    aria-labelledby="logbooks-tab"
                >
                    <Logbooks
                        ref="Logbooks"
                        :reference="group"
                        delete_label_field="expel"
                        subscribable_type="App\Group"
                        :subscribable_id="group.id"
                        :subscribable="true"
                    />
                </div>
                <!-- 4 Kanbans -->
                <div v-if="checkPermission('kanban_access')"
                    id="kanbans"
                    class="tab-pane fade"
                    role="tabpanel"
                    aria-labelledby="kanbans-tab"
                >
                    <Kanbans
                        ref="Kanbans"
                        delete_label_field="expel"
                        subscribable_type="App\Group"
                        :subscribable_id="group.id"
                        :subscribable="true"
                        create_label_field="enrol"
                    />
                </div>
                <!-- 5 Tasks -->
                <!-- <div
                    v-permission="'task_access'"
                    id="tasks"
                    class="tab-pane fade"
                    role="tabpanel"
                    aria-labelledby="tasks-tab"
                >
                    <Tasks
                        ref="Tasks"
                        subscribable_type="App\Group"
                        :subscribable_id="group.id"
                    />
                </div> -->
                <!-- 6 Plans -->
                <div v-if="checkPermission('plan_access')"
                    id="plans"
                    class="tab-pane fade"
                    role="tabpanel"
                    aria-labelledby="plans-tab"
                >
                    <Plans
                        ref="Plans"
                        delete_label_field="expel"
                        subscribable_type="App\Group"
                        :subscribable_id="group.id"
                        :subscribable="true"
                        create_label_field="enrol"
                    />
                </div>
                <!-- 7 Exams -->
                <div v-if="checkPermission('exam_access')"
                    id="exams"
                    class="tab-pane fade"
                    role="tabpanel"
                    aria-labelledby="exams-tab"
                >
                    <Exams
                        ref="Exams"
                        delete_label_field="expel"
                        subscribable_type="App\Group"
                        :subscribable_id="group.id"
                        :subscribable="true"
                        create_label_field="enrol"
                    />
                </div>
                <!-- 8 Videoconferences -->
                <div v-if="checkPermission('videoconference_access')"
                    id="videoconferences"
                    class="tab-pane fade"
                    role="tabpanel"
                    aria-labelledby="videoconferences-tab"
                >
                    <Videoconferences
                        ref="Videoconference"
                        subscribable_type="App\Group"
                        :subscribable_id="group.id"
                        :subscribable="true"
                        create_label_field="enrol"
                        delete_label_field="expel"
                    />
                </div>
                <!-- 9 Glossar -->
                <!-- <div v-if="group.glossar !== null"
                    id="glossar"
                    class="tab-pane fade"
                    role="tabpanel"
                    aria-labelledby="glossar-tab"
                >
                    <Glossars :glossar="group.glossar"/>
                </div> -->
                <!-- 10 Media -->
                <!-- <div
                    id="media"
                    class="tab-pane fade"
                    role="tabpanel"
                    aria-labelledby="media-tab"
                >
                   <Media
                        subscribable_type="App\Group"
                        :subscribable_id="group.id"
                        format="list"
                    />
               </div> -->
            </div>
        </div>
        <Teleport to="body">
            <GroupModal/>
        </Teleport>
        <teleport to="#customTitle">
            <small>{{ this.currentGroup.title }} </small>
        </teleport>
    </div>
</template>
<script>
import GroupModal from "../group/GroupModal.vue";
import Videoconferences from "../videoconference/Videoconferences.vue";
import Courses from "../course/Courses.vue";
//import Glossars from "../glossar/Glossars.vue";
import Media from "../media/Media.vue";
import Contents from "../content/Contents.vue";
import Users from "../user/Users.vue";
import Logbooks from "../logbook/Logbooks.vue";
import Kanbans from "../kanban/Kanbans.vue";
//import Tasks from "../task/Tasks.vue";
import Plans from "../plan/Plans.vue";
import Exams from "../exam/Exams.vue";
//import Tests from "../tests/Tests_Exams_View.vue";
import {useGlobalStore} from "../../store/global";

export default {
    components: {
        GroupModal,
        Videoconferences,
        Users,
        Courses,
        Media,
        //Glossars,
        Contents,
        Logbooks,
        Kanbans,
        //Tasks,
        Plans,
        Exams,
        //Tests,
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    props: {
        group: {
            default: null,
        },
        courses: Array,
    },
    data() {
        return {
            componentId: this.$.uid,
            currentGroup: {},
            help: true,
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.currentGroup = this.group;

        this.$eventHub.on('group-updated', (group) => {
            this.currentGroup = group;
            this.globalStore?.closeModal('group-modal');
        });

        this.$eventHub.on('course-updated', () => {
            this.loaderCourses()
        });

    },
    methods: {
        editGroup() {
            this.globalStore?.showModal('group-modal', this.currentGroup);
        },
        loaderCourses() {
            //this.$refs.Courses.loaderEvent();
        },
        loaderEvent() {
            this.$refs.Contents.loaderEvent();
        },
        loadGroupUsers() {
            this.$refs.Users.loaderEvent();
        },
        loadLogbooks() {
            this.$refs.Logbooks.loaderEvent();
        },
        loadPlans() {
            this.$refs.Plans.loaderEvent();
        },
        loadTasks() {
            this.$refs.Tasks.loaderEvent();
        },
    },
}
</script>
