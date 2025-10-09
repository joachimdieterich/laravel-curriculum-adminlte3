<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask align-items-center justify-content-center"
            @mouseup.self="globalStore.closeModal($options.name)"
        >
            <button
                class="btn btn-icon-alt btn-icon-big highlight position-absolute"
                style="top: 1rem; right: 1rem; z-index: 1;"
                @click="globalStore.closeModal($options.name)"
            >
                <i class="fa fa-2x fa-close"></i>
            </button>
            <div
                class="position-absolute d-flex flex-column"
                style="bottom: 1rem; gap: 1rem; z-index: 1;"
            >
                <!-- create-links overlay -->
                <div
                    id="link-overlay"
                    class="d-print-none"
                >
                    <div
                        id="link-wrapper"
                        class="d-flex align-items-center justify-content-center bg-light rounded-pill"
                        :style="{ width: generatingLinks ? '50px' : '175px' }"
                        style="z-index: 1;"
                    >
                        <button v-if="!generatingLinks && !URLsLoaded"
                            type="button"
                            class="btn btn-default bg-transparent rounded-pill border-0 w-100"
                            @click="getURLs()"
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
                        id="link-buttons"
                        class="btn-group-vertical d-flex flex-column bg-light text-nowrap hide"
                    >
                        <button
                            type="button"
                            class="btn btn-light"
                            @click="openLink(currentViewLink)"
                        >
                            <i class="fa fa-arrow-up-right-from-square"></i>
                            {{ trans('global.open') }}
                        </button>
                        <button
                            type="button"
                            class="btn btn-light"
                            @click="openLink(currentDownloadLink)"
                        >
                            <i class="fa fa-download"></i>
                            {{ trans('global.download') }}
                        </button>
                    </div>
                </div>
                <!-- preview gallery at bottom | needs to be before the high resolution images to load first -->
                <div v-if="media.length > 1"
                    class="d-flex"
                    style="gap: 1rem;"
                >
                    <button v-for="(medium, index) in media"
                        type="button"
                        class="gallery-item btn d-flex align-items-center justify-content-center bg-white p-1 border-0"
                        :class="{ 'active': index === currentSlide }"
                        :disabled="generatingLinks || URLsLoaded"
                        @click="slideTo(index)"
                    >
                        <img
                            :src="'/media/' + medium.id + '?preview=true&maxWidth=92&maxHeight=92'"
                            :alt="medium.title ?? medium.name"
                            class="mh-100 mw-100 rounded"
                            style="box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);"
                        >
                    </button>
                </div>
            </div>
            <div class="w-100">
                <div
                    :id="'carousel-' + component_id"
                    class="carousel slide"
                    data-interval="false"
                    data-touch="true"
                >
                    <!-- slider arrows left/right -->
                    <div v-if="media.length > 1"
                        class="position-absolute d-flex align-items-center"
                        style="inset: 0"
                    >
                        <button
                            type="button"
                            class="btn btn-icon-alt btn-icon-big highlight position-absolute"
                            style="left: 1rem; z-index: 1;"
                            :disabled="generatingLinks || URLsLoaded"
                            @click="prev()"
                        >
                            <i class="fa fa-2x fa-angle-left"></i>
                        </button>
                        <button
                            type="button"
                            class="btn btn-icon-alt btn-icon-big highlight position-absolute"
                            style="right: 1rem; z-index: 1;"
                            :disabled="generatingLinks || URLsLoaded"
                            @click="next()"
                        >
                            <i class="fa fa-2x fa-angle-right"></i>
                        </button>
                    </div>
                    <div class="carousel-inner d-flex align-items-center">
                        <div v-for="(medium, index) in media"
                            :class="{ 'active': index === 0 }"
                            class="carousel-item text-center"
                            style="min-height: 50px;"
                        >
                            <i class="fa fa-spinner fa-pulse fa-3x fa-fw position-absolute text-white"></i>
                            <img
                                :src="'/media/' + medium.id + '?preview=true&maxWidth=null&maxHeight=null'"
                                :alt="medium.title ?? medium.name"
                                class="position-relative user-select-none"
                                style="max-width: 90svw; max-height: 90svh;"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>
<script>
import {useGlobalStore} from "../../store/global";

export default {
    name: 'medium-preview-modal',
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            media: [],
            currentSlide: 0,
            generatingLinks: false,
            currentViewLink: null,
            currentDownloadLink: null,
            sliding: false,
        }
    },
    methods: {
        prev() {
            if (this.sliding) return;
            $('#carousel-' + this.component_id).carousel('prev');
        },
        next() {
            if (this.sliding) return;
            $('#carousel-' + this.component_id).carousel('next');
        },
        slideTo(index) {
            if (this.sliding) return;
            $('#carousel-' + this.component_id).carousel(index);
        },
        openLink(link) {
            if (link) window.open(link, '_blank');
        },
        getURLs() {
            this.generatingLinks = true;
            const medium = this.media[this.currentSlide];

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

            // to activate the transitions, we need to implement the logic through timeouts
            setTimeout(() => { // first we hide the loading-indicator
                const buttons = document.getElementById('link-buttons'); // element doesn't exist before
                this.generatingLinks = false;
                buttons.classList.remove('hide');
                document.getElementById('link-wrapper').style.visibility = 'hidden';

                setTimeout(() => { // then we show the actual links
                    buttons.classList.add('hide');

                    setTimeout(() => { // after the links aren't valid anymore, we reset everything
                        document.getElementById('link-wrapper').style.visibility = 'visible';
                        this.currentViewLink = null;
                        this.currentDownloadLink = null;
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
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (!state.modals[this.$options.name].show) return;
            
            const params = state.modals[this.$options.name].params;
            if (params === undefined) return;

            this.media = params.media;
            if (this.media.length > 1) {
                // carousel needs to be initialized to activate swipe functionality
                this.$nextTick(() => {
                    $('#carousel-' + this.component_id).carousel();
                    $('#carousel-' + this.component_id).on('slide.bs.carousel', e => {
                        this.currentSlide = e.to;
                        this.sliding = true;
                    });
                    $('#carousel-' + this.component_id).on('slid.bs.carousel', e => { this.sliding = false; });
                    // needs to be called after carousel initialization
                    if (params.initialSlide > 0) this.slideTo(params.initialSlide);
                });
            }
        });
    },
}
</script>
<style scoped>
.gallery-item {
    flex: 1;
    width: 100px;
    height: 100px;
    border-radius: 0.5rem;
    box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.3);
    filter: brightness(0.6);
    transition: filter 0.3s ease;

    &.active { filter: brightness(1) }
}
#link-overlay {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.25rem;
    transition: opacity 0.3s linear;

    & > #link-wrapper, & > #link-buttons {
        transition: width 0.3s ease, opacity 0.3s linear, box-shadow 0.3s ease;
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.25);

        &:hover { box-shadow: 4px 4px 5px rgba(0, 0, 0, 0.25); }
    }
    & > #link-buttons {
        position: absolute;
        border-radius: 1rem;

        & > button:first-child { border-top-left-radius: 1rem; border-top-right-radius: 1rem; }
        & > button:last-child { border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem; }
    }
    &:hover > #link-wrapper, &:focus-within > #link-wrapper, &.active > #link-wrapper { opacity: 1 !important; }
}
</style>