<template >
    <div class="row">
        <div class="col-md-12 ">
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
                        <i class="fas fa-th pr-2"></i>{{ trans('global.all') }} {{ trans('global.curriculum.title') }}
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
                        <i class="fas fa-university pr-2"></i>{{ trans('global.my') }} {{ trans('global.organization.title_singular') }}
                    </a>
                </li>
                <li v-can="'curriculum_create'"
                    class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'owner' ? 'active' : ''"
                       id="custom-filter-owner"
                       @click="setFilter('owner')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fa fa-user pr-2"></i>{{ trans('global.my') }} {{ trans('global.curriculum.title') }}
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
                        <i class="fa fa-paper-plane pr-2"></i>{{ trans('global.shared_with_me') }}
                    </a>
                </li>
                <li v-can="'curriculum_create'"
                    class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'shared_by_me' ? 'active' : ''"
                       id="custom-tabs-shared-by-me"
                       @click="setFilter('shared_by_me')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fa fa-share-nodes  pr-2"></i>{{ trans('global.shared_by_me') }}
                    </a>
                </li>
            </ul>
        </div>

        <div id="videoconference-content"
             class="col-md-12 m-0">
            <IndexWidget
                v-permission="'videoconference_create'"
                key="'videoconferenceCreate'"
                modelName="Videoconference"
                url="/videoconferences"
                :create=true
                :createLabel="trans('global.videoconference.create')">
            </IndexWidget>
            <IndexWidget
                v-for="videoconference in videoconferences"
                :key="'videoconferenceIndex'+videoconference.id"
                :model="videoconference"
                :color="videoconference.bannerColor"
                titleField="meetingName"
                modelName= "videoconference"
                url="/videoconferences">
                <template v-slot:icon>
                    <i class="fa fa-videoconference-location-dot pt-2"></i>
                </template>

                <template
                    v-permission="'videoconference_edit, videoconference_delete'"
                    v-slot:dropdown>
                    <div class="dropdown-menu dropdown-menu-right"
                         style="z-index: 1050;"
                         x-placement="left-start">
                        <button
                            v-permission="'videoconference_edit'"
                            :name="'edit-videoconference-' + videoconference.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editVideoconference(videoconference)">
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.videoconference.edit') }}
                        </button>
                        <button
                            v-permission="'videoconference_create'"
                            v-if="$userId == videoconference.owner_id"
                            :name="'edit-videoconference-' + videoconference.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="share(videoconference)">
                            <i class="fa fa-share-alt mr-2"></i>
                            {{ trans('global.videoconference.share') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'videoconference_delete'"
                            :id="'delete-videoconference-' + videoconference.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(videoconference)">
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.videoconference.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div id="videoconference-datatable-wrapper"
             class="w-100 dataTablesWrapper">
            <DataTable
                id="videoconference-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display:none; "
            ></DataTable>

        </div>

        <Teleport to="body">
            <SubscribeVideoconferenceModal
                v-if="subscribable"
            >
            </SubscribeVideoconferenceModal>
            <VideoconferenceModal></VideoconferenceModal>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.videoconference.delete')"
                :description="trans('global.videoconference.delete_helper')"
                @close="this.showConfirm = false;"
                @confirm="() => {
                    this.showConfirm = false;
                    this.destroy();
                }"
            ></ConfirmModal>
            <SubscribeModal></SubscribeModal>
        </Teleport>
    </div>
</template>

<script>
import VideoconferenceModal from "../videoconference/VideoconferenceModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
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
    setup () {
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
                { title: 'meetingName', data: 'meetingName', searchable: true},
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
            if (!this.subscribable) {
                this.globalStore?.closeModal('videoconference-modal');
            } else {
                this.globalStore?.closeModal('subscribe-videoconference-modal');
            }

            this.videoconferences.push(videoconference);
        });

        this.$eventHub.on('videoconference-updated', (videoconference) => {
            this.globalStore?.closeModal('videoconference-modal');
            this.update(videoconference);
        });

        this.$eventHub.on('videoconference-subscription-added', () => {
            this.globalStore?.closeModal('subscribe-videoconference-modal');
            this.loaderEvent();
        });

        this.$eventHub.on('createVideoconference', () => {
            if (!this.subscribable) {
                this.globalStore?.showModal('videoconference-modal', {});
            } else {
                this.globalStore?.showModal('subscribe-videoconference-modal', {
                    'reference': {},
                    'subscribable_type': this.subscribable_type,
                    'subscribable_id': this.subscribable_id,
                });
            }

        });
    },
    methods: {
        setFilter(filter){
            this.filter = filter;
            if (typeof (this.subscribable_type) !== 'undefined' && typeof(this.subscribable_id) !== 'undefined'){
                this.url = '/videoconferenceSubscriptions?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id
            } else {
                this.url = '/videoconferences/list?filter=' + this.filter
            }

            this.dt.ajax.url(this.url).load();
        },
        editVideoconference(videoconference){
            this.globalStore?.showModal('videoconference-modal', videoconference);
        },
        loaderEvent(){
            this.dt = $('#videoconference-datatable').DataTable();

            this.dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.videoconferences = this.dt.rows({page: 'current'}).data().toArray();

                $('#videoconference-content').insertBefore('#videoconference-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                this.dt.search(filter).draw();
            });
        },
        confirmItemDelete(videoconference){
            this.currentVideoconference = videoconference;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/videoconferences/' + this.currentVideoconference.id)
                .then(res => {
                    let index = this.videoconferences.indexOf(this.currentVideoconference);
                    this.videoconferences.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        update(videoconference) {
            const index = this.videoconferences.findIndex(
                vc => vc.id === videoconference.id
            );

            for (const [key, value] of Object.entries(videoconference)) {
                this.videoconferences[index][key] = value;
            }
        },
        share(videoconference){
            this.globalStore?.showModal('subscribe-modal', {
                'modelId': videoconference.id,
                'modelUrl': 'videoconference',
                'shareWithUsers': true,
                'shareWithGroups': true,
                'shareWithOrganizations': true,
                'shareWithToken': true,
                'canEditCheckbox': true
            });
        },
    },
    components: {
        SubscribeVideoconferenceModal,
        ConfirmModal,
        DataTable,
        VideoconferenceModal,
        IndexWidget,
        SubscribeModal
    },
}
</script>
