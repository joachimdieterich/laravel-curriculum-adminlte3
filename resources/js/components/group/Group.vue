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
                <li class="nav-item"
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
                </li>
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
                <li v-permission="'test_access'"
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
                <li class="nav-item ">
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
                </li>
                <!-- <li class="nav-item ">
                    <a class="nav-link link-muted"
                       id="medium-nav-tab"
                       data-toggle="pill"
                       href="#medium-tab"
                       role="tab"
                       aria-controls="medium-tab"
                       aria-selected="true">
                        <i class="fa fa-folder-open pr-2"></i>{{trans('global.media.title')}}
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
                        :group="group"></courses>

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
                        :subscribable="true"
                        subscribable_type="App\Group"
                        :subscribable_id="group.id"></users>
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
                       subscribable_type="App\Group"
                       :subscribable_id="group.id"
                       :subscribable="true"
                   ></logbooks>
               </div>
                <!--                 <div v-if="checkPermission('kanban_access')"
                                      class="tab-pane "
                                      :class="getGlobalStorage('#group_'+group.id, '#group_kanbans_'+group.id)"
                                      id="kanban-tab"
                                      role="tab"
                                      aria-labelledby="kanban-nav-tab">
                                     <kanbans
                                         ref="Kanbans"
                                         subscribable_type="App\Group"
                                         :subscribable_id="group.id"
                                     ></kanbans>
                                 </div>
                                 <div v-if="checkPermission('task_access')"
                                      class="tab-pane fade "
                                      id="task-tab"
                                      role="tab"
                                      aria-labelledby="content-nav-tab">
                                     <tasks
                                         ref="Tasks"
                                         subscribable_type="App\Group"
                                         :subscribable_id="group.id"></tasks>
                                 </div>
                                 <div v-if="checkPermission('test_access')"
                                      class="tab-pane "
                                      :class="getGlobalStorage('#group_'+group.id, '#group_tests_'+group.id)"
                                      id="tests-tab"
                                      role="tab"
                                      aria-labelledby="tests-nav-tab">
                                     <tests
                                         ref="Tests"
                                         :group_id="group.id"></tests>
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
                                         :subscribable_id="group.id"></videoconferences>
                                 </div>
                                 <div v-if="checkPermission('plan_access')"
                                      class="tab-pane fade "
                                      id="plan-tab"
                                      role="tab"
                                      aria-labelledby="content-nav-tab">
                                     <plans
                                         ref="Plans"
                                         subscribable_type="App\Group"
                                         :subscribable_id="group.id"></plans>
                                 </div>
                                 <div v-if="group.glossar !== null"
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

<!--            <div class="card card-primary">
                <div class="card-header">
                    <div class="card-title">
                        <h5 class="m-0">
                            <i class="fas fa-layer-group mr-1"></i>
                            {{ this.currentGroup.title }}
                        </h5>
                    </div>
                    <div
                        v-permission="'organization_edit'"
                        class="card-tools pr-2">
                        <a  @click="editGroup()">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    </div>

                </div>

                <div class="card-body">

                    <p class="text-muted">
                        {{ trans('global.group.fields.external_begin') }}: {{ this.currentGroup.external_begin }}<br>
                        {{ trans('global.group.fields.external_end') }}: {{ this.currentGroup.external_end }}
                    </p>
                    <hr>

                    <strong>
                        <i class="fas fa-city mr-1"></i>
                        {{ trans('global.organizationType.title_singular') }}
                    </strong>
                    <p class="text-muted">
                        {{ this.currentGroup.organization_type?.title }}
                    </p>
                    <hr>
                </div>

                <div class="card-footer">
                    <small class="float-right">
                        {{ this.currentGroup.updated_at }}
                    </small>
                </div>
            </div>-->
        </div>

        <Teleport to="body">
            <GroupModal
                :show="this.showGroupModal"
                @close="this.showGroupModal = false"
                :params="this.currentGroup"
            ></GroupModal>
        </Teleport>
    </div>
</template>

<script>
import GroupModal from "../group/GroupModal";
import Videoconferences from "../videoconference/Videoconferences";
import Courses from "../course/Courses";
import Glossars from "../glossar/Glossars";
import Media from "../media/Media";
import Contents from "../content/Contents";
import Users from "../user/Users";
import Logbooks from "../logbook/Logbooks";
import Kanbans from "../kanban/Kanbans";
import Tasks from "../task/Tasks";
import Plans from "../plan/Plans";
import Tests from "../tests/Tests_Exams_View";

export default {
    name: "group",
    components:{
        GroupModal,
        Videoconferences,
        Users,
        Courses,
        Media,
        Glossars,
        Contents,
        Logbooks,
        Kanbans,
        Tasks,
        Plans,
        Tests
    },
    props: {
        group: {
            default: null
        },
        'courses': Array,
    },
    data() {
        return {
            componentId: this._uid,
            showGroupModal: false,
            currentGroup: {},
            help: true,
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);
        this.currentGroup = this.group;
        this.$eventHub.on('group-updated', (group) => {
            this.currentGroup = group;
            this.showGroupModal = false;
        });
        this.$eventHub.on('course-updated', () => {
            this.loaderCourses()
        });

    },
    methods: {
        editGroup(){
            this.showGroupModal = true;
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
