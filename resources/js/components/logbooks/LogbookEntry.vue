<template>
    <div :id="'#logbook_'+entry.id"
         class="card col-12"
         :class="{'collapsed-card' : first === false}"
         :style="isActive">
        <div class="user-block p-2 pointer"
             data-card-widget="collapse"
             :data-target="'#logbook_body_'+entry.id"
             aria-expanded="true">

            <span class="username ml-0">
                <avatar class="pull-right ml-2 contacts-list-img"
                        data-toggle="tooltip"
                        :title="entry.owner.firstname + ' ' + entry.owner.lastname"
                        :firstname="entry.owner.firstname"
                        :lastname="entry.owner.lastname"
                        :medium_id="entry.owner.medium_id"
                        :size="40"
                ></avatar>
                <span class="pull-right "
                      v-permission="'logbook_entry_edit'"
                      v-if="this.$userId == logbook.owner_id || editable === true"
                >
<!--                    <button
                        type="button"
                        class="btn btn-tool pt-3"
                        @click.stop="print()">
                        <i class="fa fa-print "></i>
                    </button>-->
                    <button
                        type="button"
                        class="btn btn-tool pt-3"
                        @click.stop="destroy()">
                        <i class="fa fa-trash "></i>
                    </button>
                    <button
                         type="button"
                         class="btn btn-tool pt-3"
                         @click.stop="edit()">
                        <i class="fa fa-pencil-alt "></i>
                    </button>
                </span>
                <span >{{ entry.title }}</span>
                <span class="description ml-0 ">{{ postDate() }}</span>
            </span>

        </div>

        <div class="card-body p-0"
             :class="{'collapse' : first === false}"
             :id="'#logbook_body_'+entry.id" >
            <hr class="m-1">
            <span class="clearfix"></span>

            <ul class="nav nav-pills">
                <li class="nav-item small"
                    @click="setLocalStorage('#logbook_'+entry.id, '#logbook_description_'+entry.id)">
                    <a class="nav-link show"
                       :class="checkLocalStorage('#logbook_'+entry.id, '#logbook_description_'+entry.id, 'active', true)"
                       v-bind:href="'#logbook_description_'+entry.id"
                       data-toggle="tab">
                        <i class="fa fa-info pr-1"></i>
                        <span v-if="help">{{ trans('global.logbook.fields.description') }}</span>
                    </a>
                </li>
                <li v-permission="'content_access'"
                    class="nav-item small"
                    @click="setLocalStorage('#logbook_'+entry.id, '#logbook_contents_'+entry.id)">
                    <a class="nav-link"
                       :class="checkLocalStorage('#logbook_'+entry.id, '#logbook_contents_'+entry.id)"
                       v-bind:href="'#logbook_contents_'+entry.id"
                       data-toggle="tab"
                       @click="loaderEvent()">
                        <i class="fa fa-align-justify pr-1"></i>
                        <span v-if="help">{{ trans('global.content.title') }}</span>
                    </a>
                </li>
                <li v-permission="'task_access'"
                    class="nav-item small"

                    @click="setLocalStorage('#logbook_'+entry.id, '#logbook_tasks_'+entry.id)">
                    <a class="nav-link"
                       :class="checkLocalStorage('#logbook_'+entry.id, '#logbook_tasks_'+entry.id)"
                       v-bind:href="'#logbook_tasks_'+entry.id"
                       data-toggle="tab">
                        <i class="fa fa-tasks pr-1"></i>
                        <span v-if="help">{{ trans('global.task.title') }}</span>
                    </a>
                </li>
                <li class="nav-item small"
                    v-permission="'medium_access'"
                    @click="setLocalStorage('#logbook_'+entry.id, '#logbook_media_'+entry.id)">
                    <a class="nav-link"
                       :class="checkLocalStorage('#logbook_'+entry.id, '#logbook_media_'+entry.id)"
                       v-bind:href="'#logbook_media_'+entry.id"
                       data-toggle="tab">
                        <i class="fa fa-photo-video pr-1"></i>
                        <span v-if="help">{{ trans('global.media.title') }}</span>
                    </a>
                </li>
                <li class="nav-item small"
                    v-permission="'reference_access'"
                    @click="setLocalStorage('#logbook_'+entry.id, '#logbook_objectives_'+entry.id)">
                    <a class="nav-link"
                       :class="checkLocalStorage('#logbook_'+entry.id, '#logbook_objectives_'+entry.id)"
                       v-bind:href="'#logbook_objectives_'+entry.id"
                       data-toggle="tab">
                        <i class="fa fa-sitemap pr-1"></i>
                        <span v-if="help">{{
                                trans('global.terminalObjective.title')
                            }}/{{ trans('global.enablingObjective.title') }}</span>
                    </a>
                </li>

                <li v-permission="'absence_access'"
                    v-if="displayAbsences()"
                    class="nav-item small"
                    @click="setLocalStorage('#logbook_'+entry.id, '#logbook_userStatuses_'+entry.id)">
                    <a class="nav-link"
                       :class="checkLocalStorage('#logbook_'+entry.id, '#logbook_userStatuses_'+entry.id)"
                       v-bind:href="'#logbook_userStatuses_'+entry.id"
                       data-toggle="tab"
                       @click="loaderAbsences()">
                        <i class="fa fa-users-slash pr-1"></i>
                        <span v-if="help">{{ trans('global.absences.title') }}</span>
                    </a>
                </li>

                <li v-permission="'lms_access'"
                    class="nav-item"
                    @click="setLocalStorage('#logbook_'+entry.id, '#logbook_lms_'+entry.id)">
                    <a class="nav-link small link-muted"
                       :class="checkLocalStorage('#logbook_view_'+entry.id, '#logbook_lms_'+entry.id)"
                       v-bind:href="'#lms_'+entry.id"
                       data-toggle="tab"
                       @click="loadLmsPlugin()">
                        <i class="fa fa-graduation-cap pr-1"></i>
                        <span v-if="help">{{ trans('global.lms.title_singular') }}</span>

                    </a>
                </li>

                <li class="nav-item ml-auto pull-right">
                    <a class="nav-link small link-muted"
                       @click="help = !help">
                        <i class="fa fa-question pr-1"></i>
                    </a>
                </li>
            </ul>
            <span class="clearfix"></span>
            <hr class="m-1">


            <div class="pb-2 px-1">
                <div class="tab-content">
                    <!-- tab-pane -->
                    <div class="tab-pane p-2"
                         :class="checkLocalStorage('#logbook_'+entry.id, '#logbook_description_'+entry.id, 'active', true)"
                         v-bind:id="'logbook_description_'+entry.id">
                        <span v-html="entry.description"></span>
                    </div>

                    <!-- tab-pane -->
                    <div v-permission="'content_access'"
                         class="tab-pane "
                         :class="checkLocalStorage('#logbook_'+entry.id, '#logbook_contents_'+entry.id)"
                         v-bind:id="'logbook_contents_'+entry.id"
                    >
                        <contents
                            class="mb-0"
                            ref="Contents"
                            subscribable_type="App\LogbookEntry"
                            :subscribable_id="entry.id">

                        </contents>
                    </div>
                    <!-- /.tab-pane -->
                    <!-- tab-pane -->
                    <div v-permission="'task_access'"
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
                    <!-- /.tab-pane -->
                    <div v-permission="'medium_access'"
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
                         v-permission="'reference_access'"
                         :class="checkLocalStorage('#logbook_'+entry.id, '#logbook_objectives_'+entry.id)"
                         v-bind:id="'logbook_objectives_'+entry.id">

                        <reference-list
                            subscribable_type="App\LogbookEntry"
                            :subscribable_id="entry.id"
                            :entry="entry"
                        />

                    </div>
                    <!-- /.tab-pane -->

                    <!-- tab-pane -->
                    <div  v-permission="'absence_access'"
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

                    <div v-permission="'lms_access'"
                         class="tab-pane"
                         :class="checkLocalStorage('#logbook_'+entry.id, '#logbook_lms_'+entry.id)"
                         v-bind:id="'lms_'+entry.id">
                        <lms ref="LmsPlugin"
                             :referenceable_type="model"
                             :referenceable_id="entry.id">
                        </lms>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END timeline item -->
</template>

<script>
import Absences from '../absence/Absences';
import Contents from '../content/Contents';
import TaskList from '../uiElements/TaskList';
import Media from '../media/Media';
import Lms from '../../../../app/Plugins/Lms/resources/js/components/Lms';
import ReferenceList from "../reference/ReferenceList";
import Avatar from "../uiElements/Avatar";

export default {
    props: {
        'logbook': Object,
        'entry': Object,
        'search': '',
        'first': false,
        'editable': false,
    },
    data() {
        return {
            media: {},
                active: true,
                model: 'App\\LogbookEntry',
                help: true,
            };
        },
        methods: {
            edit() {
                 this.$modal.show('logbook-entry-modal', { 'id': this.entry.id, 'method': 'patch'});
            },
            async destroy(){
                try {
                    this.location = (await axios.delete('/logbookEntries/' + this.entry.id)).data.message;
                } catch (error) {
                    console.log(error);
                }
                this.$parent.$emit('deleteLogbookEntry', this.entry);
            },

            postDate() {
                var start = new Date(this.entry.begin.replace(/-/g, "/"));
                var end = new Date(this.entry.end.replace(/-/g, "/"));
                var dateFormat = {
                    weekday: 'short',
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                };

                if (start.toDateString() === end.toDateString()) {
                    return start.toLocaleString([], dateFormat) + " - " + end.toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                } else {
                    return start.toLocaleString([], dateFormat) + " - " + end.toLocaleString([], dateFormat);
                }
            },
            isEditableForUser() {

                const exists = this.logbook.subscriptions.findIndex(            // Is editable?
                    subscription => subscription.subscribable_type === "App\\User" && subscription.subscribable_id == this.$userId && subscription.editable === 1
                );
                //console.log('isEditableForUser(): '+exists);
                return (exists !== -1);
            },
            isEditableForGroup() {
                const exists = this.logbook.subscriptions.findIndex(            // Is editable?
                    subscription => subscription.subscribable_type === "App\\Group" && subscription.editable === 1
                );
                //console.log('isEditableForGroup(): '+exists);
                return (exists !== -1);
            },
            isEditableForOrganization() {
                const exists = this.logbook.subscriptions.findIndex(            // Is editable?
                    subscription => subscription.subscribable_type === "App\\Organization" && subscription.editable === 1
                );
                //console.log('isEditableForOrganization(): '+exists);
                return (exists !== -1);
            },

            displayAbsences() {
                const exists = this.logbook.subscriptions.findIndex(            // Only Show absences on group and course subscriptions
                    subscription => subscription.subscribable_type === "App\\Course" || subscription.subscribable_type === "App\\Group"
                );

                return (exists !== -1);
            },

            loaderEvent: function () {
                this.$refs.Contents.loaderEvent();
            },
            loaderAbsences: function () {
                this.$refs.Absences.loaderEvent();
            },
            loadLmsPlugin() {
                this.$refs.LmsPlugin.loaderEvent();
            },
            print() {
                location.href = '/print/LogbookEntry/' + this.entry.id
            }
        },
        mounted() {
            if (this.isEditableForUser() || this.isEditableForGroup() || this.isEditableForOrganization()) {
                this.editable = true;
            }
            //load contents if tab is selected
            if (this.checkLocalStorage('#logbook_' + this.entry.id, '#logbook_contents_' + this.entry.id) == 'active') {
                this.$refs.Contents.loaderEvent();
            }

            //register events
            this.$root.$on('lmsUpdate', () => {
                this.$refs.LmsPlugin.loaderEvent();
            });
        },
        computed: {
            isActive: function () {
                if (this.entry.title.toLowerCase().indexOf(this.search.toLowerCase()) === -1) {
                    return "display:none";
                } else {
                    return "";
                }
            },
        },

        components: {
            ReferenceList,
            Absences,
            Avatar,
            Media,
            Contents,
            TaskList,
            Lms
        }
    }
</script>
