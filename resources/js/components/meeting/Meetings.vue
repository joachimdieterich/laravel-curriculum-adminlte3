<template>
    <div>
        <div class="px-3">
            <ul v-if="typeof (this.subscribable_type) == 'undefined' && typeof(this.subscribable_id) == 'undefined'"
                class="nav nav-pills py-2" role="tablist">
                <li class="nav-item">
                    <a class="nav-link "
                       :class="filter === 'all' ? 'active' : ''"
                       id="curriculum-filter-all"
                       @click="setFilter('all')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fas fa-th pe-2"></i>
                        {{ trans('global.all') }} {{ trans('global.meeting.title') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'by_organization' ? 'active' : ''"
                       id="custom-filter-by-organization"
                       @click="setFilter('by_organization')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fas fa-university pe-2"></i>
                        {{ trans('global.my') }} {{ trans('global.organization.title_singular') }}
                    </a>
                </li>
                <li v-permission="'curriculum_create'"
                    class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'owner' ? 'active' : ''"
                       id="custom-filter-owner"
                       @click="setFilter('owner')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fa fa-user pe-2"></i>
                        {{ trans('global.my') }} {{ trans('global.meeting.title') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'shared_with_me' ? 'active' : ''"
                       id="custom-filter-shared-with-me"
                       @click="setFilter('shared_with_me')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fa fa-paper-plane pe-2"></i>
                        {{ trans('global.shared_with_me') }}
                    </a>
                </li>
                <li v-permission="'curriculum_create'"
                    class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'shared_by_me' ? 'active' : ''"
                       id="custom-tabs-shared-by-me"
                       @click="setFilter('shared_by_me')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fa fa-share-nodes  pe-2"></i>
                        {{ trans('global.shared_by_me') }}
                    </a>
                </li>
            </ul>
        </div>

        <div class="px-3">
            <IndexWidget
                v-permission="'meeting_create'"
                key="meetingCreate"
                modelName="Meeting"
                url="/meetings"
                :create=true
                :label="trans('global.meeting.' + create_label_field)"
            >
                <template #itemIcon>
                    <i v-if="create_label_field == 'enrol'"
                       class="fa fa-2x fa-link text-muted"
                    ></i>
                </template>
            </IndexWidget>
            <IndexWidget v-for="meeting in meetings"
                :key="'meetingIndex'+meeting.id"
                :model="meeting"
                modelName="Meeting"
                url="/meetings"
            >
                <template #icon>
                    <i class="fa fa-meeting-location-dot pt-2"></i>
                </template>

                <template #dropdown
                    v-permission="'meeting_edit, meeting_delete'"
                >
                    <div class="dropdown-menu dropdown-menu-end">
                        <button
                            v-permission="'meeting_edit'"
                            type="button"
                            class="dropdown-item"
                            @click="editMeeting(meeting)"
                        >
                            <i class="fa fa-pencil-alt"></i>
                            {{ trans('global.meeting.edit') }}
                        </button>
                        <button v-if="meeting.allow_copy"
                            typ="button"
                            class="dropdown-item"
                            @click="confirmMeetingCopy(meeting)"
                        >
                            <i class="fa fa-copy"></i>
                            {{ trans('global.meeting.copy') }}
                        </button>

                        <hr class="my-1">

                        <button
                            v-permission="'meeting_delete'"
                            type="submit"
                            class="dropdown-item text-danger"
                            @click="confirmItemDelete(meeting)"
                        >
                            <span v-if="create_label_field == 'enrol'">
                                <i class="fa fa-unlink"></i>
                                {{ trans('global.meeting.expel') }}
                            </span>
                            <span v-else>
                                <i class="fa fa-trash"></i>
                                {{ trans('global.meeting.delete') }}
                            </span>
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <DataTable
            ref="datatable"
            id="meeting-datatable"
            :columns="columns"
            :options="$dtOptions"
            :ajax="url"
            class="d-none"
            @xhr="(e, settings, json) => meetings = json.data"
        />

        <Teleport to="body">
            <MeetingModal/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.meeting.' + delete_label_field)"
                :description="trans('global.meeting.' + delete_label_field + '_helper')"
                @close="showConfirm = false"
                @confirm="() => {
                    showConfirm = false;
                    destroy();
                }"
            />
            <ConfirmModal
                :showConfirm="showCopy"
                :title="trans('global.meeting.copy')"
                :description="trans('global.meeting.copy_helper')"
                css='primary'
                @close="showCopy = false"
                @confirm="() => {
                    showCopy = false;
                    copy();
                }"
            />
        </Teleport>
    </div>
</template>
<script>
import MeetingModal from "../meeting/MeetingModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
DataTable.use(DataTablesCore);

export default {
    components: {
        ConfirmModal,
        DataTable,
        MeetingModal,
        IndexWidget
    },
    props: {
        subscribable: {
            type: Boolean,
            default: false
        },
        create_label_field: {
            type: String,
            default: 'create'
        },
        delete_label_field: {
            type: String,
            default: 'delete'
        },
        subscribable_type: '',
        subscribable_id: '',
    },
    data() {
        return {
            component_id: this.$.uid,
            meetings: null,
            showConfirm: false,
            showCopy: false,
            url: '/meetings/list',
            currentMeeting: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'uid', data: 'uid' },
                { title: 'title', data: 'title', searchable: true },
                { title: 'description', data: 'description', searchable: true },
            ],
            filter: 'all',
            dt: null,
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.dt = this.$refs.datatable.dt;

        this.$eventHub.on('meeting-added', (meeting) => {
            this.globalStore?.closeModal('meeting-modal');
            this.meetings.push(meeting);
        });

        this.$eventHub.on('meeting-updated', (meeting) => {
            this.globalStore?.closeModal('meeting-modal');
            this.update(meeting);
        });

        this.$eventHub.on('filter', filter => this.dt.search(filter.searchString).draw());
    },
    methods: {
        setFilter(filter) {
            this.filter = filter;
            if (typeof (this.subscribable_type) !== 'undefined' && typeof(this.subscribable_id) !== 'undefined'){
                this.url = '/meetingSubscriptions?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id
            } else {
                this.url = '/meetings/list?filter=' + this.filter
            }
        },
        editMeeting(meeting) {
            this.globalStore.showModal('meeting-modal', meeting);
        },
        confirmItemDelete(meeting) {
            this.currentMeeting = meeting;
            this.showConfirm = true;
        },
        confirmMeetingCopy(meeting) {
            this.currentMeeting = meeting;
            this.showCopy = true;
        },
        copy() {
            window.location = "/meetings/" + this.currentMeeting.id + "/copy";
        },
        destroy() {
            axios.delete('/meetings/' + this.currentMeeting.id)
                .then(res => {
                    let index = this.meetings.indexOf(this.currentMeeting);
                    this.meetings.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        update(meeting) {
            const index = this.meetings.findIndex(
                vc => vc.id === meeting.id
            );

            for (const [key, value] of Object.entries(meeting)) {
                this.meetings[index][key] = value;
            }
        }
    },
}
</script>