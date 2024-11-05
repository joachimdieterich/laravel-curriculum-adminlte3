<template>
    <div :id="component_id">
        <slot name="title"></slot>
        <slot id="titleToolbar"
              name="toolbar"
        ></slot>
        <div id="customTitle"></div>
    </div>
</template>

<script>
export default {
    props: {},
    data() {
        return {
            component_id: this.$.uid,
        }
    },
    mounted() {},
    methods: {}
}
</script>
