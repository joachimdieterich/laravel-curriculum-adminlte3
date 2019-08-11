<template >
   
        <div class="boxfooter row" v-bind:style="{ 'color': textcolor }">
            <span v-if="objective.media != 0 || objective.references != 0" class="mr-auto">
                <span class="fa fa-briefcase mr-1"
                    @click.prevent="showModal('objective-medium-modal')">
                </span>
            </span>
            <span class="ml-auto ">  
                <span class="fa fa-info ml-1 " 
                    @click.prevent="showModal('objective-description-modal')">
                </span>
            </span>
        </div>
     
</template>


<script>
    
    export default {
        props: {
                objective: {},
                textcolor: {default: '#000'},
                type:{}
            },
        methods: {
            showModal(modal) { 
                this.$modal.show(modal, {'content': this.objective, 'type': this.type});
            },
        },
    
    }
</script>
