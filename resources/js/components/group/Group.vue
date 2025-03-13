<template>
    <div class="row">
        <div class="col-12">
            <ul
                class="nav nav-tabs"
                role="tablist"
            >
                <li
                    class="nav-item"
                    @click="setGlobalStorage('#group_' + group.id, '#group_curriculum_' + group.id)"
                >
                    <a
                        id="curriculum-nav-tab"
                        class="nav-link link-muted"
                        :class="getGlobalStorage('#group_' + group.id, '#group_curriculum_' + group.id, 'active', true)"
                        data-toggle="pill"
                        href="#curriculum-tab"
                        role="tab"
                        aria-controls="curriculum-tab"
                        aria-selected="false"
                    >
                        <i class="fas fa-th"></i>
                        <span v-if="help">{{ trans('global.curriculum.title') }}</span>
                    </a>
                </li>
                <li
                    v-permission="'group_enrolment'"
                    class="nav-item"
                    @click="setGlobalStorage('#group_' + group.id, '#group_users_' + group.id);"
                >
                    <a
                        id="user-nav-tab"
                        class="nav-link link-muted"
                        :class="getGlobalStorage('#group_' + group.id, '#group_users_' + group.id)"
                        data-toggle="pill"
                        href="#users-tab"
                        role="tab"
                        aria-controls="users-tab"
                        aria-selected="true"
                    >
                        <i class="fa fa-users"></i>
                        <span v-if="help">{{ trans('global.user.title') }}</span>
                    </a>
                </li>
                <li
                    v-permission="'logbook_access'"
                    class="nav-item "
                    @click="setGlobalStorage('#group_' + group.id, '#group_logbooks_' + group.id);"
                >
                    <a
                        id="logbook-nav-tab"
                        class="nav-link link-muted"
                        :class="getGlobalStorage('#group_' + group.id, '#group_logbooks_' + group.id)"
                        data-toggle="pill"
                        href="#logbook-tab"
                        role="tab"
                        aria-controls="logbook-tab"
                        aria-selected="true"
                    >
                        <i class="fas fa-book "></i>
                        <span v-if="help">{{ trans('global.logbook.title') }}</span>
                    </a>
                </li>
                <li
                    v-permission="'kanban_access'"
                    class="nav-item "
                    @click="setGlobalStorage('#group_' + group.id, '#group_kanbans_' + group.id);"
                >
                    <a
                        id="kanban-nav-tab"
                        class="nav-link link-muted"
                        :class="getGlobalStorage('#group_' + group.id, '#group_kanbans_' + group.id)"
                        data-toggle="pill"
                        href="#kanban-tab"
                        role="tab"
                        aria-controls="kanban-tab"
                        aria-selected="true"
                    >
                        <i class="fa fa-columns"></i>
                        <span v-if="help">{{ trans('global.kanban.title') }}</span>
                    </a>
                </li>
<!--                <li class="nav-item"
                    v-permission="'task_access'"
                    @click="setGlobalStorage('#group_'+group.id, '#group_tasks_'+group.id);">
                    <a class="nav-link link-muted"
                       :class="getGlobalStorage('#group_'+group.id, '#group_tasks_'+group.id)"
                       id="task-nav-tab"
                       data-toggle="pill"
                       href="#task-tab"
                       role="tab"
                       aria-controls="task-tab"
                       aria-selected="true">
                        <i class="fas fa-tasks"></i>
                        <span v-if="help">{{ trans('global.task.title') }}</span>
                    </a>
                </li>-->
                <li
                    v-permission="'plan_access'"
                    class="nav-item"
                    @click="setGlobalStorage('#group_' + group.id, '#group_plans_' + group.id);"
                >
                    <a
                        id="plans-nav-tab"
                        class="nav-link link-muted"
                        :class="getGlobalStorage('#group_' + group.id, '#group_plans_' + group.id)"
                        data-toggle="pill"
                        href="#plan-tab"
                        role="tab"
                        aria-controls="plan-tab"
                        aria-selected="true"
                    >
                        <i class="fa fa-clipboard-list"></i>
                        <span v-if="help">{{ trans('global.plan.title') }}</span>
                    </a>
                </li>
                <!-- TODO: needs fix -->
                 <li
                    v-permission="'exam_access'"
                    class="nav-item"
                    @click="setGlobalStorage('#group_' + group.id, '#group_tests_' + group.id);"
                >
                    <a
                        id="test-nav-tab"
                        class="nav-link link-muted"
                        :class="getGlobalStorage('#group_' + group.id, '#group_tests_' + group.id)"
                        data-toggle="pill"
                        href="#tests-tab"
                        role="tab"
                        aria-controls="tests-tab"
                        aria-selected="true"
                    >
                        <i class="fa-solid fa-ranking-star"></i>
                        <span v-if="help">{{ trans('global.exam.title') }}</span>
                    </a>
                </li>
                <li
                    v-permission="'videoconference_access'"
                    class="nav-item"
                    @click="setGlobalStorage('#group_' + group.id, '#group_videoconference_' + group.id);"
                >
                    <a
                        id="test-nav-tab"
                        class="nav-link link-muted"
                        :class="getGlobalStorage('#group_' + group.id, '#group_videoconference_' + group.id)"
                        data-toggle="pill"
                        href="#videoconference-tab"
                        role="tab"
                        aria-controls="videoconference-tab"
                        aria-selected="true"
                    >
                        <i class="fa-solid fa-video"></i>
                        <span v-if="help">{{ trans('global.videoconference.title') }}</span>
                    </a>
                </li>
<!--                <li class="nav-item ">
                    <a v-if="group.glossar != null"
                       class="nav-link link-muted"
                       id="glossar-nav-tab"
                       data-toggle="pill"
                       href="#glossar-tab"
                       role="tab"
                       aria-controls="glossar-tab"
                       aria-selected="true">
                        <i class="fa fa-book-open pr-2"></i>
                        <span v-if="help"> {{ trans('global.glossar.title_singular') }}</span>
                    </a>
                    <a v-else
                       v-permission="'glossar_create'"
                       class="nav-link link-muted"
                       id="glossar-nav-tab"
                       :href="'/glossar/create?subscribable_type=App\\Group&subscribable_id='+ group.id "
                    >
                        <i class="fa fa-book-open pr-2"></i>{{ trans('global.glossar.create') }}
                    </a>
                </li>-->
                <!-- <li class="nav-item ">
                    <a class="nav-link link-muted"
                       id="medium-nav-tab"
                       data-toggle="pill"
                       href="#medium-tab"
                       role="tab"
                       aria-controls="medium-tab"
                       aria-selected="true">
                        <i class="fa fa-folder-open pr-2"></i>{{trans('global.medium.title')}}
                    </a>
                </li>-->
                <li class="nav-item ml-auto pull-right">
                    <a
                        class="nav-link small link-muted pointer"
                        style="line-height: 24px;"
                        @click="help = !help"
                    >
                        <i class="fa fa-question pr-1" style="font-size: 16px;"></i>
                    </a>
                </li>
                <li
                    v-permission="'organization_edit'"
                    class="nav-item"
                >
                    <a
                        class="nav-link link-muted"
                        @click="editGroup()"
                    >
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                </li>
            </ul>

            <div
                id="custom-content-below-tabContent"
                class="tab-content"
            >
                <div
                    id="curriculum-tab"
                    class="tab-pane show"
                    :class="getGlobalStorage('#group_' + group.id, '#group_curriculum_' + group.id, 'active', true)"
                    role="tabpanel"
                    aria-labelledby="curriculum-nav-tab"
                >
                    <courses
                        ref="Courses"
                        :group="group"
                        create_label_field="enrol"
                        delete_label_field="expel"
                    />
                </div>
               <div
                    v-permission="'group_enrolment'"
                    id="users-tab"
                    class="tab-pane"
                    :class="getGlobalStorage('#group_' + group.id, '#group_users_' + group.id)"
                    role="tab"
                    aria-labelledby="users-nav-tab"
                >
                    <users
                        ref="Users"
                        :reference="group"
                        delete_label_field="expel"
                        subscribable_type="App\Group"
                        :subscribable_id="group.id"
                        :subscribable="true"
                        create_label_field="enrol"
                    />
               </div>
               <div
                    v-permission="'logbook_access'"
                    id="logbook-tab"
                    class="tab-pane"
                    :class="getGlobalStorage('#group_' + group.id, '#group_logbooks_' + group.id)"
                    role="tab"
                    aria-labelledby="logbook-nav-tab"
                >
                    <logbooks
                        ref="Logbooks"
                        :reference="group"
                        delete_label_field="expel"
                        subscribable_type="App\Group"
                        :subscribable_id="group.id"
                        :subscribable="true"
                    />
               </div>
               <div
                    v-permission="'kanban_access'"
                    id="kanban-tab"
                    class="tab-pane"
                    :class="getGlobalStorage('#group_' + group.id, '#group_kanbans_' + group.id)"
                    role="tab"
                    aria-labelledby="kanban-nav-tab"
                >
                    <kanbans
                        ref="Kanbans"
                        delete_label_field="expel"
                        subscribable_type="App\Group"
                        :subscribable_id="group.id"
                        :subscribable="true"
                        create_label_field="enrol"
                    />
                </div>
                <div
                    v-permission="'plan_access'"
                    id="plan-tab"
                    class="tab-pane"
                    :class="getGlobalStorage('#group_' + group.id, '#group_plans_' + group.id)"
                    role="tab"
                    aria-labelledby="plan-nav-tab"
                >
                    <plans
                        ref="Plans"
                        delete_label_field="expel"
                        subscribable_type="App\Group"
                        :subscribable_id="group.id"
                        :subscribable="true"
                        create_label_field="enrol"
                    />
                </div>
                <!-- TODO: needs fix -->
                 <div
                    v-permission="'exam_access'"
                    id="tests-tab"
                    class="tab-pane"
                    :class="getGlobalStorage('#group_' + group.id, '#group_tests_' + group.id)"
                    role="tab"
                    aria-labelledby="tests-nav-tab"
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
                <div
                    v-permission="'videoconference_access'"
                    id="videoconference-tab"
                    class="tab-pane"
                    :class="getGlobalStorage('#group_' + group.id, '#group_videoconference_' + group.id)"
                    role="tab"
                    aria-labelledby="tests-nav-tab"
                >
                    <videoconferences
                        ref="Videoconference"
                        subscribable_type="App\Group"
                        :subscribable_id="group.id"
                        :subscribable="true"
                        create_label_field="enrol"
                        delete_label_field="expel"
                    />
                </div>
                <!-- <div v-permission="'task_access'"
                        class="tab-pane fade "
                        id="task-tab"
                        role="tab"
                        aria-labelledby="content-nav-tab">
                       <tasks
                           ref="Tasks"
                           subscribable_type="App\Group"
                           :subscribable_id="group.id"></tasks>
                   </div> -->
                <!-- <div v-if="group.glossar !== null"
                          class="tab-pane fade"
                          id="glossar-tab"
                          role="tab"
                          aria-labelledby="glossar-nav-tab">
                         <glossars
                             :glossar="group.glossar">
                         </glossars>
                     </div>-->
                <!--<div class="tab-pane fade "
                    id="medium-tab"
                    role="tab"
                    aria-labelledby="medium-nav-tab">
                   <media subscribable_type="App\Group"
                          :subscribable_id="group.id"
                          format="list">
                   </media>
               </div>-->
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
        Exams
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
        this.$eventHub.emit('showSearchbar', true);

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
