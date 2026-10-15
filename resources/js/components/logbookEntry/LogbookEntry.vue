<template>
    <div
        :id="'logbook-entry-' + entry.id"
        class="flex-column bg-white mb-3 rounded-3 shadow-layout"
        :style="isVisible"
    >
        <div
            class="d-flex p-2"
            data-bs-toggle="collapse"
            :data-bs-target="'#logbook-entry-body-' + entry.id"
            aria-expanded="true"
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
            class="collapse show"
        >
            <hr class="m-1">

            <NavList
                :navs="[
                    { icon: 'fa-info', target: 'logbook-description-' + entry.id, label: 'global.description' },
                    { icon: 'fa-align-justify', target: 'logbook-contents-' + entry.id, label: 'global.content.title', permission: 'content_access' },
                    { icon: 'fa-tasks', target: 'logbook-tasks-' + entry.id, label: 'global.task.title', permission: 'task_access' },
                    { icon: 'fa-photo-video', target: 'logbook-media-' + entry.id, label: 'global.medium.title' },
                    { icon: 'fa-sitemap', target: 'logbook-objectives-' + entry.id, label: 'global.objective_tab' },
                    { icon: 'fa-graduation-cap', target: 'logbook-lms-' + entry.id, label: 'global.lms.title_singular' },
                ]"
                class="px-2"
                style="max-width: calc(100vw - 2rem);"
            />

            <div class="px-1">
                <div class="tab-content mb-1">
                    <div
                        :id="'logbook-description-' + entry.id"
                        class="tab-pane fade show active p-2 p-margin-0"
                        role="tabpanel"
                        :aria-labelledby="'logbook-description-' + entry.id + '-tab'"
                        tabindex="0"
                    >
                        <span v-html="entry.description?.length > 0 ? entry.description : trans('global.no_description')"></span>
                    </div>

                    <div v-if="checkPermission('content_access')"
                        :id="'logbook-contents-' + entry.id"
                        class="tab-pane fade"
                        role="tabpanel"
                        :aria-labelledby="'logbook-content-' + entry.id + '-tab'"
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
                        :aria-labelledby="'logbook-task-' + entry.id + '-tab'"
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
                        role="tabpanel"
                        :aria-labelledby="'logbook-media-' + entry.id + '-tab'"
                        tabindex="0"
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
                        role="tabpanel"
                        :aria-labelledby="'logbook-objectives-' + entry.id + '-tab'"
                        tabindex="0"
                    >
                        <Objectives
                            :referenceable_id="entry.id"
                            referenceable_type="App\LogbookEntry"
                            :owner_id="entry.owner_id"
                            :editable="entry.owner_id == $userId"
                        />
                    </div>

                    <div v-if="checkPermission('lms_access')"
                        :id="'logbook-lms-' + entry.id"
                        class="tab-pane fade"
                        role="tabpanel"
                        :aria-labelledby="'logbook-lms-' + entry.id + '-tab'"
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
import NavList from "../uiElements/NavList.vue";
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import Contents from '../content/Contents.vue';
import Tasks from '../task/Tasks.vue';
import Media from '../media/Media.vue';
import Lms from '../lms/Lms.vue';
import Objectives from "../objectives/Objectives.vue";
import Avatar from "../uiElements/Avatar.vue";

export default {
    components: {
        NavList,
        ConfirmModal,
        Objectives,
        Avatar,
        Media,
        Contents,
        Tasks,
        Lms,
    },
    props: {
        logbook: {
            type: Object,
            default: null,
        },
        entry: {
            type: Object,
            default: null,
        },
        editable: {
            type: Boolean,
            default: false,
        },
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

        this.$eventHub.on('filter', filter => {
            // always case insensitive
            const content = (
                this.$el.firstChild.innerText + ' '
                + this.$el.querySelector('[id^="logbook-description"]').innerText
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
}
</script>
<style scoped>
.badge:not(:hover) {
    background-color: #adb5bd !important;
}
</style>