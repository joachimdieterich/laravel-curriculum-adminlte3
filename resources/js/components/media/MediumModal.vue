<template>
    <modal 
        id="medium-modal" 
        name="medium-modal" 
        height="auto"
        :adaptive=true
        :scrollable=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen"
        @before-close="beforeClose"
        style="z-index: 1100">
        <div class="card" style="margin-bottom: 0px !important">
            <div class="card-header">
                 <h3 class="card-title">
                    {{ medium.title }}
                 </h3>
                
                 <div class="card-tools">
                     <button type="button" class="btn btn-tool draggable" >
                        <i class="fa fa-arrows-alt"></i>
                     </button>
                     <button type="button" class="btn btn-tool" data-widget="remove" @click="close()">
                        <i class="fa fa-times"></i>
                     </button>
                 </div>
              
            </div>
            
            <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                <embed :src="scr" width="100%" height="600px" class="full-height">
            </div>
                <div class="card-footer">
                     <span class="pull-right">
                         <button type="button" class="btn btn-info" data-widget="remove" @click="close()">{{ trans('global.cancel') }}</button>
                         <button class="btn btn-primary" @click="download()" >{{ trans('global.downloadFile') }}</button>
                    </span>
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