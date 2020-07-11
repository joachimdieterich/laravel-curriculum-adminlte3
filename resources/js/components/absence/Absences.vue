<template>  
    
    <ul class="todo-list" data-widget="todo-list">
                            
        <Absence 
            v-for="absence in absences"
             v-bind:data="absence"
            v-bind:key="'absence_container'+absence.id"
            :absence="absence"></Absence>

    </ul>
      
</template>

<script>
    import Absence from '../absence/Absence.vue'
    export default {
        props: {
            'absences': Array,
        },
        data() {
            return {
            }
        },
        
        methods: {
        },
        
        mounted(){
            
        },
        components: {
            Absence
        }
        
    }
</script>
