<template>
    <div
        class="d-flex flex-column"
        :style="{ marginTop: subscribable ? null : '-1.5rem' }"
    >
        <ul v-if="!subscribable"
            class="nav nav-pills px-3 py-2"
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
                    <i class="fas fa-video pe-2"></i>
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
                    <i class="fas fa-university pe-2"></i>
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
                    <i class="fa fa-user pe-2"></i>
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
                    <i class="fa fa-paper-plane pe-2"></i>
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
                    <i class="fa fa-share-nodes  pe-2"></i>{{ trans('global.shared_by_me') }}
                </a>
            </li>
        </ul>

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
            <IndexWidget v-for="videoconference in videoconferences"
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

                <template #dropdown
                    v-permission="'videoconference_edit, videoconference_delete'"
                >
                    <div v-if="subscribable"
                        class="dropdown-menu dropdown-menu-end"
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
                            <i class="fa fa-link me-2"></i>
                            {{ trans('global.videoconference.expel') }}
                        </button>
                    </div>
                    <div v-else
                        class="dropdown-menu dropdown-menu-end"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            v-permission="'videoconference_edit'"
                            :name="'edit-videoconference-' + videoconference.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editVideoconference(videoconference)"
                        >
                            <i class="fa fa-pencil-alt me-2"></i>
                            {{ trans('global.videoconference.edit') }}
                        </button>
                        <button v-if="$userId == videoconference.owner_id"
                            v-permission="'videoconference_create'"
                            :name="'edit-videoconference-' + videoconference.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="share(videoconference)"
                        >
                            <i class="fa fa-share-alt me-2"></i>
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
                            <i class="fa fa-trash me-2"></i>
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
            :options="options"
            :ajax="subscribable ? '/videoconferences/list?group_id=' + subscribable_id : '/videoconferences/list'"
            class="d-none"
            @xhr="(e, settings, json) => videoconferences = json.data"
        />

        <Teleport to="body">
            <MediumModal v-if="!subscribable"/>
            <SubscribeModal v-if="!subscribable"/>
            <VideoconferenceModal v-if="!subscribable"/>
            <SubscribeVideoconferenceModal v-if="subscribable"/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.videoconference.' + deleteLabel)"
                :description="trans('global.videoconference.' + deleteLabel + '_helper')"
                @close="showConfirm = false;"
                @confirm="() => {
                    showConfirm = false;
                    destroy();
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
import SubscribeVideoconferenceModal from "./SubscribeVideoconferenceModal.vue";
import { json } from "d3";
DataTable.use(DataTablesCore);

export default {
    components: {
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
                    model_id : this.currentVideoconference.id,
                    subscribable_id : this.subscribable_id,
                    subscribable_type : this.subscribable_type,
                })
                    .then(response => {
                        let index = this.videoconferences.indexOf(this.currentVideoconference);
                        this.videoconferences.splice(index, 1);
                    })
                    .catch(e => {
                        console.log(e);
                    });
            } else {
                axios.delete('/videoconferences/' + this.currentVideoconference.id)
                    .then(response => {
                        let index = this.videoconferences.indexOf(this.currentVideoconference);
                        this.videoconferences.splice(index, 1);
                    })
                    .catch(e => {
                        console.log(e);
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
    },
    computed: {
        createLabel() {
            return this.subscribable ? 'enrol' : 'create';
        },
        deleteLabel() {
            return this.subscribable ? 'expel' : 'delete';
        },
    },
}
</script>