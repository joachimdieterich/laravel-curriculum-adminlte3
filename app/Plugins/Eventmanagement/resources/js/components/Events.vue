<template >
    <events ref="eventPlugin" 
        :model="model"
        :curriculum="curriculum"
            >    
    </events>
</template>


<script>
    import events from '../../../eVewa/resources/js/components/Events';
    export default {
        props: {
            model: Object,
            curriculum: Object,
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