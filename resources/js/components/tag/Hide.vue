<template>
    <tag-marker
        :url="url"
        :title-active="trans('global.remove_hide')"
        :title-inactive="trans('global.hide')"
        :text-active="trans('global.remove_hide')"
        :text-inactive="trans('global.hide')"
        fa-icon-active="fa fa-eye"
        fa-icon-inactive="fa fa-eye-slash"
        :is-marked="isHidden"
        :model="model"
        :button-class="{'dropdown-item': true, 'text-secondary': true}"
        :icon-class="{'mr-2': true}"
        @mark-status-changed="(newModel) => {
            this.$emit('hide-status-changed', newModel)
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
            title: "If the model is already hidden"
        },
    },
}
</script>