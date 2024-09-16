<template >
    <create :model="model">
    </create>
</template>


<script>
    import create from '../../../edusharing/resources/js/components/Create.vue';
    export default {
        props: {
                model: {},
              },
        data() {
            return {
                errors: {}
            }
        },
        methods: {},
        computed: {},
        components: {
            create,
        }
    }
</script>
