<template>
    <div :id="'#task_'+task.id" >
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <h5 class="m-0">
                        <i class="fa fa-tasks mr-1"></i> {{ task.title }}
                    </h5>
                </div>
                <div  class="card-tools pr-2">
                    <a v-can="'task_delete'"
                       @click.prevent="destroy()" >
                        <i class="fa fa-trash text-danger pr-4"></i>
                    </a>
                    <a v-can="'task_edit'"
                       @click.prevent="$modal.show('task-modal', {'method': 'patch', 'id': '{{ task.id }}'})" >
                        <i class="fa fa-pencil-alt"></i>
                    </a>
                </div>

            </div>
            <!-- /.card-header -->
            <div class="card-body py-2">
                <div class="row small">
                    <span class="col-md-6 col-sm-12 ">
                        <i class="fa fa-calendar pr-1"></i>
                        {{ task.start_date }}
                    </span>
                    <span class="col-md-6 col-sm-12">
                        <i class="fa fa-calendar-check pr-1"></i>
                        {{ task.start_date }}
                    </span>
                </div>

                <hr class="my-2">

                <span v-html="task.description"></span>

            </div>
            <!-- /.card-body -->

            <!--<div v-can="'task_edit'" class="card-footer">
                <small class="float-right">
                    {{ task.updated_at }}
                </small>
            </div>-->
        </div>

        <ul class="nav nav-tabs">
            <li class="nav-item small">
                <a class="nav-link link-muted"
                   v-bind:href="'#task_contents_'+task.id"
                   data-toggle="tab"
                   @click="loaderEvent()">
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
                    <i class="fa fa-folder-open pr-2"></i>{{ trans('global.media.title') }}
                </a>
            </li>
            <li class="nav-item small">
                <a class="nav-link link-muted"
                   href="#activity"
                   data-toggle="tab">
                    <i class="fa fa-paperclip pr-1"></i>{{ trans('global.subscription-billing') }}
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
                    <ul  v-can="'reference_create'" class="todo-list" data-widget="todo-list">
                        <li class="pointer bg-white">
                            <a @click="open('subscribe-objective-modal', 'referenceable');">
                                <i class="px-2 fa fa-plus text-muted"></i> {{ trans('global.terminalObjective.title') }}/{{ trans('global.enablingObjective.title') }}
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
            <div class="tab-pane" id="activity">
                <task-timeline
                    class="py-2"
                    :task="task"></task-timeline>
            </div><!-- /.tab-pane -->
        </div><!-- /.tab-content -->

    </div>

</template>

<script>
import ObjectiveBox from '../objectives/ObjectiveBox';
import Media from '../media/Media';
import Contents from '../content/Contents';

export default {
    props: {
        'task': Object,
        'search': ''
    },
    data() {
        return {
            media: {},
            active: true
        };
    },
    methods: {
        open(modal, relationKey) {
            if (modal === 'absence-modal'){
                this.$modal.show(modal, JSON.stringify(_.merge({ 'referenceable_type': 'App\\Task', 'referenceable_id': this.task.id}, this.task)));
            }
            else if (relationKey === 'referenceable'){
                this.$modal.show(modal, { 'referenceable_type': 'App\\Task', 'referenceable_id': this.task.id });
            } else {
                this.$modal.show(modal, { 'subscribable_type': 'App\\Task', 'subscribable_id': this.task.id });
            }
            //open tab
            switch (modal) {
                case "subscribe-objective-modal":
                    $('.nav-item a[href="#task_objectives_'+this.task.id+'"]').tab('show');
                break;
                case "task-modal":
                    $('.nav-item a[href="#task_tasks_1'+this.task.id+'"]').tab('show');
                break;
            }
        },
        edit() {
             this.$modal.show('task-modal', { 'id': this.task.id, 'method': 'patch'});
        },
        async destroy(){
            try {
                this.location = (await axios.delete('/tasks/'+this.task.id)).data.message;
            } catch(error) {
                alert(error);
            }
            window.open(this.location);
        },
        loaderEvent: function() {
            this.$refs.Contents.loaderEvent();
        }
    },

    mounted() {
    },

    components: {
        Media,
        ObjectiveBox,
        Contents
    }

}
</script>
