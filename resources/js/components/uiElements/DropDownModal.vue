<template>
    <transition name="drop-down-modal">
        <div v-if="show"
             :style="modalCss"
             class="modal"
             @click.self="$emit('close')">
            <div class="card">
                <div v-if="showTitle" class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ title }}</h5>
                    <button type="button" class="close" @click="$emit(close)">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="card-body">
                    <slot name="body">
                        <p>Please add your text or HTML-Elements.</p>
                    </slot>
                </div>
                <div v-if="showTitle" class="card-footer">
                    <slot name="footer">
                        <button class="btn btn-secondary" @click="$emit(close)">Schließen</button>
                    </slot>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
export default {
    name: 'DropDownModal',
    props: {
        id: {
            type: String,
            default: 'modal'
        },
        show: {
            type: Boolean,
            required: true
        },
        title: {
            type: String,
            default: 'Modal Titel'
        },
        showTitle: {
            type: Boolean,
            default: true
        },
        showFooter: {
            type: Boolean,
            default: true
        },
        modalCss: {
            type: String,
            default: ''
        },
    },
    computed: {
        close: function() {
            return this.id + '-close';
        }
    }
}
</script>

<style scoped>
.card {
    box-shadow: 1px 1px 1px 1px lightgray;
    border-radius: 0.5rem;
}
.modal {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1000;
    margin-top: 2px;
    margin-left: -20%;
    width: 120%;
}

.close {
    margin-left: auto;
}

.drop-down-modal-enter-active,
.drop-down-modal-leave-active {
    transition: all 0.3s ease;
}

.drop-down-modal-enter-from,
.drop-down-modal-leave-to {
    transform: translateY(-20px);
    opacity: 0;
}
</style>