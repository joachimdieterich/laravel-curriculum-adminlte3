<template>
    <div>
        <div class="sidebar-header d-flex align-items-center pr-0">
            <span class="line-clamp">{{ marker.title }}</span>
            <span v-if="marker.owner_id == $userId || checkPermission('is_admin')"
                v-permission="'map_edit'"
                class="d-flex pull-right ml-auto mr-1"
            >
                <button
                    type="button"
                    class="btn btn-icon-alt mx-1"
                    :title="trans('global.marker.edit')"
                    @click="editMarker(marker)"
                >
                    <i class="fas fa-pencil-alt p-2"></i>
                </button>
            </span>
        </div>
        <div>
            <span v-for="tag in tag_array"
                class="right badge badge-primary mr-2"
            >
                {{ tag }}
            </span>
        </div>

        <div v-if="marker.author">
            <h5 class="pt-3">{{ trans('global.marker.fields.author') }}</h5>
            <div>{{ marker.author }}</div>
        </div>

        <div v-if="marker.description">
            <div class="h5 pt-3 pb-2 m-0">{{ trans('global.description') }}</div>
            <div v-html="marker.description"></div>
        </div>

        <h5 class="pt-3 clearfix">{{ trans('global.medium.title') }}</h5>
        <div v-if="marker.id != null"
            :id="'map_marker_media_' + marker.id"
        >
            <Media
                subscribable_type="App\MapMarker"
                :subscribable_id="marker.id"
                :editable="marker.owner_id == $userId"
                :public="true"
                format="list"
            />
        </div>

        <div v-if="marker.address">
            <h5 class="pt-3">{{ trans('global.address') }}</h5>
            {{ marker.address }}
        </div>

        <div v-if="marker.url">
            <h5 class="pt-3">{{ trans('global.marker.fields.link') }}</h5>
            <a
                :href="marker.url"
                target="_blank"
            >
                <span v-if="marker.url_title">
                    {{ marker.url_title }}
                </span>
                <span v-else>{{ trans('global.marker.fields.link_helper') }}</span>
            </a>
        </div>

        <Teleport to="body">
            <SubscribeModal/>
        </Teleport>
    </div>
</template>
<script>
import Media from '../media/Media.vue';
import tokens from "../subscription/Tokens.vue";
import SubscribeModal from "../subscription/SubscribeModal.vue";
import {useGlobalStore} from "../../store/global.js";

export default {
    name: 'MarkerView',
    components: {
        SubscribeModal,
        tokens,
        Media,
    },
    props: {
        marker: {
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
            tag_array: {},
            subscribers: {},
        }
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    watch: { // reload if context change
        marker: function(newVal, oldVal) {
            this.tag_array = newVal.tags?.split(",");
        },
    },
    methods: {
        editMarker(marker) {
            this.globalStore?.showModal('map-marker-modal', marker);
        },
        shareMarker(marker) {
            this.globalStore?.showModal('subscribe-modal', {
                modelId: this.marker.id,
                modelUrl: 'mapMarker',
                shareWithUsers: true,
                shareWithGroups: true,
                shareWithOrganizations: true,
                shareWithToken: false,
                canEditCheckbox: false,
            });
        },
    },
}
</script>