<template>
    <div class="position-relative">
        <img v-if="isCarousel"
            :src="'/media/' + medium.id + '?preview=true'"
            width="100%"
        >
        <div v-else
            @click="show()"
            class="nav-item-box-image-size h-100 w-100"
            :style="{'background': 'url() center no-repeat'}"
            :alt="medium.title"
        ></div>

        <div
            :id="'loading_' + medium.id"
            class="overlay text-center w-100"
        >
            <i class="fa fa-spinner fa-pulse fa-fw"></i>
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        medium: {
            type: Object,
            default: null,
        },
        downloadable: {
            type: Boolean,
            default: true,
        },
        isCarousel: {
            type: Boolean,
            default: false,
        },
    },
    methods: {
        show() {
            $("#loading_" + this.medium.id).show();
            if (this.medium.adapter == 'local') {
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
    mounted() {
        $("#loading_"+this.medium.id).hide();

        this.$eventHub.on('download', (medium) => {
            if (this.medium.id == medium.id) {
                $("#loading_" + this.medium.id).show();
                axios.get('/media/' + this.medium.id + '?download=true')
                    .then((response) => {
                        window.location.assign(response.data.url);
                        $("#loading_" + this.medium.id).hide();
                    })
                    .catch((error) => {
                        console.log(error);
                        $("#loading_" + this.medium.id).hide();
                    });
            } else {
                console.log('no downloadURL');
            }
        });
    },
}
</script>
<style>
.edusharing_rendering_content_wrapper > img {
    width: 100% !important;
}
</style>