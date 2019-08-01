<template>
   
    <div class="btn-group">
        <button  type="button" class="btn btn-default dropdown-toggle" style="background-color: transparent;" data-toggle="dropdown" aria-expanded="false">
            {{ label }} <span class="caret"></span>
        </button>
        <div class="dropdown-menu" 
             x-placement="top-start" 
             style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, -2px, 0px);">
            <span v-for="entry in entries">
                <hr v-if="entry.hr === true ">
                <button class="dropdown-item" @click="show(model, entry)">
                    <i class="mr-4" v-if="entry.icon" v-bind:class="entry.icon"></i>
                    {{ entry.title }}
                </button>
            </span>

        </div>
    </div>

</template>


<script>
    export default {
        props: {
            'label': '', 
            'entries': {},
            'model': '',
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
