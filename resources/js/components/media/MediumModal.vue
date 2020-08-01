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
                <embed :src="scr" width="200" height="150" scale="tofit">
            </div>
            <div class="card-footer">
                 <span class="pull-right">
                     <button type="button" class="btn btn-info" data-widget="remove" @click="close()">{{ trans('global.close') }}</button>
                     <button 
                         v-if="medium.mime_type == 'url'" 
                         class="btn btn-primary" 
                         data-widget="remove" 
                         @click="close();window.open(medium.path, '_blank');">
                         <a :href="scr" class="text-white text-decoration-none" target="_blank">{{ trans('global.open') }}</a>
                     </button>
                     <button 
                         v-else
                         class="btn btn-primary" 
                         data-widget="remove" 
                         @click="close();window.open(medium.path, '_blank');">
                         <a :href="scr" class="text-white text-decoration-none" target="_blank">{{ trans('global.downloadFile') }}</a>
                     </button>
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
                    this.medium = event.params.content;
                }
            },
            beforeClose() {
                 
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