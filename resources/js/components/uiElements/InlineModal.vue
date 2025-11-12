<template>
    <div id="inline-modal" :class="this.open ? 'inline-modal-active' : ''">
        <div id="hidden-keeper"></div>
        <div class="content" :class="classString">
            <slot></slot>
        </div>
    </div>
</template>
<script>
import {defineComponent} from "vue";

export default defineComponent({
    name: "InlineModal",
    props: {
        open: {
            required: true,
            type: Boolean,
            title: "Determines if the modal is open",
        },
        classes: {
            type: String,
            default: '',
            title: "Additional styling classes",
        },
    },
    computed: {
        classString() {
            return `content ${this.classes}`;
        }
    }
})
</script>
<style scoped>
#inline-modal {
    display: grid;
    padding: 0;
    grid-template-rows: min-content 0fr;
    transition: grid-template-rows 500ms, padding 500ms;
}

#inline-modal .content {
    background-color: lightgrey;
    border-radius: 0.4em;
    padding: 4px;
    overflow: hidden;
}

.inline-modal-active {
    grid-template-rows: min-content 1fr !important;
}
</style>