<template>
    <div v-html="this.html"></div>
</template>

<script>
    export default {
        props: {
            html: '',
        },
        data() {
            return {
                component_id: this.$.uid,
            }
        },
        methods: {},
        mounted() {}
    }
</script>
