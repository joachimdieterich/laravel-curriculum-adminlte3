<template>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div class="card-title">
                        <h5 class="m-0">
                            <i class="fa fa-tasks mr-1"></i>
                            {{ this.currentTask.title }}
                        </h5>
                    </div>
                    <div
                        v-permission="'task_edit'"
                        class="card-tools pr-2">
                        <a  @click="destroy()">
                            <i class="fa fa-trash text-danger pr-4"></i>
                        </a>
                        <a  @click="editTask()">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row small">
                        <span class="col-md-6 col-sm-12 text-muted">
                            <i class="fa fa-calendar pr-1"></i>
                            {{ task.start_date }}
                        </span>
                            <span class="col-md-6 col-sm-12 text-muted">
                            <i class="fa fa-calendar-check pr-1"></i>
                            {{ task.start_date }}
                        </span>
                    </div>
                    <hr class="my-2">

                    <span v-html="task.description"></span>
                </div>

                <ul class="nav nav-tabs">
                    <li class="nav-item small">
                        <a class="nav-link link-muted"
                           v-bind:href="'#task_contents_'+task.id"
                           data-toggle="tab"
                           @click="loadContents()">
                            <i class="fa fa-align-justify pr-2"></i>{{ trans('global.content.title') }}</a>
                    </li>
                    <li class="nav-item small">
                        <a class="nav-link link-muted"
                           v-bind:href="'#task_objectives_'+task.id"
                           data-toggle="tab">
                            <i class="fa fa-bullseye pr-1"></i>
                            {{ trans('global.terminalObjective.title') }}/{{ trans('global.enablingObjective.title') }}
                        </a>
                    </li>
                    <li class="nav-item small">
                        <a class="nav-link link-muted"
                           v-bind:href="'#task_media_'+task.id"
                           data-toggle="tab">
                            <i class="fa fa-folder-open pr-2"></i>{{ trans('global.medium.title') }}
                        </a>
                    </li>
                    <li class="nav-item small">
                        <a class="nav-link link-muted"
                           href="#activity"
                           data-toggle="tab">
                            <i class="fa fa-paperclip pr-1"></i>{{ trans('global.task.subscriptions') }}
                        </a>
                    </li>
                    <!--<li class="nav-item small"><a class="nav-link link-muted" href="#timeline" data-toggle="tab">{{ trans('global.history') }}</a></li>-->
                </ul>

                <div class="tab-content" >
                    <div class="tab-pane"
                         v-bind:id="'task_contents_'+task.id"  >
                        <contents
                            ref="Contents"
                            subscribable_type="App\Task"
                            :subscribable_id="task.id"></contents>
                    </div>
                    <div class="tab-pane"
                         v-bind:id="'task_media_'+task.id">
                        <media subscribable_type="App\Task"
                               :subscribable_id="task.id"
                               format="list">
                        </media>
                    </div>

                    <div class="tab-pane"
                         v-bind:id="'task_objectives_'+task.id">
                        <div class="p-2">
                            <objective-box
                                v-for="terminal_subscription in task.terminal_objective_subscriptions"
                                v-bind:key="terminal_subscription.id"
                                :objective="terminal_subscription.terminal_objective"
                                type="terminal"></objective-box>
                            <span class="clearfix"></span>
                            <span v-for="enabling_subscription in task.enabling_objective_subscriptions">
                                <objective-box
                                    :objective="enabling_subscription.enabling_objective.terminal_objective"
                                    type="terminal"></objective-box>
                                <objective-box
                                    :objective="enabling_subscription.enabling_objective"
                                    type="enabling"></objective-box>
                                <span class="clearfix"></span>
                            </span>
                            <ul  v-permission="'reference_create'" class="todo-list" data-widget="todo-list">
                                <li class="pointer bg-white">
                                    <a @click="open('subscribe-objective-modal', 'referenceable');">
                                        <i class="px-2 fa fa-plus text-muted"></i> {{ trans('global.terminalObjective.title') }}/{{ trans('global.enablingObjective.title') }}
                                    </a>
                                </li>
                            </ul>
                        </div>

                    </div>
                    <div class="tab-pane" id="activity">
<!--                        <TaskTimeline
                            class="p-2"
                            :task="task"></TaskTimeline>-->
                    </div><!-- /.tab-pane -->
                </div><!-- /.tab-content -->

                <div class="card-footer">
                    <small class="float-right">
                        {{ this.currentTask.updated_at }}
                    </small>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <TaskModal
                :show="this.showTaskModal"
                @close="this.showTaskModal = false"
                :params="this.currentTask"
            ></TaskModal>
        </Teleport>
    </div>
</template>

<script>
import TaskModal from "../task/TaskModal.vue";
import Contents from '../content/Contents.vue';
import TaskTimeline from '../task/Timeline.vue';
import Media from '../media/Media.vue';

export default {
    name: "task",
    components:{
        TaskModal,
        Contents,
        TaskTimeline,
        Media
    },
    props: {
        task: {
            default: null
        },
    },
    data() {
        return {
            componentId: this.$.uid,
            showTaskModal: false,
            currentTask: {},
        }
    },
    mounted() {
        this.currentTask = this.task;
        this.$eventHub.on('task-updated', (task) => {
            this.currentTask = task;
            this.showTaskModal = false;
        });

    },
    methods: {
        editTask(){
            this.showTaskModal = true;
        },
        destroy(){},
        loadContents(){
            this.$refs.Contents.loaderEvent();
        }
    }
}
</script>
