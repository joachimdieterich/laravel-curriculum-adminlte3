<template>
    <tag-marker
        :url="url"
        :text-marked="trans('global.remove_hide')"
        :text-unmarked="trans('global.hide')"
        faIconMarked="fa fa-eye"
        faIconUnmarked="fa fa-eye-slash"
        :is-marked="isHidden"
        :model="model"
        :class="classes"
        @mark-status-changed="(newStatus) => {
            this.$emit('hide-status-changed', newStatus)
        }"
    />
</template>
<script>
import TagMarker from "./TagMarker.vue";

export default {
    name: "Hide",
    components: {TagMarker},
    emits: ['hide-status-changed'],
    props: {
        url: {
            type: String,
            required: true,
            title: "Url to attach/detach tag to a model. E.g. /kanbans/[id]/hide. The [id] is important and reserved for the modelID."
        },
        model: {
            type: Object,
            required: true,
        },
        isHidden: {
            type: Boolean,
            title: "If the model is already hidden",
        },
        classes: {
            type: String,
            default: 'dropdown-item',
        },
    },
}
</script>