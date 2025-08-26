<template>
    <div>
        <span v-if="mime(medium.mime_type) === 'embed'"
            style="height: 500px"
        >
            <iframe
                :src="'/media/' + medium.id"
                :height="height"
                :width="width"
                frameborder="0"
            ></iframe>
        </span>

        <RenderUsage v-else-if="mime(medium.mime_type) === 'external'"
            :medium="medium"
            :downloadable="downloadable"
            :isCarousel="true"
        />

        <img v-else-if="mime(medium.mime_type) === 'img'"
            :src="'/media/' + medium.id"
            width="100%"
        />

        <span v-else>
            - Please download file -
        </span>
    </div>
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
        mime(type) {
            switch (type) {
                //Images use <img>
                case 'image/jpg' :
                case 'image/jpeg':
                case 'image/png':
                case 'image/gif':
                case 'image/bmp':
                case 'image/tiff':
                case 'image/ico':
                case 'image/svg':
                    return 'img';
                    break;
                case 'edusharing':
                    return 'external';
                    break;
                // default use <embed>
                default:
                    return 'embed';
                    break;
            }
        },
    },
    components: {
        RenderUsage
    },
}
</script>