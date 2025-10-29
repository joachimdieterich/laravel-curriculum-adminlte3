<template>
    <div
        :id="'link-overlay-' + component_id"
        class="link-overlay d-print-none"
    >
        <div
            class="link-wrapper d-flex align-items-center justify-content-center bg-light rounded-pill"
            :style="{ width: generatingLinks ? '50px' : '175px' }"
            style="z-index: 1;"
        >
            <button v-if="!generatingLinks && !URLsLoaded"
                type="button"
                class="btn btn-default bg-transparent rounded-pill border-0 w-100"
                @click.stop="getURLs()"
            >
                <i class="fa fa-link"></i>
                {{ trans('global.medium.generate_links') }}
            </button>
            <span v-else>
                <i class="fa fa-spinner fa-pulse p-2"></i>
                <span class="sr-only">{{ trans('global.loading') }}</span>
            </span>
        </div>
        <div v-if="(currentViewLink && currentDownloadLink)"
            class="link-buttons btn-group-vertical d-flex flex-column bg-light text-nowrap hide"
        >
            <button
                type="button"
                class="btn btn-light"
                @click.stop="open(currentViewLink)"
            >
                <i class="fa fa-arrow-up-right-from-square"></i>
                {{ trans('global.open') }}
            </button>
            <button
                type="button"
                class="btn btn-light"
                @click.stop="open(currentDownloadLink)"
            >
                <i class="fa fa-download"></i>
                {{ trans('global.download') }}
            </button>
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
    },
    data() {
        return {
            component_id: this.$.uid,
            generatingLinks: false,
            currentViewLink: null,
            currentDownloadLink: null,
        }
    },
    methods: {
        open(link) {
            window.open(link, '_blank');
        },
        getURLs() {
            this.$emit('generating', true); // deactivate slider in Carousel
            this.generatingLinks = true;
            const medium = this.medium;

            if (medium.adapter === 'local') {
                this.currentViewLink = '/media/' + medium.id + '?content=true';
                this.currentDownloadLink = '/media/' + medium.id + '?download=true';
            } else {
                // send requests for edusharing links
                axios.get('/media/' + medium.id + '?content=true')
                    .then((response) => this.currentViewLink = response.data);
                axios.get('/media/' + medium.id + '?download=true')
                    .then((response) => this.currentDownloadLink = response.data);
            }
        },
        setURLs() {
            const animationTime = 300;
            const linkValidTime = 5000; // edusharing links are only valid for a couple of seconds
            const overlay = document.getElementById('link-overlay-' + this.component_id);

            // to activate the transitions, we need to implement the logic through timeouts
            setTimeout(() => { // first we hide the loading-indicator
                const buttons = overlay.querySelector('.link-buttons'); // element doesn't exist before
                this.generatingLinks = false;
                buttons.classList.remove('hide');
                overlay.querySelector('.link-wrapper').style.visibility = 'hidden';

                setTimeout(() => { // then we show the actual links
                    buttons.classList.add('hide');

                    setTimeout(() => { // after the links aren't valid anymore, we reset everything
                        overlay.querySelector('.link-wrapper').style.visibility = 'visible';
                        this.currentViewLink = null;
                        this.currentDownloadLink = null;
                        this.$emit('generating', false); // reactivate slider in Carousel
                    }, animationTime);
                }, linkValidTime);
            }, animationTime);
        },
    },
    computed: {
        URLsLoaded() {
            return this.currentViewLink !== null && this.currentDownloadLink !== null;
        },
    },
    watch: {
        URLsLoaded(loaded) {
            if (loaded) this.setURLs();
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

    & > .link-wrapper, & > .link-buttons {
        transition: width 0.3s ease, opacity 0.3s linear, box-shadow 0.3s ease;
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.25);

        &:hover { box-shadow: 4px 4px 5px rgba(0, 0, 0, 0.25); }
    }
    & > .link-buttons {
        position: absolute;
        border-radius: 1rem;

        & > button:first-child { border-top-left-radius: 1rem; border-top-right-radius: 1rem; }
        & > button:last-child { border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem; }
    }
    &:hover > .link-wrapper,
    &:focus-within > .link-wrapper,
    &.active > .link-wrapper {
        opacity: 1 !important;
    }
}
</style>