<template> 
    <div class="col-12">
       <TerminalObjectives 
           :curriculum="curriculum"
           :terminalobjectives="terminalobjectives" 
           :enablingobjectives="ena"
           :objectivetypes="objectivetypes"
           :settings="settings">                     
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
            'settings': Object,
        }, 
        data () {
            return {
                ena: false,
            };
        }, 
        
        methods: {
           externalEvent: function(ids) {
               
               this.reloadEnablingObjectives(ids);
            },
            async reloadEnablingObjectives(ids) {
                try {                    
                    this.ena = (await axios.post('/curricula/2/achievements', {'user_ids' : ids})).data.enablingobjectives;
                } catch(error) {
                    this.errors = error.response.data.errors;
                } 
            }, 
        },
        created() {
            this.ena = this.enablingobjectives;
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