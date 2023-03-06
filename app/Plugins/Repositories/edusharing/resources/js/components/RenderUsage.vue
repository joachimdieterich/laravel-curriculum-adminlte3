<template>
    <div>
        <div v-html="this.detailsSnippet"></div>

        <div :id="'loading_'+this.medium.id" class="overlay text-center" style="width:100% !important;">
            <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</template>


<script>
    export default {
        props: {
                'medium': {},
              },
        data() {
            return {
                detailsSnippet: '',
                errors:  {},
            }
        },
        methods: {
            async loader() {
                $("#loading_"+this.medium.id).show();
                axios.get('/media/' + this.medium.id)
                    .then((response) => {
                        //console.log(response);
                        this.detailsSnippet = response.data.detailsSnippet;
                        $("#loading_"+this.medium.id).hide();
                    })
                    .catch((error) => {
                        console.log(error);
                        $("#loading_"+this.medium.id).hide();
                    });
            },
            show(medium) {
                //window.open(medium.path, '_blank');
            },

        },
        mounted(){
            this.loader();
        },
        watch: {
            media: function (value, oldValue) {
                $("#loading_"+this.medium.id).hide();
            }
        },

    }
</script>

<style lang="scss">
.edusharing_rendering_content_wrapper > img {
    width: 100% !important;
}
.edusharing_rendering_content_footer_top,
.edusharing_rendering_content_footer {
    display: none !important;
}
</style>
