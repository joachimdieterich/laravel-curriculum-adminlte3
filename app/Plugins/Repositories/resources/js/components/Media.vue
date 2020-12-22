<template >
    <media
        v-if="repository.value == 'edusharing'"
        ref="mediaPlugin" :model="model">
    </media>
</template>


<script>
    import media from '../../../edusharing/resources/js/components/Media';
    export default {
        props: {
            model: {},
            repository: Object
        },
        data() {
            return {
                errors: {}
            }
        },
        methods: {
            loader: function() {
                    this.$refs.mediaPlugin.loader();
            }

        },

        components: {
            media,
        }
    }
</script>
