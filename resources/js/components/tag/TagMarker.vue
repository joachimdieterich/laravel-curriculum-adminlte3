<template>
    <button
        :class="buttonClass"
        :title="active ? titleInactive : titleActive"
        @click.prevent="active = !active"
    >
        <i v-if="active"
           :class="activeIconClass()"
        ></i>
        <i v-else
           :class="inactiveIconClass()"
        ></i>
        {{ !active ? this.textInactive : this.textActive }}
    </button>
</template>
<script>
export default {
    name: "TagMarker",
    emits: ['mark-status-changed'],
    props: {
        faIconActive: {
            type: String,
            required: true,
            title: "Font awesome icon if active"
        },
        faIconInactive: {
            type: String,
            required: true,
            title: "Font awesome icon if inactive"
        },
        url: {
            type: String,
            required: true,
            title: "Url to attach/detach tag to a model. E.g. /kanbans/[id]/favour. The [id] is important and reserved for the modelID."
        },
        model: {
            type: Object,
            required: true,
        },
        buttonClass: {
            type: Object,
            default: {'btn': true, 'btn-icon': true, 'px-2': true, 'py-1': true}
        },
        iconClass: {
            type: Object,
            default: {'fa': true, 'btn-icon': true, 'px-2': true, 'py-1': true}
        },
        isMarked: {
            type: Boolean,
            title: "If the model is already marked"
        },
        titleActive: {
            type: String,
            title: "Title if it's active"
        },
        titleInactive: {
            type: String,
            title: "Title if it's inactive"
        },
        textActive: {
            type: String,
            title: "Title if it's active"
        },
        textInactive: {
            type: String,
            title: "Title if it's inactive"
        }
    },
    methods: {
        activeIconClass: function () {
            let classes = {};
            classes[this.faIconActive] = true;

            for (let key in this.iconClass) {
                classes[key] = this.iconClass[key];
            }

            return classes;
        },
        inactiveIconClass: function () {
            let classes = {};
            classes[this.faIconInactive] = true;

            for (let key in this.iconClass) {
                classes[key] = this.iconClass[key];
            }

            return classes;
        }
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