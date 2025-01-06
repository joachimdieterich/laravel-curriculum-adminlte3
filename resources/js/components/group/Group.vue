<template>
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs"
                role="tablist">
                <li class="nav-item"
                    @click="setGlobalStorage('#group_'+group.id, '#group_curriculum_'+group.id)">
                    <a class="nav-link link-muted"
                       :class="getGlobalStorage('#group_'+group.id, '#group_curriculum_'+group.id, 'active', true)"
                       id="curriculum-nav-tab"
                       data-toggle="pill"
                       href="#curriculum-tab"
                       role="tab"
                       aria-controls="curriculum-tab"
                       aria-selected="false"
                    >
                        <i class="fas fa-th"></i>
                        <span v-if="help"> {{ trans('global.curriculum.title') }}</span>
                    </a>
                </li>
                <li v-permission="'group_enrolment'"
                    class="nav-item"
                    @click="setGlobalStorage('#group_'+group.id, '#group_users_'+group.id);">
                    <a class="nav-link link-muted"
                       :class="getGlobalStorage('#group_'+group.id, '#group_users_'+group.id)"
                       id="user-nav-tab"
                       data-toggle="pill"
                       href="#users-tab"
                       role="tab"
                       aria-controls="users-tab"
                       aria-selected="true"
                    >
                        <i class="fa fa-users"></i>
                        <span v-if="help"> {{ trans('global.user.title') }}</span>
                    </a>
                </li>
                <li class="nav-item "
                    v-permission="'logbook_access'"
                    @click="setGlobalStorage('#group_'+group.id, '#group_logbooks_'+group.id);">
                    <a class="nav-link link-muted"
                       :class="getGlobalStorage('#group_'+group.id, '#group_logbooks_'+group.id)"
                       id="logbook-nav-tab"
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
                <li class="nav-item "
                    v-permission="'kanban_access'"
                    @click="setGlobalStorage('#group_'+group.id, '#group_kanbans_'+group.id);">
                    <a class="nav-link link-muted"
                       :class="getGlobalStorage('#group_'+group.id, '#group_kanbans_'+group.id)"
                       id="kanban-nav-tab"
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
                <li class="nav-item"
                    v-permission="'plan_access'"
                    @click="setGlobalStorage('#group_'+group.id, '#group_plans_'+group.id);">
                    <a class="nav-link link-muted"
                       :class="getGlobalStorage('#group_'+group.id, '#group_plans_'+group.id)"
                       id="plans-nav-tab"
                       data-toggle="pill"
                       href="#plan-tab"
                       role="tab"
                       aria-controls="plan-tab"
                       aria-selected="true">
                        <i class="fa fa-clipboard-list"></i>
                        <span v-if="help">{{ trans('global.plan.title') }}</span>
                    </a>
                </li>
                <li v-permission="'exam_access'"
                    class="nav-item"
                    @click="setGlobalStorage('#group_'+group.id, '#group_tests_'+group.id);">
                    <a class="nav-link link-muted"
                       :class="getGlobalStorage('#group_'+group.id, '#group_tests_'+group.id)"
                       id="test-nav-tab"
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
                <li v-permission="'videoconference_access'"
                    class="nav-item"
                    @click="setGlobalStorage('#group_'+group.id, '#group_videoconference_'+group.id);">
                    <a class="nav-link link-muted"
                       :class="getGlobalStorage('#group_'+group.id, '#group_videoconference_'+group.id)"
                       id="test-nav-tab"
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
                    <a class="nav-link small link-muted pointer" style="line-height: 24px;"
                       @click="help = !help">
                        <i class="fa fa-question pr-1" style="font-size: 16px;"></i>
                    </a>
                </li>
                <li v-permission="'organization_edit'"
                    class="nav-item">
                    <a class="nav-link link-muted"
                       @click="editGroup()"
                       >
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                </li>
            </ul>
            <div class="tab-content"
                 id="custom-content-below-tabContent">
                <div class="tab-pane show pt-2"
                     :class="getGlobalStorage('#group_'+group.id, '#group_curriculum_'+group.id, 'active', true)"
                     id="curriculum-tab"
                     role="tabpanel"
                     aria-labelledby="curriculum-nav-tab">
                    <courses
                        ref="Courses"
                        :group="group"
                        create_label_field="enrol"></courses>

                </div>
               <div v-permission="'group_enrolment'"
                     class="tab-pane "
                     :class="getGlobalStorage('#group_'+group.id, '#group_users_'+group.id)"
                     id="users-tab"
                     role="tab"
                     aria-labelledby="users-nav-tab">
                    <users
                        ref="Users"
                        :reference="group"
                        delete_label_field="expel"
                        subscribable_type="App\Group"
                        :subscribable_id="group.id"
                        :subscribable="true"
                        create_label_field="enrol"></users>
               </div>
               <div v-permission="'logbook_access'"
                    class="tab-pane "
                    :class="getGlobalStorage('#group_'+group.id, '#group_logbooks_'+group.id)"
                    id="logbook-tab"
                    role="tab"
                    aria-labelledby="logbook-nav-tab">
                   <logbooks
                       ref="Logbooks"
                       :reference="group"
                       delete_label_field="expel"
                       subscribable_type="App\Group"
                       :subscribable_id="group.id"
                       :subscribable="true"
                   ></logbooks>
               </div>
               <div v-permission="'kanban_access'"
                    :class="getGlobalStorage('#group_'+group.id, '#group_kanbans_'+group.id)"
                      class="tab-pane "
                      id="kanban-tab"
                      role="tab"
                      aria-labelledby="kanban-nav-tab">
                     <kanbans
                         ref="Kanbans"
                         delete_label_field="expel"
                         subscribable_type="App\Group"
                         :subscribable_id="group.id"
                         :subscribable="true"
                         create_label_field="enrol"
                     ></kanbans>
                </div>
                <div v-permission="'plan_access'"
                     :class="getGlobalStorage('#group_'+group.id, '#group_plans_'+group.id)"
                     class="tab-pane"
                     id="plan-tab"
                     role="tab"
                     aria-labelledby="plan-nav-tab">
                    <plans
                        ref="Plans"
                        delete_label_field="expel"
                        subscribable_type="App\Group"
                        :subscribable_id="group.id"
                        :subscribable="true"
                        create_label_field="enrol"
                    ></plans>
                </div>
                <div v-permission="'exam_access'"
                     :class="getGlobalStorage('#group_'+group.id, '#group_tests_'+group.id)"
                     class="tab-pane "
                     id="tests-tab"
                     role="tab"
                     aria-labelledby="tests-nav-tab">
                    <Exams
                        ref="Exams"
                        delete_label_field="expel"
                        subscribable_type="App\Group"
                        :subscribable_id="group.id"
                        :subscribable="true"
                        create_label_field="enrol"
                    ></Exams>
                </div>
                <div v-permission="'videoconference_access'"
                     class="tab-pane "
                     :class="getGlobalStorage('#group_'+group.id, '#group_videoconference_'+group.id)"
                     id="videoconference-tab"
                     role="tab"
                     aria-labelledby="tests-nav-tab">
                    <videoconferences
                        ref="Videoconference"
                        subscribable_type="App\Group"
                        :subscribable_id="group.id"
                        :subscribable="true"
                        create_label_field="enrol"
                        delete_label_field="expel"
                    ></videoconferences>
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
            <GroupModal></GroupModal>
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
    name: "group",
    components:{
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
        //Tests
    },
    setup () {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    props: {
        group: {
            default: null
        },
        'courses': Array,
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
        editGroup(){
            this.globalStore?.showModal('group-modal', this.currentGroup);
        },
        loaderCourses: function() {
            //this.$refs.Courses.loaderEvent();
        },
        loaderEvent: function() {
            this.$refs.Contents.loaderEvent();
        },
        loadGroupUsers: function() {
            this.$refs.Users.loaderEvent();
        },
        loadLogbooks: function() {
            this.$refs.Logbooks.loaderEvent();
        },
        loadPlans: function() {
            this.$refs.Plans.loaderEvent();
        },
        loadTasks: function() {
            this.$refs.Tasks.loaderEvent();
        },
    },
}
</script>
