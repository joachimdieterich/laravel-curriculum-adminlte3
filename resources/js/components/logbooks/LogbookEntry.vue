<template>
    <div :id="'#logbook_'+entry.id"
         class="card col-12"
         :class="{'collapsed-card' : first === false}"
         :style="isActive"
        >
        <div class="user-block p-2 "
            data-card-widget="collapse"
            :data-target="'#logbook_body_'+entry.id"
            aria-expanded="true">

            <span class="username ml-0">
                <span class="pull-right " v-can="'logbook_entry_edit'">
                    <button type="button"
                            class="btn btn-tool pt-3"
                            @click.prevent="destroy()">
                        <i class="fa fa-trash "></i>
                    </button>
                     <button type="button"
                             class="btn btn-tool pt-3"
                             @click.prevent="edit()">
                        <i class="fa fa-pencil-alt "></i>
                    </button>
                </span>
                <span >{{ entry.title }}</span>
                <span class="description ml-0 ">{{ postDate() }}</span>
            </span>

        </div>

        <div class="card-body p-0 "
             :class="{'collapse' : first === false}"
             :id="'#logbook_body_'+entry.id" >
            <hr class="m-1">
            <span class="clearfix"></span>

            <ul class="nav nav-pills">
                <li class="nav-item small"
                    @click="setLocalStorage('#logbook_'+entry.id, '#logbook_description_'+entry.id)">
                    <a class="nav-link show"
                       :class="checkLocalStorage('#logbook_'+entry.id, '#logbook_description_'+entry.id)"
                       v-bind:href="'#logbook_description_'+entry.id"
                       data-toggle="tab">
                        {{ trans('global.logbook.fields.description') }}
                    </a>
                </li>
                <li class="nav-item small"
                    @click="setLocalStorage('#logbook_'+entry.id, '#logbook_media_'+entry.id)">
                    <a class="nav-link"
                       :class="checkLocalStorage('#logbook_'+entry.id, '#logbook_media_'+entry.id)"
                       v-bind:href="'#logbook_media_'+entry.id"
                       data-toggle="tab">
                        {{ trans('global.media.title') }}
                    </a>
                </li>
                <li class="nav-item small"
                    @click="setLocalStorage('#logbook_'+entry.id, '#logbook_objectives_'+entry.id)">
                    <a class="nav-link"
                       :class="checkLocalStorage('#logbook_'+entry.id, '#logbook_objectives_'+entry.id)"
                       v-bind:href="'#logbook_objectives_'+entry.id"
                       data-toggle="tab">
                        {{ trans('global.terminalObjective.title') }}/{{ trans('global.enablingObjective.title') }}
                    </a>
                </li>
                <li class="nav-item small"
                    @click="setLocalStorage('#logbook_'+entry.id, '#logbook_contents_'+entry.id)">
                    <a class="nav-link"
                       :class="checkLocalStorage('#logbook_'+entry.id, '#logbook_contents_'+entry.id)"
                       v-bind:href="'#logbook_contents_'+entry.id"
                       data-toggle="tab"
                       @click="loaderEvent()">{{ trans('global.content.title') }}</a>
                </li>
                <li class="nav-item small"
                    @click="setLocalStorage('#logbook_'+entry.id, '#logbook_tasks_'+entry.id)">
                    <a class="nav-link"
                       :class="checkLocalStorage('#logbook_'+entry.id, '#logbook_tasks_'+entry.id)"
                       v-bind:href="'#logbook_tasks_'+entry.id"
                       data-toggle="tab">{{ trans('global.task.title') }}</a>
                </li>
                <li v-can="'absence_access'"
                    v-if="displayAbsences()"
                    class="nav-item small"
                    @click="setLocalStorage('#logbook_'+entry.id, '#logbook_userStatuses_'+entry.id)">
                    <a class="nav-link"
                       :class="checkLocalStorage('#logbook_'+entry.id, '#logbook_userStatuses_'+entry.id)"
                       v-bind:href="'#logbook_userStatuses_'+entry.id"
                       data-toggle="tab"
                       @click="loaderAbsences()">{{ trans('global.absences.title') }}</a>
                </li>
            </ul>
            <span class="clearfix"></span>
            <hr class="m-1">


            <div class="pb-2 px-1">
                <div class="tab-content">
                    <!-- tab-pane -->
                    <div class="tab-pane"
                         :class="checkLocalStorage('#logbook_'+entry.id, '#logbook_description_'+entry.id)"
                         v-bind:id="'logbook_description_'+entry.id">
                         <span class="" v-html="entry.description"></span>
                    </div>
                    <!-- /.tab-pane -->
                    <div v-can="'medium_access'"
                         class="tab-pane"
                         :class="checkLocalStorage('#logbook_'+entry.id, '#logbook_media_'+entry.id)"
                         v-bind:id="'logbook_media_'+entry.id">
                        <media subscribable_type="App\LogbookEntry"
                               :subscribable_id="entry.id"
                               format="list">
                        </media>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane"
                         :class="checkLocalStorage('#logbook_'+entry.id, '#logbook_objectives_'+entry.id)"
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
                        <ul  v-can="'reference_create'" class="todo-list" data-widget="todo-list">
                            <li class="pointer bg-white">
                                <a @click="open('subscribe-objective-modal', 'referenceable');">
                                    <i class="px-2 fa fa-plus text-muted"></i> {{ trans('global.terminalObjective.title') }}/{{ trans('global.enablingObjective.title') }}
                                </a>
                            </li>
                        </ul>

                    </div>
                    <!-- /.tab-pane -->
                    <!-- tab-pane -->
                    <div v-can="'content_access'"
                         class="tab-pane "
                         :class="checkLocalStorage('#logbook_'+entry.id, '#logbook_contents_'+entry.id)"
                         v-bind:id="'logbook_contents_'+entry.id"  >
                        <contents
                            class="mb-0"
                            ref="Contents"
                            subscribable_type="App\LogbookEntry"
                            :subscribable_id="entry.id">

                        </contents>
                    </div>
                    <!-- /.tab-pane -->
                    <!-- tab-pane -->
                    <div v-can="'task_access'"
                         class="tab-pane"
                         :class="checkLocalStorage('#logbook_'+entry.id, '#logbook_tasks_'+entry.id)"
                         v-bind:id="'logbook_tasks_'+entry.id">
                         <task-list
                             class="pb-2"
                            :tasks="entry.task_subscription"
                            :subscribable_id="entry.id"
                            subscribable_type="App\LogbookEntry">
                         </task-list>
                    </div>
                    <!-- /.tab-pane -->
                    <!-- tab-pane -->
                    <div  v-can="'absence_access'"
                          v-if="displayAbsences()"
                          class="tab-pane "
                          :class="checkLocalStorage('#logbook_'+entry.id, '#logbook_userStatuses_'+entry.id)"
                         v-bind:id="'logbook_userStatuses_'+entry.id"  >
                        <absences
                            class="pb-2"
                            ref="Absences"
                            :entry="entry"
                            :logbook="logbook"
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
            'search': '',
            'first': false
        },
        data() {
            return {
                media: {},
                active: true
            };
        },
        methods: {
            open(modal, relationKey) {
                if (relationKey === 'referenceable'){
                    this.$modal.show(modal, { 'referenceable_type': 'App\\LogbookEntry', 'referenceable_id': this.entry.id });
                } else {
                    this.$modal.show(modal, { 'subscribable_type': 'App\\LogbookEntry', 'subscribable_id': this.entry.id });
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
            displayAbsences(){
                const exists = this.logbook.subscriptions.findIndex(            // Only Show absences on group and course subscriptions
                    subscription => subscription.subscribable_type === "App\\Course" || subscription.subscribable_type === "App\\Group"
                );

                return (exists !== -1);
            },

            loaderEvent: function() {
                this.$refs.Contents.loaderEvent();
            },
            loaderAbsences: function() {
                this.$refs.Absences.loaderEvent();
            },
        },
        computed: {
            isActive: function(){
                if (this.entry.title.toLowerCase().indexOf(this.search.toLowerCase()) === -1){
                    return "display:none";
                } else {
                    return "";
                }
            },
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
