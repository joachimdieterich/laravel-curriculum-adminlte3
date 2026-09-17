<template>
    <div class="position-relative">
        <div v-if="loading"
            class="overlay"
        >
            <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
            <span class="sr-only">Loading...</span>
        </div>

        <ul
            class="nav nav-tabs px-2"
            role="tablist"
        >
            <li v-if="$userId != 8"
                class="btn btn-sm btn-outline-secondary m-2"
                :class="currentTab === 1 && 'active'"
                id="edusharing_mediathek-nav"
                role="tab"
                :aria-selected="currentTab === 1"
                @click="setCurrentTab(1); loader(false);"
            >
                <i class="fa fa-globe"></i>
                {{ trans('global.public_files') }}
            </li>

            <li
                class="btn btn-sm btn-outline-secondary m-2 "
                :class="currentTab === 3 && 'active'"
                id="edusharing_my_files-nav"
                role="tab"
                :aria-selected="currentTab === 3"
                @click="setCurrentTab(3); loader(false)"
            >
                <i class="fa fa-user"></i>
                {{ trans('global.my_files') }}
            </li>
        </ul>

        <div class="d-flex flex-column flex-lg-row gap-2 p-2">
            <!-- Add Media -->
            <div v-if="model.curriculum.type_id !== 1"
                v-permission="'external_medium_create, is_teacher'"
                class="box box-objective nav-item-box-image pointer m-0"
                style="border-color: #F2F4F5;"
                tabindex="0"
                @click="addMedia()"
                @keyup.enter.space="addMedia()"
            >
                <a>
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="fa fa-2x fa-plus text-muted"></i>
                    </div>
                    <span>
                        <span class="nav-item-box d-flex justify-content-center align-items-center align-items-lg-start bg-gray-light text-center p-1">
                            {{ trans('global.media.link') }}
                        </span>
                    </span>
                </a>
            </div>
    
            <!-- Media uploaded from Curriculum -->
            <div v-for="subscription in filteredMedia"
                class="box box-objective nav-item-box-image pointer m-0"
                :style="{ 'border-color': borderColor(subscription) }"
                tabindex="0"
            >
                <a class="text-decoration-none">
                    <div class="position-relative">
                        <RenderUsage :medium="subscription.medium"/>
                    </div>
                    <span class="bg-white text-center overflow-y-auto p-1">
                        {{ subscription.medium.title ?? subscription.medium.name }}
                        <p class="text-muted small mt-1" v-html="subscription.medium.description"></p>
                    </span>

                    <button v-if="subscription.owner_id == $userId || checkPermission('is_admin')"
                        type="button"
                        class="btn btn-icon position-absolute text-dark"
                        style="top: 0.25rem; right: 0.25rem;"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        @click.stop
                    >
                        <i class="fa fa-ellipsis-v"></i>

                        <div class="dropdown-menu dropdown-menu-end">
                            <button
                                type="button"
                                class="dropdown-item"
                                @click.prevent="edit(subscription);"
                            >
                                <i class="fa fa-pencil"></i>
                                {{ trans('global.medium.edit') }}
                            </button>

                            <hr class="my-1">

                            <button
                                type="button"
                                class="dropdown-item text-danger"
                                @click.prevent="confirmDelete(subscription);"
                            >
                                <i class="fa fa-unlink"></i>
                                {{ trans('global.medium.delete_subscription') }}
                            </button>
                        </div>
                    </button>
                </a>
            </div>
    
            <!-- Media linked from Edusharing -->
            <div v-for="medium in filteredExternalMedia"
                :id="medium.node_id"
                class="box box-objective nav-item-box-image pointer m-0"
                style="border-color: #F2F4F5;"
                @click="show(medium)"
            >
                <a class="text-decoration-none">
                    <div>
                        <div
                            class="nav-item-box-image-size h-100 w-100"
                            :style="{ 'background-image': 'url(' + medium.thumb + ')' }"
                        ></div>
                        <div
                            class="symbol"
                            style="height: 24px;"
                            :style="{ 'background': 'white url(' + medium.iconURL + ') no-repeat center', 'background-size': '24px' }"
                        ></div>
                    </div>
        
                    <span>
                        <span class="nav-item-box bg-gray-light text-center overflow-auto p-1">
                            <h6 class="pt-1 hyphens" v-html="medium.title"></h6>
                            <p class="text-muted small" v-html="medium.description"></p>
                        </span>
                    </span>
                    <span style="position: absolute; bottom: 5px; left: 5px;">
                        <img
                            style="height: 16px;"
                            :src="medium.license.icon"
                        />
                    </span>
                </a>
            </div>
    
            <div v-if="media !== null && [0].length > maxItems"
                class="row w-100 pt-1"
            >
                <span>
                    <span class="col-6">
                        <button
                            type="button"
                            class="btn btn-block btn-primary"
                            :class="page > 0 ? '' : 'disabled'"
                            @click="lastPage()"
                        >
                            <i class="fa fa-arrow-left"></i>
                        </button>
                    </span>
    
                    <span class="col-6">
                        <button
                            type="button"
                            class="btn btn-block btn-primary"
                            :class="media[0].length == maxItems ? '' : 'disabled'"
                            @click="nextPage()"
                        >
                            <i class="fa fa-arrow-right"></i>
                        </button>
                    </span>
                </span>
            </div>
        </div>

        <Teleport to="body">
            <MediumEditModal/>
        </Teleport>
        <ConfirmModal
            :showConfirm="showConfirm"
            :title="trans('global.medium.delete_subscription')"
            :description="trans('global.medium.delete_subscription_helper')"
            css="primary"
            @close="showConfirm = false"
            @confirm="() => {
                showConfirm = false;
                unlinkMedium();
            }"
        />
    </div>
</template>
<script>
import RenderUsage from "./RenderUsage.vue";
import MediumEditModal from "../../../../../../../resources/js/components/media/MediumEditModal.vue";
import ConfirmModal from "../../../../../../../resources/js/components/uiElements/ConfirmModal.vue";

export default {
    components: {
        RenderUsage,
        ConfirmModal,
        MediumEditModal,
    },
    props: {
        model: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            component_id: this._uid,
            loading: true,
            media: null,
            externalMedia: null,
            externalMyMedia: null,
            page: 0,
            maxItems: 50,
            currentTab: 1,
            showConfirm: false,
            currentSubscription: null,
        }
    },
    methods: {
        async loader(reload = true) {
            this.loading = true;

            try {
                // only send request for media once
                if (reload) {
                    axios.get(
                        '/mediumSubscriptions?subscribable_type=' + this.subscribable_type() + '&subscribable_id=' + this.model.id
                    ).then(response => this.media = response.data.message);
                }
                // only send request for external media once, to avoid having the long loading time again when switching between tabs
                // TODO: when adding pagination, this behaviour needs to be reworked
                if (this.currentTab === 1 && this.externalMedia == null) {
                    this.externalMedia = (await axios.get('/repositorySubscriptions/getMedia', {
                        params: {
                            subscribable_type: this.subscribable_type(),
                            subscribable_id: this.model.id,
                            search: this.model.title,
                            page: this.page,
                            maxItems: this.maxItems,
                            repository: 'edusharing',
                            filter: 1,
                        },
                    })).data[0];
                    // external my-media is separated, to avoid having the long loading time multiple times
                } else if (this.currentTab === 3 && this.externalMyMedia == null) {
                    this.externalMyMedia = (await axios.get('/repositorySubscriptions/getMedia', {
                        params: {
                            subscribable_type: this.subscribable_type(),
                            subscribable_id: this.model.id,
                            search: this.model.title,
                            page: this.page,
                            maxItems: this.maxItems,
                            repository: 'edusharing',
                            filter: 3,
                        },
                    })).data[0];
                }
            } catch(error) {
                this.loading = false;
            }

            this.loading = false;
        },
        confirmDelete(mediumSubscription) {
            this.currentSubscription = mediumSubscription;
            this.showConfirm = true;
        },
        async unlinkMedium() {
            const mediumSubscription = this.currentSubscription;

            axios.delete('/media/' + mediumSubscription.medium_id, {
                data: {
                    subscribable_type: mediumSubscription.subscribable_type,
                    subscribable_id:   mediumSubscription.subscribable_id,
                },
            })
                .then(res => this.loader())
                .catch(e => {
                    console.log(e);
                    this.toast.error(this.errorMessage(e));
                });
        },
        subscribable_type() { // can't be computed property
            return typeof this.model.terminal_objective === 'object'
                ? 'App\\EnablingObjective'
                : 'App\\TerminalObjective';
        },
        borderColor(subscription) {
            return subscription.additional_data?.color ?? '#F2F4F5';
        },
        edit(subscription) {
            this.globalStore.showModal('medium-edit-modal', subscription);
        },
        show(medium) {
            window.open(medium.path, '_blank');
        },
        lastPage() {
            this.page = this.page - 1
            if (this.page == -1) {
                this.page = 0;
            } else {
                this.loader();
            }
        },
        nextPage() {
            this.page = this.page + 1;
            this.loader();
        },
        setCurrentTab(id) {
            this.currentTab = id;
        },
        addMedia() {
            this.globalStore?.showModal('medium-modal', {
                show: true,
                subscribeSelected: true,
                subscribable_type: this.subscribable_type(),
                subscribable_id: this.model.id,
                public: this.public,
                callbackId: this.component_id,
            });
        },
    },
    mounted() {
        this.loader();

        this.$eventHub.on('medium-added', e => {
            if (this.component_id == e.id) {
                this.loader();
            }
        });

        this.$eventHub.on('medium-updated', data => {
            let subscription = this.media.find(m => m.medium_id === data.medium.id);
            subscription.additional_data = data.additional_data ?? subscription.additional_data;
            Object.assign(subscription.medium, data.medium);
        });
    },
    computed: {
        filteredMedia: function() {
            return this.currentTab === 1
                ? this.media
                : this.media.filter(subscription => subscription.owner_id == this.$userId);
        },
        filteredExternalMedia: function() {
            return this.currentTab === 1
                ? this.externalMedia
                : this.externalMyMedia;
        },
    },
}
</script>
<style scoped>
@media (max-width: 990px) {
    .box { border-right: 10px solid; }
}
@media (min-width: 991px) {
    .box { border-bottom: 10px solid; }
}
</style>