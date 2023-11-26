<template>
    <div class="col-12 px-0">
        <ul class="nav nav-tabs"
            role="tablist">
            <li class="nav-item"
                @click="setLocalStorage('#group_'+group.id, '#group_curriculum_'+group.id)">
                <a class="nav-link link-muted"
                   :class="checkLocalStorage('#group_'+group.id, '#group_curriculum_'+group.id, 'active', true)"
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
            <li v-if="checkPermission('group_enrolment')"
                class="nav-item"
                @click="setLocalStorage('#group_'+group.id, '#group_users_'+group.id);">
                <a class="nav-link link-muted"
                   :class="checkLocalStorage('#group_'+group.id, '#group_users_'+group.id)"
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
                v-if="checkPermission('logbook_access')"
                @click="setLocalStorage('#group_'+group.id, '#group_logbooks_'+group.id);">
                <a class="nav-link link-muted"
                   :class="checkLocalStorage('#group_'+group.id, '#group_logbooks_'+group.id)"
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
                v-if="checkPermission('kanban_access')"
                @click="setLocalStorage('#group_'+group.id, '#group_kanbans_'+group.id);">
                <a class="nav-link link-muted"
                   :class="checkLocalStorage('#group_'+group.id, '#group_kanbans_'+group.id)"
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
               v-if="checkPermission('task_access')"
               @click="setLocalStorage('#group_'+group.id, '#group_tasks_'+group.id);">
                <a class="nav-link link-muted"
                   :class="checkLocalStorage('#group_'+group.id, '#group_tasks_'+group.id)"
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
                v-if="checkPermission('plan_access')"
                @click="setLocalStorage('#group_'+group.id, '#group_plans_'+group.id);">
                <a class="nav-link link-muted"
                   :class="checkLocalStorage('#group_'+group.id, '#group_plans_'+group.id)"
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
            <li v-if="checkPermission('test_access')"
                class="nav-item"
                @click="setLocalStorage('#group_'+group.id, '#group_tests_'+group.id);">
                <a class="nav-link link-muted"
                   :class="checkLocalStorage('#group_'+group.id, '#group_tests_'+group.id)"
                   id="test-nav-tab"
                   data-toggle="pill"
                   href="#tests-tab"
                   role="tab"
                   aria-controls="tests-tab"
                   aria-selected="true"
                >
                    <i class="fa-solid fa-ranking-star"></i>
                    <span v-if="help">{{ trans('global.test.title') }}</span>
                </a>
            </li>
            <li v-permission="'videoconference_access'"
                class="nav-item"
                @click="setLocalStorage('#group_'+group.id, '#group_videoconference_'+group.id);">
                <a class="nav-link link-muted"
                   :class="checkLocalStorage('#group_'+group.id, '#group_videoconference_'+group.id)"
                   id="test-nav-tab"
                   data-toggle="pill"
                   href="#videoconference-tab"
                   role="tab"
                   aria-controls="videoconference-tab"
                   aria-selected="true"
                >
                    <i class="fa-solid fa-video"></i>
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
                <a class="nav-link small link-muted"
                   @click="help = !help">
                    <i class="fa fa-question pr-1"></i>
                </a>
            </li>
            <li v-if="checkPermission('group_edit')"
                class="nav-item">
                <a class="nav-link link-muted"
                   :href="'/groups/'+ group.id +'/edit'"
                   id="config-nav-tab">
                    <i class="fa fa-cog"></i>
                </a>
            </li>
        </ul>

        <div class="tab-content"
             id="custom-content-below-tabContent">
            <div class="tab-pane show pt-2"
                 :class="checkLocalStorage('#group_'+group.id, '#group_curriculum_'+group.id, 'active', true)"
                 id="curriculum-tab"
                 role="tabpanel"
                 aria-labelledby="curriculum-nav-tab">
                <courses
                    ref="Courses"
                    :group="group"></courses>
<!--                <course-item
                    v-for="(item,index) in courses"
                    :key="'course_item'+index"
                    :course="item">
                </course-item>-->
<!--                <CourseIndexAddWidget
                    :group="group"
                    v-if="checkPermission('group_enrolment')"/>-->

            </div>
            <div v-if="checkPermission('group_enrolment')"
                 class="tab-pane "
                 :class="checkLocalStorage('#group_'+group.id, '#group_users_'+group.id)"
                 id="users-tab"
                 role="tab"
                 aria-labelledby="users-nav-tab">
                <users
                    ref="Users"
                    :group="group"></users>
            </div>
            <div v-if="checkPermission('logbook_access')"
               class="tab-pane "
                 :class="checkLocalStorage('#group_'+group.id, '#group_logbooks_'+group.id)"
                id="logbook-tab"
                role="tab"
                aria-labelledby="logbook-nav-tab">
                <logbooks
                    ref="Logbooks"
                    subscribable_type="App\Group"
                    :subscribable_id="group.id"
                ></logbooks>
           </div>
            <div v-if="checkPermission('kanban_access')"
                 class="tab-pane "
                 :class="checkLocalStorage('#group_'+group.id, '#group_kanbans_'+group.id)"
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
                 :class="checkLocalStorage('#group_'+group.id, '#group_tests_'+group.id)"
                 id="tests-tab"
                 role="tab"
                 aria-labelledby="tests-nav-tab">
                <tests
                    ref="Tests"
                    :group_id="group.id"></tests>
            </div>
            <div v-permission="'videoconference_access'"
                 class="tab-pane "
                 :class="checkLocalStorage('#group_'+group.id, '#group_videoconference_'+group.id)"
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
            </div>
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
</template>

<script>
import Videoconferences from "../videoconference/Videoconferences";
const Courses =
    () => import('../course/Courses.vue');
const Glossars =
    () => import('../glossar/Glossars');
const Media =
    () => import('../media/Media');
const Contents =
    () => import('../content/Contents');
const Users =
    () => import('../users/Users');
const Logbooks =
    () => import('../logbooks/Logbooks');
const Kanbans =
    () => import('../kanban/Kanbans');
const Tasks =
    () => import('../tasks/Tasks');
const Plans =
    () => import('../plan/Plans');
const Tests =
    () => import('../tests/Tests_Exams_View');

    export default {
        props: {
            'group': Object,
            'courses': Array,
        },
        data () {
            return {
                help: false,
            };
        },

        methods: {
            loaderCourses: function() {
                this.$refs.Courses.loaderEvent();
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
        mounted() {
            if (document.getElementById('searchbar') != null) {
                document.getElementById('searchbar').classList.remove('d-none');
            }

            this.$eventHub.$on('course-updated', () => {
                this.loaderCourses()
            });

        },
        components: {
            //CourseIndexAddWidget,
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
        }

    }
</script>
