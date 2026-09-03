<template>
    <div
        :id="'logbook-entry-' + entry.id"
        class="flex-column bg-white mb-3 rounded-3 shadow-layout"
        :style="isVisible"
    >
        <div
            class="d-flex p-2"
            :class="!first && 'collapsed'"
            data-bs-toggle="collapse"
            :data-bs-target="'#logbook-entry-body-' + entry.id"
            :aria-expanded="first"
        >
            <div class="d-flex flex-column flex-fill">
                <span class="d-flex flex-wrap align-items-center">
                    <strong class="me-1">{{ entry.title }}</strong>
                    <i class="fa fa-angle-up d-print-none me-2"></i>
                    <span
                        class="badge text-bg-secondary pointer user-select-none"
                        tabindex="0"
                        data-bs-toggle="collapse"
                        @click.stop="editSubject()"
                        @keyup.enter.space="editSubject()"
                    >
                        <i class="fa fa-book-open"></i>
                        {{ entry.subject?.title ?? trans("global.logbookEntry.no_subject") }}
                    </span>
                </span>
                <span>{{ timePeriod }}</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span v-if="$userId == logbook.owner_id || isEditable"
                    v-permission="'logbook_entry_edit'"
                    style="display: contents;"
                >
                    <button
                        type="button"
                        class="d-print-none btn btn-icon"
                        :title="trans('global.logbookEntry.delete')"
                        data-bs-toggle="collapse"
                        @click="confirmItemDelete()"
                    >
                        <i class="fa fa-trash text-danger"></i>
                    </button>
                    <button
                        type="button"
                        class="d-print-none btn btn-icon text-secondary"
                        :title="trans('global.logbookEntry.edit')"
                        data-bs-toggle="collapse"
                        @click="edit(entry)"
                    >
                        <i class="fa fa-pencil-alt"></i>
                    </button>
                </span>
                <Avatar
                    class="ms-2 contacts-list-img"
                    data-bs-toggle="tooltip"
                    :title="entry.owner.firstname + ' ' + entry.owner.lastname"
                    :firstname="entry.owner.firstname"
                    :lastname="entry.owner.lastname"
                    :medium_id="entry.owner.medium_id"
                    :size="40"
                />
            </div>
        </div>

        <div
            :id="'logbook-entry-body-' + entry.id"
            class="collapse"
            :class="first && 'show'"
        >
            <hr class="m-1">

            <ul class="nav nav-pills align-items-center mx-2">
                <li
                    class="nav-item small"
                    role="presentation"
                >
                    <button
                        :id="'logbook-description-tab-' + entry.id"
                        class="nav-link active"
                        data-bs-toggle="tab"
                        :data-bs-target="'#logbook-description-' + entry.id"
                        type="button"
                        role="tab"
                        aria-controls="'logbook-description-' + entry.id"
                        aria-selected="true"
                    >
                        <i class="fa fa-info p-0"></i>
                        <span v-if="help" class="ps-2">{{ trans('global.logbook.fields.description') }}</span>
                    </button>
                </li>

                <li v-if="checkPermission('content_access')"
                    class="nav-item small"
                    role="presentation"
                >
                    <button
                        :id="'logbook-contents-tab-' + entry.id"
                        :href="'#logbook-contents-' + entry.id"
                        class="nav-link"
                        data-bs-toggle="tab"
                        type="button"
                        role="tab"
                        @click="loaderEvent()"
                    >
                        <i class="fa fa-align-justify pe-1"></i>
                        <span v-if="help">{{ trans('global.content.title') }}</span>
                    </button>
                </li>

                <li v-if="checkPermission('task_access')"
                    class="nav-item small"
                    role="presentation"
                >
                    <button
                        :href="'#logbook-tasks-' + entry.id"
                        class="nav-link"
                        data-bs-toggle="tab"
                        type="button"
                        role="tab"
                    >
                        <i class="fa fa-tasks pe-1"></i>
                        <span v-if="help">{{ trans('global.task.title') }}</span>
                    </button>
                </li>

                <li v-if="checkPermission('medium_access')"
                    class="nav-item small"
                    role="presentation"
                >
                    <button
                        :href="'#logbook-media-' + entry.id"
                        class="nav-link"
                        data-bs-toggle="tab"
                        type="button"
                        role="tab"
                    >
                        <i class="fa fa-photo-video pe-1"></i>
                        <span v-if="help">{{ trans('global.medium.title') }}</span>
                    </button>
                </li>

                <li v-if="checkPermission('achievement_create_self_assessment')"
                    class="nav-item small"
                    role="presentation"
                >
                    <button
                        :href="'#logbook-objectives-' + entry.id"
                        class="nav-link"
                        data-bs-toggle="tab"
                        type="button"
                        role="tab"
                    >
                        <i class="fa fa-sitemap pe-1"></i>
                        <span v-if="help">
                            {{ trans('global.terminalObjective.title') }}/{{ trans('global.enablingObjective.title') }}
                        </span>
                    </button>
                </li>

                <li v-if="displayAbsences()"
                    class="nav-item small"
                    role="presentation"
                >
                    <button
                        :id="'logbook-absence-tab-' + entry.id"
                        class="nav-link"
                        data-bs-toggle="tab"
                        :data-bs-target="'#logbook-absence-' + entry.id"
                        type="button"
                        role="tab"
                        @click="loaderAbsences()"
                    >
                        <i class="fa fa-users-slash pe-1"></i>
                        <span v-if="help">{{ trans('global.absences.title') }}</span>
                    </button>
                </li>

                <li v-if="checkPermission('lms_access')"
                    class="nav-item"
                    role="presentation"
                >
                    <button
                        :href="'#lms-' + entry.id"
                        class="nav-link small link-muted"
                        data-bs-toggle="tab"
                        type="button"
                        role="tab"
                        @click="loadLmsPlugin()"
                    >
                        <i class="fa fa-graduation-cap pe-1"></i>
                        <span v-if="help">{{ trans('global.lms.title_singular') }}</span>
                    </button>
                </li>

                <button
                    class="d-print-none btn btn-icon text-secondary ms-auto"
                    @click="help = !help"
                >
                    <i class="fa fa-question"></i>
                </button>
            </ul>

            <hr class="m-1">

            <div class="px-1">
                <div class="tab-content mb-1">
                    <div
                        :id="'logbook-description-' + entry.id"
                        class="tab-pane fade show active p-2 p-margin-0"
                        role="tabpanel"
                        :aria-labelledby="'logbook-description-tab-' + entry.id"
                        tabindex="0"
                    >
                        <span v-html="entry.description?.length > 0 ? entry.description : trans('global.no_description')"></span>
                    </div>

                    <div v-if="checkPermission('content_access')"
                        :id="'logbook-contents-' + entry.id"
                        class="tab-pane fade"
                        role="tabpanel"
                        :aria-labelledby="'logbook-content-tab-' + entry.id"
                        tabindex="0"
                    >
                        <Contents
                            ref="Contents"
                            subscribable_type="App\LogbookEntry"
                            :subscribable_id="entry.id"
                        />
                    </div>

                    <div v-if="checkPermission('task_access')"
                        :id="'logbook-tasks-' + entry.id"
                        class="tab-pane fade"
                        role="tabpanel"
                        :aria-labelledby="'logbook-task-tab-' + entry.id"
                        tabindex="0"
                    >
                        <Tasks
                            :subscribable_id="entry.id"
                            subscribable_type="App\LogbookEntry"
                        />
                    </div>

                    <div v-if="checkPermission('medium_access')"
                        :id="'logbook-media-' + entry.id"
                        class="tab-pane fade"
                    >
                        <media
                            :subscribable_id="entry.id"
                            subscribable_type="App\LogbookEntry"
                            format="list"
                        />
                    </div>

                    <div v-if="checkPermission('achievement_create_self_assessment')"
                        :id="'logbook-objectives-' + entry.id"
                        class="tab-pane fade"
                    >
                        <Objectives
                            :referenceable_id="entry.id"
                            referenceable_type="App\LogbookEntry"
                            :owner_id="entry.owner_id"
                            :editable="entry.owner_id == $userId"
                        />
                    </div>

                    <div v-if="displayAbsences()"
                        :id="'logbook-absence-' + entry.id"
                        class="tab-pane fade"
                        role="tabpanel"
                        :aria-labelledby="'logbook-absence-tab-' + entry.id"
                        tabindex="0"
                    >
                        <absences
                            ref="Absences"
                            :subscribable_id="entry.id"
                            :subscribable_type="'App\\LogbookEntry'"
                            :entry="entry"
                            :logbook="logbook"
                        />
                    </div>
                    <div v-if="checkPermission('lms_access')"
                        :id="'lms-' + entry.id"
                        class="tab-pane fade"
                        role="tabpanel"
                        :aria-labelledby="'lms-tab-' + entry.id"
                        tabindex="0"
                    >
                        <Lms
                            ref="LmsPlugin"
                            :editable="entry.owner_id == $userId || logbook.owner_id == $userId"
                            :referenceable_id="entry.id"
                            :referenceable_type="'App\\LogbookEntry'"
                        />
                    </div>
                </div>
            </div>
        </div>
        <Teleport to="body">
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.logbookEntry.delete')"
                :description="trans('global.logbookEntry.delete_helper')"
                @close="showConfirm = false"
                @confirm="() => {
                    showConfirm = false;
                    destroy(this.entry);
                }"
            />
        </Teleport>
    </div>
</template>
<script>
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import Absences from '../absence/Absences.vue';
import Contents from '../content/Contents.vue';
import Tasks from '../task/Tasks.vue';
import Media from '../media/Media.vue';
import Lms from '../lms/Lms.vue';
import Objectives from "../objectives/Objectives.vue";
import Avatar from "../uiElements/Avatar.vue";
import {useGlobalStore} from "../../store/global";

export default {
    props: {
        logbook: Object,
        entry: Object,
        first: false,
        editable: false,
    },
    setup() {
        return {
            globalStore: useGlobalStore(),
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            media: {},
            active: true,
            timePeriod: '',
            help: true,
            isEditable: this.editable,
            showConfirm: false,
            search: '',
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
                    id: this.entry.id,
                    subject_id: this.entry.subject_id,
                });
        },
        confirmItemDelete() {
            this.showConfirm = true;
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
            const exists = this.logbook.subscriptions.findIndex(
                subscription => subscription.subscribable_type === "App\\User" && subscription.subscribable_id == this.$userId && subscription.editable === 1
            );

            return (exists !== -1);
        },
        isEditableForGroup() {
            const exists = this.logbook.subscriptions.findIndex(
                subscription => subscription.subscribable_type === "App\\Group" && subscription.editable === 1
            );

            return (exists !== -1);
        },
        isEditableForOrganization() {
            const exists = this.logbook.subscriptions.findIndex(
                subscription => subscription.subscribable_type === "App\\Organization" && subscription.editable === 1
            );

            return (exists !== -1);
        },
        displayAbsences() {
            if (!this.checkPermission('absence_access')) return false;
            // Only Show absences on group and course subscriptions
            const exists = this.logbook.subscriptions.findIndex(
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
            const search = filter.searchString.toLowerCase();

            this.$el.style.display = content.includes(search)
                ? 'flex'
                : 'none';
        });

        this.postDate();
    },
    computed: {
        isVisible: function () {
            return this.entry.title.toLowerCase().indexOf(this.search.toLowerCase()) === -1
                ? "display: none"
                : "display: flex";
        },
    },
    watch: {
        'entry.begin': function() { this.postDate(); },
        'entry.end': function() { this.postDate(); },
    },
    components: {
        ConfirmModal,
        Objectives,
        Absences,
        Avatar,
        Media,
        Contents,
        Tasks,
        Lms,
    }
}
</script>
<style scoped>
.badge:not(:hover) {
    background-color: #adb5bd !important;
}
</style>