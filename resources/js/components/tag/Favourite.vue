<template>
    <tag-marker
        :url="url"
        :title-active="trans('global.remove_favourite')"
        :title-inactive="trans('global.add_favourite')"
        text-active=""
        text-inactive=""
        fa-icon-active="fa fa-heart"
        fa-icon-inactive="far fa-heart"
        :is-marked="isFavourited"
        :model="model"
        :button-class="{'btn': true, 'btn-icon': true, 'px-2': true, 'py-1': true}"
        :icon-class="{}"
        @mark-status-changed="(newModel) => {
            this.$emit('favour-status-changed', newModel)
        }"
    />
</template>
<script>
import TagMarker from "./TagMarker.vue";

export default {
    name: "Favourite",
    components: {TagMarker},
    emits: ['favour-status-changed'],
    props: {
        url: {
            type: String,
            required: true,
            title: "Url to attach/detach tag to a model. E.g. /kanbans/[id]/favour. The [id] is important and reserved for the modelID."
        },
        model: {
            type: Object,
            required: true,
        },
        isFavourited: {
            type: Boolean,
            title: "If the model is already favourited"
        },
    },
}
</script>