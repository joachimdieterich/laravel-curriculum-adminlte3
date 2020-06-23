<template >
    <events ref="eventPlugin" :model="model">    
    </events>
</template>


<script>
    import events from '../../../eVewa/resources/js/components/Events';
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
            loader: function() {
                this.$refs.eventPlugin.loader();
            }
        },
        computed: {
          
        },
        bevorOpen() {
            
        },
        components: {
            events,
        }
    }
</script>