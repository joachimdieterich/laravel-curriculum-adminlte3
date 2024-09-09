<template>
    <div>
        <h1 class="sidebar-header  mb-3">
            {{ this.marker.title }}
            <span v-can="'map_edit'" class="card-tools pl-2">
                <a @click="editMarker(marker)" >
                    <i class="fa fa-pencil-alt"></i>
                </a>
            </span>
        </h1>
        <div>
            <span v-for="tag in this.tag_array"
                  class="right badge badge-primary mr-2">
                {{ tag }}
            </span>
        </div>

        <h5 class="pt-3">{{ trans('global.author') }}</h5>
        <div>{{ this.marker.author }}</div>

        <h5 class="pt-3">{{ trans('global.description') }}</h5>
        <div class="pb-2"
             v-dompurify-html="this.marker.description"></div>

        <h5 class="pt-3">{{ trans('global.medium.title') }}</h5>
        <div v-if="marker.id != null"
             v-permission="'medium_access'"
             v-bind:id="'map_marker_media_'+marker.id">
            <media
                subscribable_type="App\MapMarker"
               :subscribable_id="marker.id"
               format="list"/>
        </div>

        <h5 class="pt-3">{{ trans('global.marker.fields.address') }}</h5>
        <div v-dompurify-html="this.marker.address"></div>

        <h5 class="pt-3">{{ trans('global.marker.fields.link') }}</h5>
        <div>
            <a :href="this.marker.url"
            target="_blank">
                <span v-if="this.marker.url_title">
                    {{ this.marker.url_title }}
                </span>
                <span v-else>Link zum Projekt</span>
            </a>
        </div>
    </div>
</template>
<script>

import Media from '../media/Media';

export default {
    name: 'MarkerView',
    components: {
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
    },

}
</script>
