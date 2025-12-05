<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask align-items-center justify-content-center"
            @mouseup.self="close()"
        >
            <button
                class="btn btn-icon-alt btn-icon-big position-absolute bg-white"
                style="top: 1rem; right: 1rem; z-index: 1;"
                @click="close()"
            >
                <i class="fa fa-2x fa-close"></i>
            </button>
            <div
                class="position-absolute d-flex flex-column"
                style="bottom: 1rem; gap: 2rem; z-index: 1;"
            >
                <!-- preview gallery at bottom | needs to be before the high resolution images to load first -->
                <div v-if="media.length > 1"
                    class="d-flex"
                    style="gap: 1rem;"
                >
                    <button v-for="(medium, index) in media"
                        type="button"
                        class="gallery-item btn d-flex align-items-center justify-content-center bg-white p-1 border-0"
                        :class="{ 'active': index === currentSlide }"
                        @click="slideTo(index)"
                    >
                        <img
                            :src="'/media/' + medium.id + '?preview=true&size=200'"
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
                            @click="prev()"
                        >
                            <i class="fa fa-2x fa-angle-left"></i>
                        </button>
                        <button
                            type="button"
                            class="btn btn-icon-alt btn-icon-big highlight position-absolute"
                            style="right: 1rem; z-index: 1;"
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
                                :src="'/media/' + medium.id + '?preview=true&size=max'"
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
            sliding: false, // prevent multiple slide events at the same time
        }
    },
    methods: {
        close() {
            this.globalStore.closeModal(this.$options.name);
        },
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
    },    
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (!state.modals[this.$options.name].show) return;
            
            const params = state.modals[this.$options.name].params;
            if (params === undefined) return;

            this.media = params.media;
            if (this.media.length > 1) {
                this.currentSlide = params.initialSlide ?? 0;
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
</style>