<template>
    <div
        class="d-flex flex-column"
        :style="{ marginTop: subscribable ? null : '-1.5rem' }"
    >
        <TabList
            class="px-3"
            model="curriculum"
            :tabs="[
                { icon: 'fa-heart', label: 'global.tag.favourite.plural', filter: 'favourite' },
                { icon: 'fa-video', label: ['global.all', 'global.videoconference.title'], filter: 'all' },
                { icon: 'fa-university', label: ['global.my', 'global.organization.title_singular'], filter: 'by_organization' },
                { icon: 'fa-user', label: ['global.my', 'global.videoconference.title'], filter: 'owner', permission: 'curriculum_create' },
                { icon: 'fa-paper-plane', label: 'global.shared_by_me', filter: 'shared_by_me', permission: 'videoconference_create' },
                { icon: 'fa-share-nodes', label: 'global.shared_with_me', filter: 'shared_with_me' },
                { icon: 'fa-eye-slash', label: 'global.tag.hidden.singular', filter: 'hidden' },
            ]"
            @change-tab="setFilter($event.filter)"
        />

        <div
            id="videoconference-content"
            class="px-3"
        >
            <IndexWidget
                v-permission="'videoconference_create'"
                key="videoconferenceCreate"
                modelName="Videoconference"
                url="/videoconferences"
                :create="!subscribable"
                :subscribe="subscribable"
                :subscribable_id="subscribable_id"
                :subscribable_type="subscribable_type"
                :label="trans('global.videoconference.' + createLabel)"
            >
                <template #itemIcon>
                    <i v-if="subscribable"
                       class="fa fa-2x fa-link text-muted"
                    ></i>
                </template>
            </IndexWidget>
            <IndexWidget v-for="(videoconference, index) in videoconferences"
                         :key="'videoconferenceIndex' + videoconference.id"
                         :model="videoconference"
                         :color="videoconference.bannerColor"
                         titleField="meetingName"
                         modelName="Videoconference"
                         url="/videoconferences"
                         :showSubscribable="subscribable"
            >
                <template #itemIcon>
                    <i class="fa fa-2x fa-video"></i>
                </template>

                <template #dropdown>
                    <div v-if="subscribable"
                         class="dropdown-menu dropdown-menu-end"
                         v-permission="'videoconference_edit, videoconference_delete'"
                    >
                        <button
                            v-permission="'videoconference_delete'"
                            type="submit"
                            class="dropdown-item text-danger"
                            @click="confirmItemDelete(videoconference)"
                        >
                            <i class="fa fa-link"></i>
                            {{ trans('global.videoconference.expel') }}
                        </button>
                    </div>
                    <div v-else
                         class="dropdown-menu dropdown-menu-end"
                    >
                        <button
                            v-if="ownerOrAdmin(videoconference)"
                            v-permission="'videoconference_edit, videoconference_delete'"
                            type="button"
                            class="dropdown-item"
                            @click.prevent="editVideoconference(videoconference)"
                        >
                            <i class="fa fa-pencil-alt"></i>
                            {{ trans('global.videoconference.edit') }}
                        </button>
                        <button
                            v-if="ownerOrAdmin(videoconference)"
                            v-permission="'videoconference_edit, videoconference_delete'"
                            type="button"
                            class="dropdown-item"
                            @click.prevent="share(videoconference)"
                        >
                            <i class="fa fa-share-alt"></i>
                            {{ trans('global.videoconference.share') }}
                        </button>

                        <Hide v-if="filter === 'shared_with_me' || filter === 'all' || filter === 'hidden'"
                              url="/videoconferences/[id]/hide"
                              :model="videoconference"
                              :is-hidden="videoconference.is_hidden"
                              @mark-status-changed="() => videoconference.splice(index, 1)"
                        />

                        <hr v-if="ownerOrAdmin(videoconference)" v-permission="'videoconference_edit, videoconference_delete'" class="my-1">

                        <button
                            v-if="ownerOrAdmin(videoconference)"
                            v-permission="'videoconference_delete'"
                            type="submit"
                            class="dropdown-item text-danger"
                            @click="confirmItemDelete(videoconference)"
                        >
                            <i class="fa fa-trash"></i>
                            {{ trans('global.videoconference.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <DataTable
            ref="datatable"
            id="videoconference-datatable"
            :columns="columns"
            :options="$dtOptions"
            :ajax="subscribable ? '/videoconferences/list?group_id=' + subscribable_id : '/videoconferences/list'"
            class="d-none"
            @xhr="xhrEvent"
        />

        <Teleport to="body">
            <MediumModal v-if="!subscribable"/>
            <SubscribeModal v-if="!subscribable"/>
            <VideoconferenceModal v-if="!subscribable"/>
            <SubscribeVideoconferenceModal v-if="subscribable" :group_id="subscribable_id"/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.videoconference.' + deleteLabel)"
                :description="trans('global.videoconference.' + deleteLabel + '_helper')"
                @close="showConfirm = false;"
                @confirm="destroy()"
            />
        </Teleport>
    </div>
</template>
<script>
import VideoconferenceModal from "../videoconference/VideoconferenceModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import MediumModal from "../media/MediumModal.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import SubscribeModal from "../subscription/SubscribeModal.vue";
import SubscribeVideoconferenceModal from "./SubscribeVideoconferenceModal.vue";
import TabList from "../uiElements/TabList.vue";
import Hide from "../tag/Hide.vue";

DataTable.use(DataTablesCore);

export default {
    components: {
        Hide,
        TabList,
        SubscribeVideoconferenceModal,
        ConfirmModal,
        MediumModal,
        DataTable,
        VideoconferenceModal,
        IndexWidget,
        SubscribeModal,
    },
    props: {
        subscribable: {
            type: Boolean,
            default: false,
        },
        subscribable_type: {
            type: String,
            default: null,
        },
        subscribable_id: {
            type: Number,
            default: null,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            videoconferences: null,
            showConfirm: false,
            currentVideoconference: {},
            columns: [
                {title: 'id', data: 'id'},
                {title: 'meetingID', data: 'meetingID'},
                {title: 'meetingName', data: 'meetingName', searchable: true},
                {title: 'welcomeMessage', data: 'welcomeMessage', searchable: true},
            ],
            filter: 'favourite',
            dt: null,
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.dt = this.$refs.datatable.dt;

        this.$eventHub.on('videoconference-added', videoconference => {
            this.videoconferences.push(videoconference);
        });

        this.$eventHub.on('videoconference-updated', updatedVideoconference => {
            let videoconference = this.videoconferences.find(vc => vc.id === updatedVideoconference.id);

            Object.assign(videoconference, updatedVideoconference);
        });

        this.$eventHub.on('videoconference-subscription-added', vcSubscription => {
            this.videoconferences.push(vcSubscription.videoconference);
        });

        this.$eventHub.on('filter', filter => {
            this.dt.search(filter.searchString).draw();
        });
    },
    methods: {
        xhrEvent(_e, _settings, json) {
            // if user doesn't have any favourited objects, default to 'all'-tab
            if (!this.subscribable && json.draw === 1 && json.data.length === 0) {
                this.setFilter('all');
                return;
            }
            this.videoconferences = json.data;
        },
        setFilter(filter) {
            this.filter = filter;
            this.dt.ajax.url('/videoconferences/list?filter=' + this.filter).load();
        },
        editVideoconference(videoconference) {
            this.globalStore.showModal('videoconference-modal', videoconference);
        },
        confirmItemDelete(videoconference) {
            this.currentVideoconference = videoconference;
            this.showConfirm = true;
        },
        destroy() {
            if (this.subscribable) {
                axios.post('/videoconferenceSubscriptions/expel', {
                    videoconference_id: this.currentVideoconference.id,
                    subscribable_id: this.subscribable_id,
                    subscribable_type: this.subscribable_type,
                })
                    .then(response => {
                        this.showConfirm = false;
                        let index = this.videoconferences.indexOf(this.currentVideoconference);
                        this.videoconferences.splice(index, 1);
                    })
                    .catch(e => {
                        console.log(e);
                        this.showConfirm = false;
                        this.toast.error(this.errorMessage(e));
                    });
            } else {
                axios.delete('/videoconferences/' + this.currentVideoconference.id)
                    .then(response => {
                        this.showConfirm = false;
                        let index = this.videoconferences.indexOf(this.currentVideoconference);
                        this.videoconferences.splice(index, 1);
                    })
                    .catch(e => {
                        console.log(e);
                        this.showConfirm = false;
                        this.toast.error(this.errorMessage(e));
                    });
            }
        },
        share(videoconference) {
            this.globalStore.showModal('subscribe-modal', {
                modelId: videoconference.id,
                modelUrl: 'videoconference',
                shareWithUsers: true,
                shareWithGroups: true,
                shareWithOrganizations: true,
                shareWithToken: true,
                canEditCheckbox: true,
            });
        },
        ownerOrAdmin(videoconference) {
            return videoconference.owner_id == this.$userId || this.isAdmin;
        },
    },
    computed: {
        isAdmin() {
            return this.checkPermission('is_admin');
        },
        createLabel() {
            return this.subscribable ? 'enrol' : 'create';
        },
        deleteLabel() {
            return this.subscribable ? 'expel' : 'delete';
        },
    },
}
</script>