<template>
    <img v-if="isCarousel"
        :src="'/media/' + medium.id + '?preview=true'"
        class="d-block mw-100 w-auto m-auto"
        :alt="medium.title ?? medium.medium_name"
        :mime="medium.mime_type"
    >
    <div v-else
        class="nav-item-box-image-size h-100 w-100"
        :style="{'background': 'url(/media/' + medium.id + '?preview=true) center no-repeat'}"
        :alt="medium.title"
        @click="show()"
    ></div>

    <div
        :id="'loading_' + component_id"
        class="overlay position-absolute text-center w-100"
        style="inset: 0;"
    >
        <i class="fa fa-spinner fa-pulse fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
</template>
<script>
export default {
    props: {
        medium: {
            type: Object,
            default: null,
        },
        isCarousel: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
        }
    },
    methods: {
        show() {
            this.toggleLoadingIndicator();
            if (this.medium.adapter == 'local') {
                window.location.assign('/media/' + this.medium.id + '?download=true');
            } else {
                axios.get('/media/' + this.medium.id + '?content=true')
                    .then((response) => {
                        window.open(response.data, '_blank');
                        this.toggleLoadingIndicator();
                    })
                    .catch((error) => {
                        console.log(error);
                        this.toggleLoadingIndicator();
                    });
            }
        },
        toggleLoadingIndicator() {
            // initial state => visible
            $("#loading_" + this.component_id).toggle();
        },
    },
    mounted() {
        this.toggleLoadingIndicator();

        this.$eventHub.on('download', (medium) => {
            if (this.medium.id == medium.id) {
                this.toggleLoadingIndicator();
                axios.get('/media/' + this.medium.id + '?download=true')
                    .then((response) => {
                        window.open(response.data, '_blank');
                        this.toggleLoadingIndicator();
                    })
                    .catch((error) => {
                        console.log(error);
                        this.toggleLoadingIndicator();
                    });
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