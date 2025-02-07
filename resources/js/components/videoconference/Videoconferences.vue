<template >
    <div class="row">
        <div class="col-md-12 ">
            <ul v-if="!subscribable"
                class="nav nav-pills py-2"
                role="tablist"
            >
                <li class="nav-item pointer">
                    <a
                        id="custom-filter-all"
                        class="nav-link "
                        :class="filter === 'all' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('all')"
                    >
                        <i class="fas fa-video pr-2"></i>
                        {{ trans('global.all') }} {{ trans('global.videoconference.title') }}
                    </a>
                </li>
                <li class="nav-item pointer">
                    <a
                        id="custom-filter-by-organization"
                        class="nav-link"
                        :class="filter === 'by_organization' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('by_organization')"
                    >
                        <i class="fas fa-university pr-2"></i>
                        {{ trans('global.my') }} {{ trans('global.organization.title_singular') }}
                    </a>
                </li>
                <li
                    v-permission="'videoconference_create'"
                    class="nav-item pointer"
                >
                    <a
                        id="custom-filter-owner"
                        class="nav-link"
                        :class="filter === 'owner' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('owner')"
                    >
                        <i class="fa fa-user pr-2"></i>
                        {{ trans('global.my') }} {{ trans('global.videoconference.title') }}
                    </a>
                </li>
                <li class="nav-item pointer">
                    <a
                        id="custom-filter-shared-with-me"
                        class="nav-link"
                        :class="filter === 'shared_with_me' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('shared_with_me')"
                    >
                        <i class="fa fa-paper-plane pr-2"></i>
                        {{ trans('global.shared_with_me') }}
                    </a>
                </li>
                <li
                    v-permission="'videoconference_create'"
                    class="nav-item pointer"
                >
                    <a
                        id="custom-tabs-shared-by-me"
                        class="nav-link"
                        :class="filter === 'shared_by_me' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('shared_by_me')"
                    >
                        <i class="fa fa-share-nodes  pr-2"></i>{{ trans('global.shared_by_me') }}
                    </a>
                </li>
            </ul>
        </div>

        <div
            id="videoconference-content"
            class="col-md-12 m-0"
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
                :label="trans('global.videoconference.' + create_label_field)"
            >
                <template v-slot:itemIcon>
                    <i v-if="create_label_field == 'enrol'"
                        class="fa fa-2x fa-link text-muted"
                    ></i>
                </template>
            </IndexWidget>
            <IndexWidget v-for="videoconference in videoconferences"
                :key="'videoconferenceIndex' + videoconference.id"
                :model="videoconference"
                :color="videoconference.bannerColor"
                titleField="meetingName"
                modelName="Videoconference"
                url="/videoconferences"
            >
                <template v-slot:icon>
                    <i class="fa fa-videoconference-location-dot pt-2"></i>
                </template>

                <template v-slot:dropdown
                    v-permission="'videoconference_edit, videoconference_delete'"
                >
                    <div v-if="subscribable"
                        class="dropdown-menu dropdown-menu-right"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            v-permission="'videoconference_delete'"
                            :id="'delete-videoconference-' + videoconference.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(videoconference)"
                        >
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.videoconference.expel') }}
                        </button>
                    </div>
                    <div v-else
                        class="dropdown-menu dropdown-menu-right"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            v-permission="'videoconference_edit'"
                            :name="'edit-videoconference-' + videoconference.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editVideoconference(videoconference)"
                        >
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.videoconference.edit') }}
                        </button>
                        <button v-if="$userId == videoconference.owner_id"
                            v-permission="'videoconference_create'"
                            :name="'edit-videoconference-' + videoconference.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="share(videoconference)"
                        >
                            <i class="fa fa-share-alt mr-2"></i>
                            {{ trans('global.videoconference.share') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'videoconference_delete'"
                            :id="'delete-videoconference-' + videoconference.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(videoconference)"
                        >
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.videoconference.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <div
            id="videoconference-datatable-wrapper"
            class="w-100 dataTablesWrapper"
        >
            <DataTable
                id="videoconference-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display: none;"
            />
        </div>

        <Teleport to="body">
            <MediumModal v-if="!subscribable"/>
            <SubscribeModal v-if="!subscribable"/>
            <VideoconferenceModal v-if="!subscribable"/>
            <SubscribeVideoconferenceModal v-if="subscribable"/>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.videoconference.' + delete_label_field)"
                :description="trans('global.videoconference.' + delete_label_field + '_helper')"
                @close="this.showConfirm = false;"
                @confirm="() => {
                    this.showConfirm = false;
                    this.destroy();
                }"
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
import {useGlobalStore} from "../../store/global";
import SubscribeVideoconferenceModal from "./SubscribeVideoconferenceModal.vue";
DataTable.use(DataTablesCore);

export default {
    props: {
        subscribable: {
            type: Boolean,
            default: false,
        },
        create_label_field: {
            type: String,
            default: 'create',
        },
        delete_label_field: {
            type: String,
            default: 'delete',
        },
        subscribable_type: '',
        subscribable_id: '',
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
            videoconferences: null,
            search: '',
            showConfirm: false,
            url: (this.subscribable_id) ? '/videoconferences/list?group_id=' + this.subscribable_id : '/videoconferences/list',
            errors: {},
            currentVideoconference: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'meetingID', data: 'meetingID' },
                { title: 'meetingName', data: 'meetingName', searchable: true },
                { title: 'welcomeMessage', data: 'welcomeMessage', searchable: true },
            ],
            options : this.$dtOptions,
            filter: 'all',
            dt: null,
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('videoconference-added', (videoconference) => {
            this.videoconferences.push(videoconference);
        });

        this.$eventHub.on('videoconference-updated', (updatedVideoconference) => {
            let videoconference = this.videoconferences.find(vc => vc.id === updatedVideoconference.id);

            Object.assign(videoconference, updatedVideoconference);
        });

        this.$eventHub.on('videoconference-subscription-added', (vcSubscription) => {
            this.videoconferences.push(vcSubscription.videoconference);
        });

        this.$eventHub.on('filter', (filter) => {
            this.dt.search(filter).draw();
        });
    },
    methods: {
        setFilter(filter) {
            this.filter = filter;
            this.url = '/videoconferences/list?filter=' + this.filter;
            this.dt.ajax.url(this.url).load();
        },
        editVideoconference(videoconference) {
            this.globalStore?.showModal('videoconference-modal', videoconference);
        },
        loaderEvent() {
            this.dt = $('#videoconference-datatable').DataTable();

            this.dt.on('draw.dt', () => {
                this.videoconferences = this.dt.rows({page: 'current'}).data().toArray();

                $('#videoconference-content').insertBefore('#videoconference-datatable-wrapper');
            });
        },
        confirmItemDelete(videoconference) {
            this.currentVideoconference = videoconference;
            this.showConfirm = true;
        },
        destroy() {
            if (this.subscribable) {
                axios.post('/videoconferenceSubscriptions/expel', {
                    model_id : this.currentVideoconference.id,
                    subscribable_id : this.subscribable_id,
                    subscribable_type : this.subscribable_type,
                })
                    .then(response => {
                        let index = this.videoconferences.indexOf(this.currentVideoconference);
                        this.videoconferences.splice(index, 1);
                    })
                    .catch(err => {
                        console.log(err.response);
                    });
            } else {
                axios.delete('/videoconferences/' + this.currentVideoconference.id)
                    .then(response => {
                        let index = this.videoconferences.indexOf(this.currentVideoconference);
                        this.videoconferences.splice(index, 1);
                    })
                    .catch(err => {
                        console.log(err.response);
                    });
            }
        },
        share(videoconference) {
            this.globalStore?.showModal('subscribe-modal', {
                modelId: videoconference.id,
                modelUrl: 'videoconference',
                shareWithUsers: true,
                shareWithGroups: true,
                shareWithOrganizations: true,
                shareWithToken: true,
                canEditCheckbox: true,
            });
        },
    },
    components: {
        SubscribeVideoconferenceModal,
        ConfirmModal,
        MediumModal,
        DataTable,
        VideoconferenceModal,
        IndexWidget,
        SubscribeModal,
    },
}
</script>