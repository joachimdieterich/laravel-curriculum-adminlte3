<template>
    <div>
        <div @click="show()">
            <img :src="'/media/'+this.medium.id+'?preview=true'" class="p-0 w-100" >


<!--            <img v-if="previewImg"
                 :src='previewImg' class="p-0 w-100" >
            <img v-else
                 :src='this.preview' class="p-0 w-100" >-->

        </div>
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
            <i class="fa fa-spinner fa-pulse fa-fw"></i>
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
                previewImg: false,
                title: '',
                name: '',
                errors:  {},
            }
        },
        methods: {
            async loader() {
                $("#loading_"+this.medium.id).show();
                await axios.get('/media/' + this.medium.id)
                    .then((response) => {
                        //console.log(response.data);
                        this.detailsSnippet = response.data.detailsSnippet;
                        this.downloadUrl = response.data.downloadUrl;
                        this.preview = response.data.preview;
                        this.title = response.data.title;
                        this.name = response.data.name;
                        //console.log(this.downloadUrl);
                        if (this.downloadUrl == null){
                            $("#download_medium_"+this.medium.id).hide();
                        }
                        $("#loading_"+this.medium.id).hide();
                    })
                    .catch((error) => {
                        console.log(error);
                        $("#loading_"+this.medium.id).hide();
                    });
            },
           /* async getPreview() {
                $("#loading_"+this.medium.id).show();
                axios.get('/media/' + this.medium.id + '?preview=true')
                    .then((response) => {
                        //console.log(response.data);
                        if(typeof response.data.url == 'undefined'){
                            if (response.data.startsWith('data:image')){
                                this.previewImg = response.data;
                            }
                        } else {
                            this.preview = response.data.url.info.redirect_url
                        }
                        $("#loading_"+this.medium.id).hide();
                    })
                    .catch((error) => {
                        console.log(error);
                        $("#loading_"+this.medium.id).hide();
                    });
            },*/
            show() {
                $("#loading_"+this.medium.id).show();
                axios.get('/media/' + this.medium.id + '?content=true')
                    .then((response) => {
                        window.location.assign(response.data.url);
                        $("#loading_"+this.medium.id).hide();
                    })
                    .catch((error) => {
                        console.log(error);
                        $("#loading_"+this.medium.id).hide();
                    });
            },
        },
        mounted(){
            $("#loading_"+this.medium.id).hide();
            this.$nextTick(() => {
                this.loader();
            })

            this.$eventHub.on('download', (medium) => {

                if (this.medium.id == medium.id) {
                    $("#loading_"+this.medium.id).show();
                    axios.get('/media/' + this.medium.id + '?download=true')
                        .then((response) => {
                            window.location.assign(response.data.url);
                            $("#loading_"+this.medium.id).hide();
                        })
                        .catch((error) => {
                            console.log(error);
                            $("#loading_"+this.medium.id).hide();
                        });
                } else {
                    console.log('no downloadURL');
                }
            });
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
