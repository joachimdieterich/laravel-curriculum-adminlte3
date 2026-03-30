<template>
    <div :id="component_id">
        <button v-if="showBackButton"
            type="button"
            class="d-inline btn btn-icon btn-icon-big mr-1"
            :title="trans(backButtonTitle)"
            @click="goBackTo()"
        >
            <i class="fa fa-arrow-left" style="font-size: 1.5em;"></i>
        </button>
        <slot name="title"></slot>
        <slot name="toolbar"
            id="titleToolbar"
        ></slot>
        <span id="customTitle"></span>
    </div>
</template>
<script>
export default {
    props: {
        showBackButton: {
            type: Boolean,
            default: false,
        },
        backButtonTitle: {
            type: String,
            default: 'global.back',
        },
        backButtonUrl: {
            type: String,
            default: null,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
        }
    },
    methods: {
        goBackTo() {
            if (this.backButtonUrl) {
                window.location.href = this.backButtonUrl;
            } else {
                window.history.back();
            }
        },
    },
}
</script>