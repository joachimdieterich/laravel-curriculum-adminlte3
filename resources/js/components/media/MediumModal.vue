<template>
    <modal 
        id="medium-modal" 
        name="medium-modal" 
        height="auto" 
        width="70%"
        :adaptive=true
        :scrollable=true
        :draggable=true
        :resizable=true
        @before-open="beforeOpen"
        @before-close="beforeClose"
        style="z-index: 25000">
        <div class="card" style="margin-bottom: 0px !important">
            <div class="card-header">
                 <h3 class="card-title">
                    {{ medium.title }}
                 </h3>
                
                 <div class="card-tools">
                     <button type="button" class="btn btn-tool" data-widget="remove" @click="close()">
                        <i class="fa fa-times"></i>
                     </button>
                 </div>
              
            </div>
            
            <div class="card-body" >
                <embed :src="scr" width="100%" height="600px" class="full-height">
            </div>
                <div class="card-footer">
                     <div class="form-group m-2">
                         <button type="button" class="btn btn-info" data-widget="remove" @click="close()">{{ trans('global.cancel') }}</button>
                         <button class="btn btn-info" @click="download()" >{{ trans('global.downloadFile') }}</button>
                    </div>
                </div>
            
        </div>
    </modal>
</template>

<script>
    
    export default {
        
        data() {
            return {
                medium: [],
                errors: {}
            }
        },
        methods: {
            beforeOpen(event) {
                if (event.params.content) {
                    console.log(event.params.content);
                    this.medium = event.params.content;
                }
            },
            beforeClose() {
                 
            },
            download() {
                return '/media/'+ this.medium.id +'?download';
            },
            close(){
                this.$modal.hide('medium-modal');
            }
            
        },
        computed: {
            scr: function () {
                return '/media/'+ this.medium.id;
            },
        },
        
    }
</script>