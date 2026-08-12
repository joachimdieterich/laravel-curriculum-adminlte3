<template>
    <button
        :title="active ? titleUnmarked : titleMarked"
        type="button"
        @click.prevent.stop="active = !active"
    >
        <i :class="(active ? faIconMarked : faIconUnmarked) + ' ' + iconClass"></i>
        {{ !active ? this.textUnmarked : this.textMarked }}
    </button>
</template>
<script>
export default {
    name: "TagMarker",
    emits: ['mark-status-changed'],
    props: {
        faIconMarked: {
            type: String,
            required: true,
            title: "Font awesome icon if model is marked",
        },
        faIconUnmarked: {
            type: String,
            required: true,
            title: "Font awesome icon if model is unmarked",
        },
        url: {
            type: String,
            required: true,
            title: "Url to attach/detach tag to a model. E.g. /kanbans/[id]/favour. The [id] is important and reserved for the modelID",
        },
        model: {
            type: Object,
            required: true,
        },
        iconClass: {
            type: String,
            default: '',
        },
        isMarked: {
            type: Boolean,
            title: "If the model is already marked",
        },
        titleMarked: {
            type: String,
            default: '',
            title: "Accessibility-text for button if it is marked (only needed if text is empty)",
        },
        titleUnmarked: {
            type: String,
            default: '',
            title: "Accessibility-text for button if it is unmarked (only needed if text is empty)",
        },
        textMarked: {
            type: String,
            default: '',
            title: "Button-text if it is marked",
        },
        textUnmarked: {
            type: String,
            default: '',
            title: "Button-text if it is unmarked",
        },
    },
    data: function () {
        return {
            active: undefined
        };
    },
    mounted() {
        this.active = this.isMarked;
    },
    watch: {
        active: function (newValue, oldValue) {
            if (oldValue === undefined) {
                return;
            }

            axios.post(this.url.replace('[id]', this.model.id), {
                mark: newValue,
            }).then((response) => {
                this.$emit('mark-status-changed', response.data);
            }).catch(err => {
                console.log(err);
            });
        }
    }
}
</script>