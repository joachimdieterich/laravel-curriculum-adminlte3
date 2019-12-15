<template>
    <modal 
        id="objective-description-modal" 
        name="objective-description-modal" 
        height="auto" 
        :adaptive=true
        :scrollable=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen"
        @before-close="beforeClose"
        style="z-index: 25000">
        <div class="card" style="margin-bottom: 0px !important">
            <div class="card-header">
                 <h3 class="card-title">
                    {{ trans('global.information') }}
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
            
            <div class="card-body" style="max-height: 80vh; overflow-y: auto;"
                 v-html="content.description">
                {{ content.description }}
            </div>
            
            <div class="card-footer">
                <span class="pull-right">
                    <button type="button" class="btn btn-primary" data-widget="remove" @click="close()">{{ trans('global.close') }}</button>
                </span>
            </div>
            
        </div>
    </modal>
</template>

<script>
    import Form from 'form-backend-validation';
    
    export default {
        
        data() {
            return {
                content: [],
                errors: {}
            }
        },
        methods: {
            beforeOpen(event) {
                if (event.params.content) {
                    //console.log(event.params.objective);
                    this.content = event.params.content;
                }
            },
            beforeClose() {
                //console.log('close')
            },
            close(){
                this.$modal.hide('objective-description-modal');
            }
        },
        
    }
</script>