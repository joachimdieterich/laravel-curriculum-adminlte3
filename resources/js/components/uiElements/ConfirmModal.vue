<template>
    <Transition name="modal">
        <div v-if="showConfirm"
            class="modal-mask"
            @click.self="$emit('close')"
        >
            <div
                class="modal-container"
                style="border-top-left-radius: 5px; border-top-right-radius: 5px;"
            >
                <div
                    class="card-header"
                    :class="'bg-' + css"
                >
                    <h3 class="card-title">{{ title }}</h3>
                    <div class="card-tools">
                        <button
                            type="button"
                            class="btn btn-tool"
                            @click="$emit('close')"
                        >
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">{{ description }}</div>
                    </div>
                </div>
                <div class="card-footer">
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
                            class="btn btn-primary ml-3"
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