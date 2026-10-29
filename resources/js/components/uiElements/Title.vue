<template>
    <div
        :id="component_id"
        class="d-flex align-items-center"
    >
        <button v-if="showBackButton"
            type="button"
            class="d-inline btn btn-icon btn-icon-big me-1"
            data-bs-toggle="tooltip"
            :data-bs-title="trans(backButtonTitle)"
            @click="goBackTo()"
            @click.middle="goBackTo(true)"
        >
            <i class="fa fa-arrow-left" style="font-size: 1.5em;"></i>
        </button>
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
        goBackTo(newTab = false) {
            const target = newTab ? '_blank' : '_self';

            if (this.backButtonUrl) {
                window.open(this.backButtonUrl, target)
            } else {
                window.open(window.history.back(), target);
            }
        },
    },
}
</script>