<template>
        <div class="boxheader row" 
             v-bind:style="{ 'color': textcolor }" >
            <span class="mr-auto "
                  >
                <span  v-if="(type == 'terminal' && objective.order_id != 0) " 
                       class="fa fa-arrow-up mr-1"                        
                       @click="$emit('eventSort','-1')"></span>
                <span  v-if="(type == 'enabling'  && objective.order_id != 0)" 
                    class="fa fa-arrow-left mr-1 " 
                    @click="$emit('eventSort','-1')">
                </span>
                
                <span  v-if="(type == 'terminal' && setting.last != objective.id)" 
                       class="fa fa-arrow-down  " 
                       @click="$emit('eventSort','1')"></span>
                <span  v-if="(type == 'enabling' && setting.last != objective.id)" 
                       class="fa fa-arrow-right  " 
                    @click="$emit('eventSort','1')">
                </span>
            </span>
            <span class="ml-auto" >  
                <DropdownButton v-if="type == 'terminal'" 
                                @eventDelete="emitDeleteEvent" 
                                :menuEntries="menuEntries" 
                                :objective.sync="objective"
                                :textcolor="textcolor"/>
                <DropdownButton v-else-if="type == 'enabling'" 
                                @eventDelete="emitDeleteEvent" 
                                :menuEntries="menuEntries" 
                                :objective.sync="objective"
                                :textcolor="textcolor"/>
            </span>
            
        </div>
     
</template>


<script>
    import DropdownButton from './DropdownButton'
    export default {
        props: ['objective', 'type', 'menuEntries', 'setting', 'textcolor'],
       
        methods: {
            emitDeleteEvent() {
                //this.$emit('eventtriggered')
            },   
        },
      
        components: {
            DropdownButton, 
        },
       
        }
</script>
