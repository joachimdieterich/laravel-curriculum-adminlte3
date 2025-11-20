<template>
    <div
        :id="'link-overlay-' + component_id"
        class="link-overlay d-print-none"
    >
        <div
            class="link-wrapper d-flex align-items-center justify-content-center bg-light rounded-pill overflow-hidden text-nowrap"
            :style="{ width: buttonWidth() }"
            style="height: 40px; z-index: 1;"
        >
            <button v-if="!generatingLink"
                type="button"
                class="btn btn-default d-flex justify-content-center align-items-center rounded-pill border-0 w-100 h-100"
                @click.stop="open()"
            >
                <span v-if="currentViewLink">
                    <i class="fa fa-arrow-up-right-from-square"></i>
                    {{ trans('global.open') }}
                </span>
                <span v-else>
                    <i class="fa fa-link"></i>
                    {{ trans('global.medium.generate_links') }}
                </span>
            </button>
            <span v-else>
                <i class="fa fa-spinner fa-pulse"></i>
                <span class="sr-only">{{ trans('global.loading') }}</span>
            </span>
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
        loadOnMount: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            generatingLink: false,
            currentViewLink: null,
        }
    },
    mounted() {
        if (this.loadOnMount) this.getURLs();
    },
    methods: {
        open() {
            if (!this.currentViewLink) this.getURLs();
            else window.open(this.currentViewLink, '_blank');
        },
        getURLs() {
            this.$emit('generating', true); // deactivate slider in Carousel
            this.generatingLink = true;
            const medium = this.medium;

            if (medium.adapter === 'local') {
                this.currentViewLink = '/media/' + medium.id + '?content=true';
                this.setURLs();
            } else { // send requests for edusharing links
                axios.get('/media/' + medium.id + '?content=true')
                    .then((response) => {
                        this.currentViewLink = response.data;
                        this.setURLs();
                    });
            }
        },
        setURLs() {
            const animationTime = 300;
            const linkValidTime = 5000; // edusharing links are only valid for a couple of seconds

            // to activate the transitions, we need to implement the logic through timeouts
            setTimeout(() => { // first we hide the loading-indicator
                this.generatingLink = false;

                setTimeout(() => { // after the link isn't valid anymore, we reset everything
                    this.currentViewLink = null;
                    this.$emit('generating', false); // reactivate slider in Carousel
                }, linkValidTime + animationTime);
            }, animationTime);
        },
        buttonWidth() {
            if (this.generatingLink) return '50px';
            if (this.currentViewLink) return '125px';
            return '175px';
        },
    },
}
</script>
<style scoped>
.link-overlay {
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity 0.3s linear;

    & > .link-wrapper {
        transition: width 0.3s ease, opacity 0.3s linear, box-shadow 0.3s ease;
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.25);

        &:hover { box-shadow: 4px 4px 5px rgba(0, 0, 0, 0.25); }
    }
}
</style>