<template>
    <div>
        <div v-html="this.detailsSnippet"
        @click="show()"></div>
        <div style="width: 100%;display: block;height: 25px;">
            <i v-if="downloadable"
                class="edusharing_download fa fa-download text-muted pointer"
               @click="show()"></i>
        </div>

        <div :id="'loading_'+this.medium.id" class="overlay text-center" style="width:100% !important;">
            <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</template>


<script>
    export default {
        props: {
            medium: {},
            downloadable: {
                type: Boolean,
                default: true
            }
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
            show() {
                window.open(this.medium.path, '_blank');
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
    /*width: 60%;
    margin-right: 25px;
    margin-left: 25px;*/
}

.edusharing_download {
    position: absolute;
    left: 0;
    bottom: 5px;
    z-index: 15;
    display: flex;
    justify-content: center;
    padding-left: 0;
    margin-right: 15%;
    margin-left: 15%;
    list-style: none;
}

iframe {
    min-height: 350px !important;
}
</style>
