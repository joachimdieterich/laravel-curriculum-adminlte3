<template>
    <Media v-if="repository.value == 'edusharing'"
        ref="mediaPlugin"
        :model="model"
    />
</template>
<script>
import Media from '../../../edusharing/resources/js/components/Media.vue';
export default {
    props: {
        model: {
            type: Object,
            default: null,
        },
        repository: {
            type: Object,
            default: null,
        },
    },
    methods: {
        loader: function() {
            if (this.repository.value == 'edusharing') {
                this.$refs.mediaPlugin.loader();
            }
        },
    },
    components: {
        Media,
    },
}
</script>