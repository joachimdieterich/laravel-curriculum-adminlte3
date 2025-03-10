<template>
    <div :id="component_id">
        <slot name="title"></slot>
        <slot name="toolbar"
            id="titleToolbar"
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
}
</script>