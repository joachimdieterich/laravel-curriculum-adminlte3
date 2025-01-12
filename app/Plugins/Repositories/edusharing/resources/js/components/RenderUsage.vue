<template>
    <div>
        <div @click="show()">
            <img :src="'/media/' + this.medium.id + '?preview=true'"
                 :alt="this.medium.title"
                 class="p-0 w-100" >
        </div>

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
            }
        },
        methods: {
            show() {
                $("#loading_"+this.medium.id).show();
                if (this.medium.adapter == 'local'){
                    window.location.assign('/media/' + this.medium.id + '?download=true');
                } else {
                    axios.get('/media/' + this.medium.id + '?content=true')
                        .then((response) => {
                            window.location.assign(response.data.url);
                            $("#loading_" + this.medium.id).hide();
                        })
                        .catch((error) => {
                            console.log(error);
                            $("#loading_" + this.medium.id).hide();
                        });
                }
            },
        },
        mounted(){
            $("#loading_"+this.medium.id).hide();

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

iframe {
    min-height: 350px !important;
}
</style>
