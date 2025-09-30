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
                            class="btn btn-icon-alt btn-icon-big highlight position-absolute"
                            style="left: 1rem; z-index: 1;"
                            @click="prev()"
                        >
                            <i class="fa fa-2x fa-angle-left"></i>
                        </button>
                        <button
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
                                :src="'/media/' + medium.id + '?preview=true'"
                                :alt="medium.title ?? medium.name"
                                class="position-relative user-select-none"
                                style="max-width: 90svw; max-height: 90svh;"
                            >
                        </div>
                    </div>
                </div>
            </div>
            <!-- preview gallery at bottom -->
            <div v-if="media.length > 1"
                class="position-absolute d-flex"
                style="bottom: 1rem; gap: 1rem; z-index: 1;"
            >
                <button v-for="(medium, index) in media"
                    class="gallery-item d-flex align-items-center justify-content-center bg-white border-0"
                    :class="{ 'active': index === currentSlide }"
                    @click="slideTo(index)"
                >
                    <img
                        :src="'/media/' + medium.id + '?preview=true'"
                        :alt="medium.title ?? medium.name"
                        style="max-height: 100px; max-width: 100px;"
                    >
                </button>
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
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params;

                if (typeof (params) !== 'undefined') {
                    this.media = params;

                    if (this.media.length > 1) {
                        // carousel needs to be initialized to activate swipe functionality
                        this.$nextTick(() => {
                            $('#carousel-' + this.component_id).carousel();
                            $('#carousel-' + this.component_id).on('slide.bs.carousel', e => {
                                this.currentSlide = e.to;
                                this.sliding = true;
                            });
                            $('#carousel-' + this.component_id).on('slid.bs.carousel', e => { this.sliding = false; });
                        });
                    }
                }
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