<template>
    <!-- timeline item -->
    <div :id="'#logbook_'+entry.id"
        class="card collapsed-card col-12"
        :style="isActive"
        >
        <div class="user-block p-2"
            data-card-widget="collapse"
            :data-target="'#logbook_body_'+entry.id"
            aria-expanded="true">

            <span class="username ml-0">
                <div class="pull-right " v-can="'logbook_entry_edit'">
                    <button type="button"
                            class="btn btn-tool  pt-3"
                            data-toggle="dropdown"
                            aria-expanded="true">
                        <i class="fa fa-plus"></i>
                    </button>
                    <div class="dropdown-menu"
                         x-placement="top-start"
                         style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, -2px, 0px);">
                        <button class="dropdown-item" @click.prevent="open('subscribe-objective-modal', 'referenceable');">
                            <i class="fa fa-bullseye"></i>
                            <span class="ml-2">{{ trans('global.terminalObjective.title') }}/{{ trans('global.enablingObjective.title') }}</span>
                        </button>
                        <button class="dropdown-item" @click.prevent="open('task-modal', 'subscribable');">
                            <i class="fa fa-tasks"></i>
                            <span class="ml-2">{{ trans('global.task.create') }}</span>
                        </button>
                        <button class="dropdown-item" @click.prevent="open('absence-modal', 'referenceable');">
                            <i class="fa fa-user-times"></i>
                            <span class="ml-2">{{ trans('global.absences.create') }}</span>
                        </button>
                        <div class="dropdown-divider"></div>
                        <button class="dropdown-item" @click.prevent="edit()">
                            <i class="fa fa-edit"></i>
                            <span class="ml-2">{{ trans('global.logbookEntry.edit') }}</span>
                        </button>
                        <button class="dropdown-item text-danger" @click.prevent="destroy()">
                            <i class="fa fa-trash"></i>
                            <span class="ml-2">{{ trans('global.delete') }}</span>
                        </button>
                    </div>
                </div>
                <span href="#">{{ entry.title }}</span>
                <span class="description ml-0">{{ postDate() }}</span>
            </span>

        </div>

        <div class="card-body p-0 collapse" :id="'#logbook_body_'+entry.id" >
            <hr class="m-1">
            <span class="clearfix"></span>

            <ul class="nav nav-pills">
                <li class="nav-item small">
                    <a class="nav-link active show"
                       v-bind:href="'#logbook_description_'+entry.id"
                       data-toggle="tab">
                        {{ trans('global.logbook.fields.description') }}
                    </a>
                </li>
                <li class="nav-item small">
                    <a class="nav-link"
                       v-bind:href="'#logbook_media_'+entry.id"
                       data-toggle="tab">
                        {{ trans('global.media.title') }}
                    </a>
                </li>
                <li class="nav-item small">
                    <a class="nav-link"
                       v-bind:href="'#logbook_objectives_'+entry.id"
                       data-toggle="tab">
                        {{ trans('global.terminalObjective.title') }}/{{ trans('global.enablingObjective.title') }}
                    </a>
                </li>
                <li class="nav-item small">
                    <a class="nav-link"
                       v-bind:href="'#logbook_contents_'+entry.id"
                       data-toggle="tab">{{ trans('global.content.title') }}</a>
                </li>
                <li class="nav-item small">
                    <a class="nav-link"
                       v-bind:href="'#logbook_tasks_'+entry.id"
                       data-toggle="tab">{{ trans('global.task.title') }}</a>
                </li>
                <li class="nav-item small">
                    <a class="nav-link"
                       v-bind:href="'#logbook_userStatuses_'+entry.id"
                       data-toggle="tab">{{ trans('global.absences.title') }}</a>
                </li>
            </ul>
            <span class="clearfix"></span>
            <hr class="m-1">


            <div class="p-2">
                <div class="tab-content">
                    <!-- tab-pane -->
                    <div class="tab-pane active show"
                         v-bind:id="'logbook_description_'+entry.id">
                         <span class="" v-html="entry.description"></span>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane"
                         v-bind:id="'logbook_media_'+entry.id">
                        <media subscribable_type="App\LogbookEntry"
                               :subscribable_id="entry.id"
                               format="list">
                        </media>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane"
                         v-bind:id="'logbook_objectives_'+entry.id">
                        <objective-box
                            v-for="terminal_subscription in entry.terminal_objective_subscriptions"
                            v-bind:key="terminal_subscription.id"
                            :objective="terminal_subscription.terminal_objective"
                            type="terminal"></objective-box>
                        <span class="clearfix"></span>
                        <span v-for="enabling_subscription in entry.enabling_objective_subscriptions">
                            <objective-box
                                :objective="enabling_subscription.enabling_objective.terminal_objective"
                                type="terminal"></objective-box>
                            <objective-box
                                :objective="enabling_subscription.enabling_objective"
                                type="enabling"></objective-box>
                            <span class="clearfix"></span>
                        </span>
                    </div>
                    <!-- /.tab-pane -->
                    <!-- tab-pane -->
                    <div class="tab-pane "
                         v-bind:id="'logbook_contents_'+entry.id"  >
                        <contents subscribable_type="App\LogbookEntry"
                                  :subscribable_id="entry.id"></contents>
                    </div>
                    <!-- /.tab-pane -->
                    <!-- tab-pane -->
                    <div class="tab-pane show"
                         v-bind:id="'logbook_tasks_'+entry.id">
                         <task-list  class="p-2"
                            :tasks="entry.task_subscription">
                         </task-list>

                    </div>
                    <!-- /.tab-pane -->
                    <!-- tab-pane -->
                    <div class="tab-pane show"
                         v-bind:id="'logbook_userStatuses_'+entry.id"  >
                        <absences  class="p-2"
                            :absences="entry.absences">
                        </absences>

                    </div>
                    <!-- /.tab-pane -->
                </div>
            </div>
        </div>
    </div>
    <!-- END timeline item -->
</template>

<script>
    import Absences from '../absence/Absences';
    import Contents from '../content/Contents';
    import ObjectiveBox from '../objectives/ObjectiveBox';
    import TaskList from '../uiElements/TaskList';
    import Media from '../media/Media';

    export default {
        props: {
            'logbook': Object,
            'entry': Object,
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
                    this.$modal.show(modal, JSON.stringify(_.merge({ 'referenceable_type': 'App\\LogbookEntry', 'referenceable_id': this.entry.id}, this.logbook)));
                }
                else if (relationKey === 'referenceable'){
                    this.$modal.show(modal, { 'referenceable_type': 'App\\LogbookEntry', 'referenceable_id': this.entry.id });
                } else {
                    this.$modal.show(modal, { 'subscribable_type': 'App\\LogbookEntry', 'subscribable_id': this.entry.id });
                }
                //open tab
                switch (modal) {
                    case "logbook-subscribe-objective-modal":
                        $('.nav-item a[href="#logbook_objectives_'+this.entry.id+'"]').tab('show');
                    break;
                    case "task-modal":
                        $('.nav-item a[href="#logbook_tasks_1'+this.entry.id+'"]').tab('show');
                    break;
                }

            },
            edit() {
                 this.$modal.show('logbook-entry-modal', { 'id': this.entry.id, 'method': 'patch'});
            },
            async destroy(){
                try {
                    this.location = (await axios.delete('/logbookEntries/'+this.entry.id)).data.message;
                } catch(error) {
                    alert(error);
                }
                location.reload(true);
            },

            postDate() {
                var start = new Date(this.entry.begin.replace(/-/g, "/"));
                var end   = new Date(this.entry.end.replace(/-/g, "/"));
                var dateFormat = { weekday: 'short', day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute:'2-digit'};

                if (start.toDateString() === end.toDateString()) {
                  return start.toLocaleString([], dateFormat) + " - " + end.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                } else {
                  return start.toLocaleString([], dateFormat) + " - " + end.toLocaleString([], dateFormat);
                }

            },
        },
        computed: {

            isActive: function(){
                if (this.entry.title.toLowerCase().indexOf(this.search.toLowerCase()) === -1){
                    return "display:none";
                } else {
                    return "";
                }
            }
        },

        components: {
            Absences,
            Media,
            Contents,
            ObjectiveBox,
            TaskList
        }
    }
</script>
