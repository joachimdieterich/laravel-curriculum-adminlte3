<template>
    <img v-if="mime === 'image' || mime === 'video' || mime === 'audio' || mime === 'application'"
        :src="'/media/' + medium.id + '?preview=true&size=max'"
        :alt="medium.title ?? medium.medium_name"
        class="d-block mw-100 m-auto"
    />

    <RenderUsage v-else-if="mime === 'external'"
        :medium="medium"
        :isCarousel="true"
    />
    
    <img v-else-if="mime === 'learning-app'"
        :src="'/media/' + medium.id + '?preview=true&size=max'"
        :alt="medium.title ?? medium.medium_name"
        class="d-block mw-100 m-auto"
        @click.stop="open(medium.path)"
    />

    <span v-else-if="mime === 'embed'"
        style="height: 500px"
    >
        <iframe
            :src="'/media/' + medium.id"
            :height="height"
            :width="width"
            frameborder="0"
        ></iframe>
    </span>

    <div v-if="mime === 'video' || mime === 'audio' || mime === 'application'"
        class="position-absolute inset d-print-none d-flex justify-content-center"
    >
        <LinkOverlay :medium="medium"/>
    </div>
</template>
<script>
import RenderUsage from "../../../../app/Plugins/Repositories/edusharing/resources/js/components/RenderUsage.vue"
import LinkOverlay from "./LinkOverlay.vue";

export default {
    props: {
        medium: {
            type: Object,
            default: null,
        },
        height: {
            type: Number,
            default: 500,
        },
        width: {
            type: Number,
            default: 600,
        },
        edit: {
            type: Boolean,
            default: false,
        },
        downloadable: {
            type: Boolean,
            default: true,
        },
    },
    methods: {
        open(link) {
            window.open(link, '_blank');
        },
    },
    computed: {
        mime() {
            switch (this.medium.mime_type.split('/')[0]) {
                case 'image':
                    return 'image';
                case 'video':
                    return 'video';
                case 'audio':
                    return 'audio';
                // documents should get a link-overlay for download/view
                case 'application':
                    // learning-apps can be directly opened via 'path'-attribute
                    if (this.medium.mime_type === 'application/xhtml+xml') return 'learning-app'
                    else return 'application';
                case 'edusharing': // legacy support or fallback if no mimetype was set on external media
                    return 'external';
                default:
                    return 'embed';
            }
        },
    },
    components: {
        RenderUsage,
        LinkOverlay,
    },
}
</script>