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
                      v-if="this.$userId == logbook.owner_id || this.isEditable === true"
                >
                    <button
                        type="button"
                        class="btn btn-tool pt-3"
                        @click.stop="print()">
                        <i class="fa fa-print "></i>
                    </button>
                    <button
                        type="button"
                        class="btn btn-tool pt-3"
                        @click.stop="destroy()">
                        <i class="fa fa-trash text-danger"></i>
                    </button>
                    <button
                         type="button"
                         class="btn btn-tool pt-3"
                         @click.stop="edit(entry)">
                        <i class="fa fa-pencil-alt "></i>
                    </button>
                </span>
                <span >{{ entry.title }}</span>
                <span class="description ml-0 ">
                    {{ timePeriod }}
                    <small
                        style="vertical-align: middle;"
                        class="badge badge-secondary"
                        @click.stop="editSubject()">
                        <i class="fa fa-book-open"></i>
                        {{ entry.subject?.title ?? trans("global.logbookEntry.no_subject") }}
                    </small>
                </span>
            </span>

        </div>

        <div class="card-body p-0"
             :class="{'collapse' : first === false}"
             :id="'#logbook_body_'+entry.id" >
            <hr class="m-1">
            <span class="clearfix"></span>

            <ul class="nav nav-pills">
                <li class="nav-item small">
                    <a class="nav-link show active"
                       v-bind:href="'#logbook_description_'+entry.id"
                       data-toggle="tab">
                        <i class="fa fa-info pr-1"></i>
                        <span v-if="help">{{ trans('global.logbook.fields.description') }}</span>
                    </a>
                </li>
                <li v-permission="'content_access'"
                    class="nav-item small">
                    <a class="nav-link"
                       v-bind:href="'#logbook_contents_'+entry.id"
                       data-toggle="tab"
                       @click="loaderEvent()">
                        <i class="fa fa-align-justify pr-1"></i>
                        <span v-if="help">{{ trans('global.content.title') }}</span>
                    </a>
                </li>
                <li v-permission="'task_access'"
                    class="nav-item small">
                    <a class="nav-link"
                       v-bind:href="'#logbook_tasks_'+entry.id"
                       data-toggle="tab">
                        <i class="fa fa-tasks pr-1"></i>
                        <span v-if="help">{{ trans('global.task.title') }}</span>
                    </a>
                </li>
                <li class="nav-item small"
                    v-permission="'medium_access'">
                    <a class="nav-link"
                       v-bind:href="'#logbook_media_'+entry.id"
                       data-toggle="tab">
                        <i class="fa fa-photo-video pr-1"></i>
                        <span v-if="help">{{ trans('global.medium.title') }}</span>
                    </a>
                </li>
                <li class="nav-item small"
                    v-permission="'reference_access'">
                    <a class="nav-link"
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
                    class="nav-item small">
                    <a class="nav-link"
                       v-bind:href="'#logbook_userStatuses_'+entry.id"
                       data-toggle="tab"
                       @click="loaderAbsences()">
                        <i class="fa fa-users-slash pr-1"></i>
                        <span v-if="help">{{ trans('global.absences.title') }}</span>
                    </a>
                </li>

                <li v-permission="'lms_access'"
                    class="nav-item">
                    <a class="nav-link small link-muted"
                       v-bind:href="'#lms_'+entry.id"
                       data-toggle="tab"
                       @click="loadLmsPlugin()">
                        <i class="fa fa-graduation-cap pr-1"></i>
                        <span v-if="help">{{ trans('global.lms.title_singular') }}</span>

                    </a>
                </li>

                <li class="nav-item ml-auto pull-right">
                    <a class="nav-link small link-muted pointer"
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
                    <div class="tab-pane p-2 active"
                         v-bind:id="'logbook_description_'+entry.id"
                    >
                        <span v-if="entry.description"
                              v-dompurify-html="entry.description"></span>
                    </div>

                    <!-- tab-pane -->
                    <div v-permission="'content_access'"
                         class="tab-pane "
                         v-bind:id="'logbook_contents_'+entry.id"
                    >
                        <contents
                            class="mb-0"
                            ref="Contents"
                            subscribable_type="App\LogbookEntry"
                            :subscribable_id="entry.id">
                        </contents>
                    </div>

                    <div v-permission="'task_access'"
                         class="tab-pane"
                         v-bind:id="'logbook_tasks_'+entry.id">
                        <Tasks
                            class="pb-2"
                            :subscribable_id="entry.id"
                            subscribable_type="App\LogbookEntry">
                        </Tasks>
                    </div>

                   <div v-permission="'medium_access'"
                        class="tab-pane"
                        v-bind:id="'logbook_media_'+entry.id">
                       <media subscribable_type="App\LogbookEntry"
                              :subscribable_id="entry.id"
                              format="list">
                       </media>
                   </div>

                     <div class="tab-pane"
                        v-permission="'reference_access'"
                        v-bind:id="'logbook_objectives_'+entry.id">

                       <reference-list
                           subscribable_type="App\LogbookEntry"
                           :subscribable_id="entry.id"
                           :entry="entry"
                       />
                   </div>

                    <div  v-permission="'absence_access'"
                          v-if="displayAbsences()"
                          class="tab-pane "
                          v-bind:id="'logbook_userStatuses_'+entry.id"  >
                        <absences
                            class="pb-2"
                            ref="Absences"
                            :subscribable_id="entry.id"
                            :subscribable_type="model"
                            :entry="entry"
                            :logbook="logbook">
                        </absences>
                    </div>
                    <div v-permission="'lms_access'"
                         class="tab-pane"
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
</template>
<script>
import Absences from '../absence/Absences.vue';
import Contents from '../content/Contents.vue';
import Tasks from '../task/Tasks.vue';
import Media from '../media/Media.vue';
import Lms from '../lms/Lms.vue';
import ReferenceList from "../reference/ReferenceList.vue";
import Avatar from "../uiElements/Avatar.vue";
import {useGlobalStore} from "../../store/global";

export default {
    props: {
        'logbook': Object,
        'entry': Object,
        'search': '',
        'first': false,
        'editable': false,
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            media: {},
            active: true,
            timePeriod: '',
            model: 'App\\LogbookEntry',
            help: true,
            isEditable: this.editable
        };
    },
    methods: {
        edit(entry) {
            this.globalStore?.showModal('logbook-entry-modal', entry);
        },
        editSubject() {
            this.globalStore?.showModal(
                'logbook-entry-subject-modal',
                {
                    'id': this.entry.id,
                    'subject_id': this.entry.subject_id,
                    'title': this.entry.subject?.title
                });
        },
        destroy() {
            axios.delete('/logbookEntries/' + this.entry.id)
                .then(response => this.$eventHub.emit('logbook-entry-deleted', this.entry))
                .catch(error => console.log(error));
        },
        postDate() {
            if (this.entry.begin == undefined || this.entry.end == undefined) {
                this.timePeriod = '';
                return;
            }

            const start = new Date(this.entry.begin.replace(/-/g, "/"));
            const end = new Date(this.entry.end.replace(/-/g, "/"));
            const dateFormat = {
                weekday: 'short',
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };

            if (start.toDateString() === end.toDateString()) {
                this.timePeriod = start.toLocaleString([], dateFormat) + " - " + end.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } else {
                this.timePeriod = start.toLocaleString([], dateFormat) + " - " + end.toLocaleString([], dateFormat);
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
            this.isEditable = true;
        }
        //load contents if tab is selected
        if (this.getGlobalStorage('#logbook_' + this.entry.id, '#logbook_contents_' + this.entry.id) == 'active') {
            this.$refs.Contents.loaderEvent();
        }

        //register events
        this.$eventHub.on('lmsUpdate', () => {
            this.$refs.LmsPlugin.loaderEvent();
        });

        this.$eventHub.on('filter', (filter) => {
            // always case insensitive
            const content = (
                this.$el.querySelector('.username').innerText + ' '
                + this.$el.querySelector('[id^="logbook_description"]').innerText
            ).toLowerCase();
            const search = filter.toLowerCase();

            this.$el.style.display = content.includes(search)
                ? 'flex'
                : 'none';
        });

        this.postDate();
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
    watch: {
        'entry.begin': function() { this.postDate(); },
        'entry.end': function() { this.postDate(); },
    },
    components: {
        ReferenceList,
        Absences,
        Avatar,
        Media,
        Contents,
        Tasks,
        Lms
    }
}
</script>
<style scoped>
.badge-secondary:not(:hover) {
    background-color: #adb5bd;
}
</style>
