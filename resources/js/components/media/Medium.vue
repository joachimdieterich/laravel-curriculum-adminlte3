<template >
    
    <div class="info-box">
        <span class="info-box-icon bg-info">
            <i class="far fa-file-alt"></i>
        </span>

        <div class="info-box-content">
            <span class="font-bold info-box-text">
                 <a  @click="show('medium', medium)">
                    {{ medium.title }}
                </a>
            </span>
            <span class="info-box-text">{{ medium.medium_name}}</span>
        </div>
        <!-- /.info-box-content -->
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
                test:  [],
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
