<template>
    <div
        class="d-flex align-items-center justify-content-center"
        style="min-height: 100px;"
        @click="open()"
    >
        <img v-if="mime === 'image' || mime === 'video' || mime === 'audio' || mime === 'application'"
            :src="'/media/' + medium.id + '?preview=true&size=max'"
            :alt="medium.title ?? medium.medium_name"
            class="d-block mw-100 m-auto"
        />
    
        <RenderUsage v-else
            :medium="medium"
            :isCarousel="true"
        />
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
    },
    methods: {
        open() {
            window.open('/media/' + this.medium.id + '?content=true', '_blank');
        },
    },
    computed: {
        mime() {
            return this.medium.mime_type.split('/')[0];
        },
    },
    components: {
        RenderUsage,
    },
}
</script>