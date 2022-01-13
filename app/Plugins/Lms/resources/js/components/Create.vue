<template>
    <create :model="model">
    </create>
</template>


<script>
import create from '../../../moodle/resources/js/components/Create';

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
