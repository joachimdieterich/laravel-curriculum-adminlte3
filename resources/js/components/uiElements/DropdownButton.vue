<template>
    <button
        class="btn"
        :class="buttonClass"
        type="button"
        data-bs-toggle="dropdown"
        aria-expanded="false"
        @click.stop
    >
        <i class="fa" :class="icon"></i>
        <div class="dropdown-menu dropdown-menu-end">
            <template v-for="entry in activeEntries">
                <slot v-if="typeof entry.slot === 'string'" :name="entry.slot"></slot>
                <hr v-else-if="entry.hr === true && (entry.condition === undefined || entry.condition === true)"
                    class="my-1"
                />
                <button v-else-if="(entry.condition === undefined || entry.condition === true)
                        && (entry.permission === undefined || checkPermission(entry.permission))
                    "
                    type="button"
                    class="dropdown-item"
                    :class="entry.class"
                    @click="entry.callback()"
                >
                    <i class="fa" :class="entry.icon"></i>
                    {{ trans(entry.label) }}
                </button>
            </template>
        </div>
    </button>
</template>
<script>
export default {
    props: {
        icon: {
            type: String,
            default: 'fa-ellipsis-v',
        },
        entries: {
            type: Array,
            required: true,
            description: "Array of Objects each containing 'icon', 'label', 'callback' and optionally 'condition', 'permission' and 'class'",
        },
        subscribable: {
            type: Boolean,
            default: false,
            description: "Filters entries by their 'subscribable' attribute (none => false)",
        },
        color: {
            type: String,
            default: '#27AE60', // default green-color for IndexWidgets
            description: 'Gets used to defined button-icon-class',
        },
    },
    computed: {
        activeEntries() {
            return this.entries.filter(entry => !!entry.subscribable === this.subscribable);
        },
        buttonClass() {
            return window.innerWidth <= 990
                ? 'btn-icon'
                : this.$textcolor(this.color) === '#000'
                    ? 'btn-icon'
                    : 'btn-icon-alt';
        },
    },
}
</script>