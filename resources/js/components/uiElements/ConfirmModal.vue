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
                    :class="'bg-' + this.css"
                >
                    <h3 class="card-title">{{ this.title }}</h3>
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
                        <div class="card-body">{{ this.description }}</div>
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
                            {{ this.cancel_label }}
                        </button>
                        <button
                            id="confirm-save"
                            class="btn btn-primary ml-3"
                            @click="$emit('confirm');"
                        >
                            {{ this.ok_label }}
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
    components: {},
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
        }
    },
    methods: {},
    mounted() {},
}
</script>
