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
            <i class="fa fa-spinner fa-pulse fa-3x fa-fw position-absolute text-white"></i>

            <img
                :src="'/media/' + medium.id + '?preview=true'"
                :alt="medium.title ?? medium.name"
                class="position-relative"
                style="max-width: 90svw; max-height: 90svh;"
            >
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
            medium: [],
        }
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params;

                if (typeof (params) !== 'undefined') {
                    this.medium = params;
                }
            }
        });
    },
}
</script>