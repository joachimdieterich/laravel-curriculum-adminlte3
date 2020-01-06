<template >
    
    
    <div
        v-bind:id="'medium_'+medium.id" 
        class="box box-objective pointer my-1" 
        style="height: 300px !important; min-width: 200px !important; padding: 0; background-size: cover;"
        :style="{'background-image':'url('+href+')'}"
        @click="show('medium', medium)"
    >    
        <div class="symbol" 
             style="position: absolute;
                padding: 6px;
                z-index: 1;
                width: 30px;
                height: 40px;
                background-color: #0583C9;
                top: 0px;
                font-size: 1.2em;
                left: 10px;">
            
            <i v-if="medium.mime_type === 'application/pdf'" class="fa fa-file-pdf text-white pt-2"></i>
            
            <i v-else class="fa fa-photo-video text-white pt-2"></i>
        </div>
        <i v-if="medium.mime_type === 'application/pdf'" class="far fa-file-pdf text-primary text-center pt-2" 
           style="position:absolute; top: 0px; height: 150px !important; width: 100%; font-size:800%;"></i>
          
        <span class="bg-white text-center p-1 overflow-auto " 
            style="position:absolute; bottom:0px; height: 150px; width:100%;">
            <h6 class="events-heading pt-1 hyphens" v-html="medium.title"></h6>
            <p class=" text-muted small" v-html="medium.description"></p>
       </span>

    </div>
</template>


<script>
    
    export default {
        props: {
                subscription: {},
                medium: {},
              },
        data() {
            return {
                errors: {}
            }
        },
        methods: {
           show(model, entry) {   
                this.$modal.show(model.toLowerCase()+'-modal', { 'content': entry});
            },
            
        },
        computed: {
            href: function () {
                return '/media/'+ this.subscription.medium_id;
            },
            href: function () {
                return '/media/'+ this.subscription.medium_id;
            },
        },
        bevorOpen() {
             axios.get('/media/'+this.subscription.medium_id ).then(response => {
                this.sharingLevels = response.data.sharingLevel;
            }).catch(e => {
                this.errors = error.response.data.errors;
            });
        },
   
    }
</script>

<style>
/*styles (xs, sm) */
@media (max-width: 990px) {
  
}
/* /.sm view */
/*styles (md, lg) */
@media (min-width: 991px) {
  
}
</style>
