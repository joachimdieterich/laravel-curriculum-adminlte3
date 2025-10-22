<template>
    <div>
        <h1 class="sidebar-header d-flex mb-3">
            {{ marker.title }}
            <span v-if="marker.owner_id == $userId || checkPermission('is_admin')"
                v-permission="'map_edit'"
                class="card-tools ml-auto pl-2"
            >
                <a @click="editMarker(marker)" >
                    <i class="fa fa-pencil-alt"></i>
                </a>
                <a
                    v-permission="'is_admin'"
                    @click="shareMarker(marker)"
                >
                    <i class="ml-3 fa fa-share-alt"></i>
                </a>
            </span>
        </h1>
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
            <h5 class="pt-3">{{ trans('global.description') }}</h5>
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
                public="true"
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

<!--        <SubscribableList v-if="marker.id"
            url="/mapMarkerSubscriptions?map_marker_id"
            :model_id="marker.id"
        />-->

        <Teleport to="body">
            <SubscribeModal/>
        </Teleport>
    </div>
</template>
<script>
import Media from '../media/Media.vue';
import SubscribableList from "../subscription/SubscribableList.vue";
import tokens from "../subscription/Tokens.vue";
import SubscribeModal from "../subscription/SubscribeModal.vue";
import {useGlobalStore} from "../../store/global.js";

export default {
    name: 'MarkerView',
    components: {
        SubscribeModal,
        tokens,
        SubscribableList,
        Media,
    },
    props: {
        marker: {
            type: Object,
            default: null,
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