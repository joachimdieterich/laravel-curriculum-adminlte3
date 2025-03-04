<template>
    <div>
        <h1 class="sidebar-header  mb-3">
            {{ this.marker.title }}
            <span
                v-permission="'map_edit'"
                class="card-tools pl-2"
            >
                <a @click="editMarker(marker)" >
                    <i class="fa fa-pencil-alt"></i>
                </a>
                 <a  v-permission="'is_admin'"
                     @click="shareMarker(marker)" >
                     <i class="ml-3 fa fa-share-alt"></i>
                </a>
            </span>
        </h1>
        <div>
            <span v-for="tag in this.tag_array"
                  class="right badge badge-primary mr-2">
                {{ tag }}
            </span>
        </div>

        <h5 class="pt-3">{{ trans('global.marker.fields.author') }}</h5>
        <div>{{ this.marker.author }}</div>

        <h5 class="pt-3">{{ trans('global.description') }}</h5>
        <div class="pb-2" v-html="marker.description"></div>

        <h5 class="pt-3 clearfix">{{ trans('global.medium.title') }}</h5>
        <div v-if="marker.id != null"
            v-permission="'medium_access'"
            v-bind:id="'map_marker_media_' + marker.id"
        >
            <media
                subscribable_type="App\MapMarker"
               :subscribable_id="marker.id"
               format="list"
            />
        </div>

        <h5 class="pt-3">{{ trans('global.address') }}</h5>
        <div v-dompurify-html="marker.address"></div>

        <h5 class="pt-3">{{ trans('global.marker.fields.link') }}</h5>
        <div>
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

        <SubscribableList v-if="marker.id"
            url="/mapMarkerSubscriptions?map_marker_id"
            :model_id="marker.id"
        />

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
            default: null
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            tag_array: {},
            subscribers: {}
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
            this.tag_array = newVal.tags.split(",");
        },
    },
    methods: {
        editMarker(marker){
            this.$eventHub.emit('edit_marker', marker);
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
    mounted() {

    }

}
</script>
