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

    <div v-if="loading"
        class="overlay"
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
            loading: false,
        }
    },
    methods: {
        show() {
            window.open('/media/' + this.medium.id + '?content=true', '_blank');
        },
    },
    mounted() {
        this.$eventHub.on('download', medium => {
            if (this.medium.id !== medium.id) return;

            this.loading = true;
            axios.get('/media/' + this.medium.id + '?download=true')
                .then((response) => {
                    window.open(response.data, '_blank');
                    this.loading = false;
                })
                .catch((e) => {
                    console.log(e);
                    this.loading = false;
                    this.toast.error(this.errorMessage(e));
                });
        });
    },
}
</script>