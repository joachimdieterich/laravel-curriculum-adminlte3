<template>
    <div>
        <div class="tab-header d-flex align-items-center ps-3 bg-primary">
            <span class="t-20 line-clamp">{{ marker.title }}</span>
            <span v-if="marker.owner_id == $userId || checkPermission('is_admin')"
                v-permission="'map_edit'"
                class="d-print-none d-flex ms-auto me-1"
            >
                <button
                    type="button"
                    class="btn btn-icon-alt"
                    data-bs-toggle="tooltip"
                    :data-bs-title="trans('global.marker.edit')"
                    @click="editMarker(marker)"
                >
                    <i class="fas fa-pencil-alt p-2"></i>
                </button>
            </span>
        </div>

        <div class="d-flex flex-column gap-2 px-3 py-2">
            <div v-if="marker.tags?.length > 0"
                class="d-flex gap-2"    
            >
                <span v-for="tag in marker.tags.split(',')"
                    class="badge rounded-pill text-bg-primary"
                >
                    {{ tag }}
                </span>
            </div>
    
            <div v-if="marker.author">
                <h5 class="m-0">{{ trans('global.marker.fields.author') }}</h5>
                {{ marker.author }}
            </div>
    
            <div v-if="marker.description">
                <h5 class="pt-2 m-0">{{ trans('global.description') }}</h5>
                <div v-html="marker.description"></div>
            </div>
    
            <div>
                <h5>{{ trans('global.medium.title') }}</h5>
                <Media
                    subscribable_type="App\MapMarker"
                    :subscribable_id="marker.id"
                    :editable="editable"
                    :public="true"
                    format="list"
                />
            </div>
    
            <div v-if="marker.address">
                <h5 class="pt-2">{{ trans('global.address') }}</h5>
                {{ marker.address }}
            </div>
    
            <div v-if="marker.url"
                class="t-18 pt-1"
            >
                <a
                    :href="marker.url"
                    target="_blank"
                >
                    {{ marker.url_title ?? 'Link' }}
                </a>
            </div>
        </div>
    </div>
</template>
<script>
import Media from '../media/Media.vue';

export default {
    name: 'MarkerView',
    components: { Media },
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
        }
    },
    mounted() {
        this.enableTooltips(); // also enables tooltips for the Map-component
    },
    methods: {
        editMarker(marker) {
            this.globalStore.showModal('map-marker-modal', marker);
        },
    },
}
</script>