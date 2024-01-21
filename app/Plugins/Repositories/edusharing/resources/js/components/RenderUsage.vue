<template>
    <div>
        <div v-html="this.detailsSnippet"
             @click="show()"></div>
<!--        <div class="edusharing_caption">
            <span v-if="this.title">
                {{ this.title }}
            </span>
            <span v-else>
                {{ this.name }}
            </span>
        </div>-->

<!--        <div v-html="this.detailsSnippet"
        @click="show()"></div>-->
<!--        <div style="width: 100%;display: block;height: 25px;">
            <i v-if="downloadable"
                class="edusharing_download fa fa-download text-muted pointer"
               @click="show()"></i>
        </div>-->

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
                downloadUrl: '',
                preview: '',
                title: '',
                name: '',
                errors:  {},
            }
        },
        methods: {
            async loader() {
                $("#loading_"+this.medium.id).show();
                axios.get('/media/' + this.medium.id)
                    .then((response) => {
                        //console.log(response.data);
                        this.detailsSnippet = response.data.detailsSnippet;
                        this.downloadUrl = response.data.downloadUrl;
                        this.preview = response.data.preview;
                        this.title = response.data.title;
                        this.name = response.data.name;
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

            this.$eventHub.$on('download', (medium) => {
                if (this.medium.id == medium.id) {
                    axios.get('/media/' + this.medium.id + '?download=true')
                        .then((response) => {
                            window.location.assign(response.data.url);
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                }
            });
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

/*.edusharing_download {
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
}*/
.edusharing_caption {
    position: absolute;
    display:block;
    background-color: #ffffff70;
    border-radius: 0 0 0 5px ;
    width:100%;
    z-index: 15;
    display: flex;
    justify-content: space-around;
    margin: 0 auto;
    padding: 2px;
    bottom:25px;
}

iframe {
    min-height: 350px !important;
}
</style>
