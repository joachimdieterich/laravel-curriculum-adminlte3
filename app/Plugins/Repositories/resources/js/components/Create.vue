<template >
    <create :model="model">    
    </create>
</template>


<script>
    import create from '../../../edusharing/resources/js/components/Create';
    export default {
        props: {
                model: {},
              },
        data() {
            return {
                errors: {}
            }
        },
        methods: {
          
        },
        computed: {
          
        },
        bevorOpen() {
            
        },
        components: {
            create,
        }
    }
</script>