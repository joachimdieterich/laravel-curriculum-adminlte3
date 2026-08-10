<template>
    <Transition name="modal">
        <div v-if="showConfirm"
            class="modal-mask"
            @click.self="$emit('close')"
        >
            <div
                class="modal-container bg-transparent"
                style="max-width: min(95vw, 750px);"
            >
                <div
                    class="modal-header"
                    :class="'bg-' + css"
                    style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;"
                >
                    <span class="modal-title">{{ title }}</span>
                    <button
                        type="button"
                        class="btn btn-icon-alt"
                        :title="trans('global.close')"
                        @click="$emit('close')"
                    >
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="modal-body bg-white">
                    <div class="p-3" v-html="description"></div>
                </div>
                <div class="modal-footer">
                    <span class="pull-right">
                        <button
                            id="confirm-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="$emit('close')"
                        >
                            {{ cancel_label }}
                        </button>
                        <button
                            id="confirm-save"
                            class="btn btn-primary ms-3"
                            :disabled="processing"
                            @click="processing = true; $emit('confirm');"
                        >
                            <span v-if="processing"><i class="fa fa-spinner fa-pulse fa-fw"></i></span>
                            <span v-else>{{ ok_label }}</span>
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </Transition>
</template>
<script>
export default {
    name: 'confirm-modal',
    props: {
        showConfirm: {
            type: Boolean,
            default: false,
        },
        title: {
            type: String,
            default: null,
        },
        description: {
            type: String,
            default: null,
        },
        css: {
            type: String,
            default: 'danger',
        },
        ok_label: {
            type: String,
            default: window.trans.global.ok,
        },
        cancel_label: {
            type: String,
            default: window.trans.global.cancel,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            processing: false,
        }
    },
    watch: {
        showConfirm(newValue) {
            if (!newValue) this.processing = false;
        },
    },
}
</script>