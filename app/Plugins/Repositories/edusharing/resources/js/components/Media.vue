<template>
    <div>
        <ul
            class="nav nav-tabs px-2"
            role="tablist"
        >
            <li v-if="$userId != 8"
                class="btn btn-sm btn-outline-secondary m-2"
                v-bind:class="[(currentTab === 1) ? 'active' : '']"
                id="edusharing_mediathek-nav"
                data-toggle="pill"
                href="#edusharing_mediathek"
                role="tab"
                aria-controls="edusharing_mediathek"
                aria-selected="true"
                @click="setCurrentTab(1); loader(false);"
            >
                <i class="fa fa-globe"></i>
                {{ trans('global.public_files') }}
            </li>

            <li
                class="btn btn-sm btn-outline-secondary m-2 "
                v-bind:class="[(currentTab === 3) ? 'active' : '']"
                id="edusharing_my_files-nav"
                data-toggle="pill"
                href="#edusharing_my_files"
                role="tab"
                aria-controls="edusharing_my_files"
                aria-selected="true"
                @click="setCurrentTab(3);loader(false)"
            >
                <i class="fa fa-user"></i>
                {{ trans('global.my_files') }}
            </li>
        </ul>
        <div
            id="loading"
            class="overlay text-center w-100"
        >
            <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
            <span class="sr-only">Loading...</span>
        </div>

        <div class="px-2">
            <!-- Add Media -->
            <div v-if="model.curriculum.type_id !== 1"
                v-permission="'external_medium_create, is_teacher'"
                :id="'media-add'"
                class="box box-objective nav-item-box-image pointer my-1 pull-left"
                style="min-width: 200px !important; border-style: solid !important;"
                @click="addMedia()"
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
                class="box box-objective nav-item-box-image pointer my-1 pull-left"
                style="min-width: 200px !important;"
            >
                <a class="text-decoration-none">
                    <RenderUsage :medium="subscription.medium"/>
                    <span>
                        <span class="nav-item-box bg-gray-light text-center overflow-auto p-1">
                            {{ subscription.medium.title ?? subscription.medium.name }}
                        </span>
                    </span>
                    <span v-if="subscription.medium.owner_id == $userId"
                        v-permission="'medium_delete'"
                        class="position-absolute w-100 p-1 pointer"
                        style="top: 0;"
                    >
                        <button
                            id="delete-navigator-item"
                            class="btn btn-danger btn-sm pull-right"
                            type="submit"
                            @click.stop="unlinkMedium(subscription);"
                        >
                            <small><i class="fa fa-unlink"></i></small>
                        </button>
                    </span>
                </a>
            </div>
    
            <!-- Media linked from Edusharing -->
            <div v-for="medium in filteredExternalMedia"
                :id="medium.node_id"
                class="box box-objective nav-item-box-image pointer my-1 pull-left"
                @click="show(medium)"
            >
                <a class="text-decoration-none">
                    <div>
                        <div
                            class="nav-item-box-image-size h-100 w-100"
                            :style="{ 'background-image': 'url(' + href(medium) + ')' }"
                        ></div>
                        <div
                            class="symbol"
                            style="height: 24px;"
                            :style="{ 'background': 'white url(' + iconUrl(medium) + ') no-repeat center', 'background-size': '24px' }"
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
    
            <div v-if="media !== null"
                class="row w-100 pt-1"
            >
                <span v-if="[0].length > maxItems">
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
    </div>
</template>
<script>
import {useGlobalStore} from "../../../../../../../resources/js/store/global.js";
import RenderUsage from "./RenderUsage.vue";

export default {
    props: {
        model: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            component_id: this._uid,
            media: null,
            externalMedia: null,
            externalMyMedia: null,
            commonName: null,
            page: 0,
            maxItems: 50,
            errors: {},
            currentTab: 1,
        }
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore
        }
    },
    methods: {
        async loader(reload = true) {
            $("#loading").show();

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
                $("#loading").hide();
                //this.errors = error.response.data.errors;
            }

            $("#loading").hide();
        },
        async unlinkMedium(mediumSubscription) { //(id, value) { //id of external reference and value in db
            axios.delete('/media/' + mediumSubscription.medium_id, {
                data: {
                    subscribable_type: mediumSubscription.subscribable_type,
                    subscribable_id:   mediumSubscription.subscribable_id,
                },
            })
                .then(res => {
                    this.loader();
                })
                .catch(err => {
                    console.log(err);
                });
        },
        subscribable_type() {
            var reference_class = 'App\\TerminalObjective';
            if (typeof this.model.terminal_objective === 'object') {
                reference_class = 'App\\EnablingObjective';
            }

            return reference_class;
        },
        show(medium) {
            window.open(medium.path, '_blank');
        },
        href(medium) {
            return medium.thumb;
        },
        iconUrl(medium) {
            return medium.iconURL;
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
        setCurrentTab(id){
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
        this.$eventHub.on('medium-added', (e) => {
            if (this.component_id == e.id) {
                this.loader();
            }
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
    components: {
        RenderUsage,
    },
}
</script>