<template>
    <span>
        <button type="button" 
                class="btn btn-default btn-flat dropdown-toggle" 
                style="background-color: transparent;" 
                v-bind:style="styles"
                data-toggle="dropdown" 
                aria-expanded="false">
            {{ label }} 
            <span class="caret"></span>
            <div class="dropdown-menu" 
                x-placement="top-start">
               <span v-for="entry in entries">
                   <hr v-if="entry.hr === true ">
                   <button class="dropdown-item" @click="show(model, entry)">
                       <i class="mr-4" v-if="entry.icon" v-bind:class="entry.icon"></i>
                       {{ entry.title }}
                   </button>
               </span>
            </div>
        </button>
    </span>
</template>

<script>
    export default {
        props: {
            'label': '', 
            'entries': {},
            'model': '',
            'styles': ''
        },
        
        
        methods: {
            show(model, entry) {   
                this.$modal.show(model.toLowerCase()+'-modal', { 'content': entry});
            },
           
        },
        mounted(){
            
        }
        
    }
</script>
