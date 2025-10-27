<template>
    <img v-if="mime === 'img' || mime === 'document'"
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
</template>
<script>
import RenderUsage from "../../../../app/Plugins/Repositories/edusharing/resources/js/components/RenderUsage.vue"

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
            switch (this.medium.mime_type) {
                // Images use <img>
                case 'image/jpg' :
                case 'image/jpeg':
                case 'image/png':
                case 'image/gif':
                case 'image/bmp':
                case 'image/tiff':
                case 'image/ico':
                case 'image/svg':
                    return 'img';
                case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document': // .docx
                case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': // .xlsx
                case 'application/vnd.openxmlformats-officedocument.presentationml.presentation': // .pptx
                    return 'document';
                case 'application/xhtml+xml':
                    return 'learning-app';
                case 'edusharing': // legacy support or fallback if no mimetype was set on external media
                    return 'external';
                default:
                    return 'embed';
            }
        },
    },
    components: {
        RenderUsage
    },
}
</script>