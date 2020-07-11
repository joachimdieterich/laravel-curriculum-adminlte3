<template>  
    
    <li >
            <!-- drag handle -->
<!--               <span class="handle">
                <i class="fas fa-ellipsis-v"></i>
                <i class="fas fa-ellipsis-v"></i>
            </span>-->
            <!-- checkbox -->
            <div  class="icheck-primary d-inline ml-2">
                <input 
                    type="checkbox" 
                    value="absence.done" 
                    name="'absence_'+absence.id" 
                    id="'absence_'+absence.id" 
                    @click="complete(absence.id)"
                    v-bind:checked="absence.done"
                    >

                <label for="'absence_'+absence.id" class="pl-1">{{absence.owner.firstname}} {{absence.owner.lastname}}:</label>
            </div>
            <!-- todo text -->
            <span class="text">{{ absence.reason }}</span>
            <!-- Emphasis label -->
            <small class="badge badge-primary pull-right"><i class="far fa-clock"></i> <span v-html="absence.created_at"></span></small>
            <!-- General tools such as edit or delete-->
            <div class="tools">
                <a @click="destroy()" >
                    <i class="fas fa-trash"></i>
                </a>
            </div>
        </li>
      
</template>

<script>
    export default {
        props: {
            'absence': '',
        },
        data() {
            return {
                
               
            }
        },
        
        methods: {
           async complete(absence_id) {
                try {  
                    this.absence.done = (await axios.patch('/absences/'+absence_id, {'done': (1 - this.absence.done)})).data.done;
                } catch(error) {
                    this.errors = error.response.data.errors;
                } 

           },
           async destroy() { 
                try {  
                    this.location = (await axios.delete('/absences/'+this.absence.id)).data.message;
                } catch(error) {
                    this.errors = error.response.data.errors;
                } 
                location.reload(true);
            },
             
        },
        
        mounted(){
            
        }
        
    }
</script>
