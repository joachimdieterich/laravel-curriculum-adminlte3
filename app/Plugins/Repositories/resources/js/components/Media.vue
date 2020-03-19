<template >
    <media ref="mediaPlugin" :model="model">    
    </media>
</template>


<script>
    import media from '../../../edusharing/resources/js/components/Media';
    export default {
        props: {
                model: {},
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
        computed: {
          
        },
        bevorOpen() {
            
        },
        components: {
            media,
        }
    }
</script>