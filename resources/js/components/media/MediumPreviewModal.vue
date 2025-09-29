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
                    <div v-if="media.length > 1"
                        class="position-absolute d-flex align-items-center"
                        style="inset: 0"
                    >
                        <!-- slider arrows left/right -->
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
                        <!-- preview gallery at bottom -->
                    </div>
                    <div class="carousel-inner d-flex align-items-center">
                        <div v-for="(medium, index) in media"
                            :class="{ 'active': index === 0 }"
                            class="carousel-item text-center"
                            style="min-height: 50px;"
                        >
                            <i class="fa fa-spinner fa-pulse fa-3x fa-fw position-absolute text-white"></i>
                            <img
                                :src="'/media/' + medium.id"
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
        }
    },
    methods: {
        prev() {
            $('#carousel-' + this.component_id).carousel('prev');
        },
        next() {
            $('#carousel-' + this.component_id).carousel('next');
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
                        });
                    }
                }
            }
        });
    },
}
</script>