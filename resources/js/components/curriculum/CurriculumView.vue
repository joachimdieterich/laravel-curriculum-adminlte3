<template>
    <div class="col-12">
       <TerminalObjectives 
           :curriculum="curriculum"
           :terminalobjectives="terminalobjectives" 
           :enablingobjectives="enablingobjectives"
           :objectivetypes="objectivetypes" >                     
       </TerminalObjectives>   
    </div>
</template>

<script>
    import TerminalObjectives from '../objectives/TerminalObjectives.vue'
    
    export default {
        props: {
            'curriculum': Object,
            'terminalobjectives': Array, 
            'enablingobjectives': Array,       
            'objectivetypes': Array,       
        }, 
        methods: {
            
        },
        mounted() {
            
            this.$on('addTerminalObjective', function(newTerminalObjective) {
                console.log(newTerminalObjective);
             });
        },
        components: {
            TerminalObjectives
        }
        
    }
</script>