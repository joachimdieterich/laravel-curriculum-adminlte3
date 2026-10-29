<template>
    <tag-marker
        :url="url"
        :titleMarked="trans('global.tag.add_favourite')"
        :titleUnmarked="trans('global.tag.remove_favourite')"
        faIconMarked="fa fa-heart"
        faIconUnmarked="far fa-heart"
        :is-marked="isFavourited"
        :model="model"
        :class="classes"
        @mark-status-changed="(newStatus) => {
            this.$emit('favour-status-changed', newStatus)
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
            title: "If the model is already favourited",
        },
        classes: {
            type: String,
            default: 'btn btn-icon',
        },
    },
}
</script>