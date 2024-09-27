<template>
    <Transition
        v-if="showConfirm"
        name="confirm">
        <div class="modal-mask"
        >
            <div class="modal-container">
                <div class="card-header"
                     :class="'bg-' + this.css">
                    <h3 class="card-title">
                        {{ this.title}}
                    </h3>
                    <div class="card-tools">
                        <button
                            type="button"
                            class="btn btn-tool"
                            @click="$emit('close')">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                    {{ this.description}}
                </div>
                <div class="card-footer">
                     <span class="pull-right">
                         <button
                             id="confirm-cancel"
                             type="button"
                             class="btn btn-default mr-2"
                         @click="$emit('close')">
                             {{ this.cancel_label }}
                         </button>
                         <button
                             id="confirm-save"
                             class="btn btn-primary"
                             @click="$emit('confirm');" >
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
        components:{},
        props: {
            showConfirm:{
                type: Boolean
            },
            title: {
                type: String
            },
            description: {
                type: String
            },
            css: {
                type: String,
                default: 'danger'
            },
            ok_label: {
                type: String,
                default: window.trans.global.ok
            },
            cancel_label: {
                type: String,
                default: window.trans.global.cancel
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
