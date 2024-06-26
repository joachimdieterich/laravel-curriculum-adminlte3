<template>
    <div>
        <h1 class="sidebar-header  mb-3">
            {{ marker.title }}
            <span v-if="$userId == marker.owner_id || $userId == map.owner_id"
                  v-can="'map_edit'"
                  class="card-tools pl-1"
            >
                <a @click="editMarker(marker)" type="button">
                    <i class="fa fa-pencil-alt mx-1"></i>
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

        <h5 v-if="marker.author !== ''" class="pt-3">{{ trans('global.author') }}</h5>
        <div>{{ marker.author }}</div>

        <h5 v-if="marker.description !== ''" class="pt-3">{{ trans('global.description') }}</h5>
        <div v-if="marker.description !== ''"
            class="pb-2"
            v-html="marker.description"
        ></div>

        <h5 class="pt-3">{{ trans('global.media.title') }}</h5>
        <div v-if="marker.id != null"
             v-bind:id="'map_marker_media_'+marker.id"
        >
            <media
                subscribable_type="App\MapMarker"
               :subscribable_id="marker.id"
                :can_add_media="$userId == marker.owner_id || $userId == map.owner_id"
               format="list"
            />
        </div>

        <h5 v-if="marker.address !== ''" class="pt-3">{{ trans('global.marker.fields.address') }}</h5>
        <div v-html="marker.address"></div>

        <h5 v-if="marker.url" class="pt-3">{{ trans('global.marker.fields.link') }}</h5>
        <div v-if="marker.url">
            <a :href="marker.url" target="_blank">
                <span v-if="marker.url_title">
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
        map: {
            default: null
        }
    },
    data() {
        return {
            component_id: this._uid,
            tag_array: {},
        }
    },
    watch: { // reload if context change
        marker: function(newVal, oldVal) {
            this.tag_array = newVal.tags?.split(",");
        },
    },
    methods: {
        editMarker(marker){
            this.$eventHub.$emit('edit_marker', marker);
        },
    },
}
</script>
