<template>
    <modal 
        id="content-modal" 
        name="content-modal" 
        height="auto" 
        width="70%"
        :adaptive=true
        :scrollable=true
        :draggable=true
        :resizable=true
        @before-open="beforeOpen"
        @before-close="beforeClose"
        style="z-index: 25100">
        <div class="card" style="margin-bottom: 0px !important">
            <div class="card-header">
                 <h3 class="card-title">
                    {{ content.title }}
                 </h3>
                
                 <div class="card-tools">
                     <button type="button" class="btn btn-tool" data-widget="remove" @click="close()">
                        <i class="fa fa-times"></i>
                     </button>
                 </div>
              
            </div>
            
            <div class="card-body"  v-html="content.content">
                {{ content.content }}
            </div>
                <div class="card-footer">
                     <div class="form-group m-2">
                         <button type="button" class="btn btn-info" data-widget="remove" @click="close()">{{ trans('global.cancel') }}</button>
                         <button class="btn btn-info" @click="submit()" >{{ trans('global.save') }}</button>
                    </div>
                </div>
            
        </div>
    </modal>
</template>

<script>
    
    export default {
        
        data() {
            return {
                content: [],
                quote: null,
                errors: {}
            }
        },
        methods: {
            beforeOpen(event) {
                if (event.params.content) {
                    this.content = event.params.content;
                    console.log(event.params.quote);
                    if (event.params.quote) {
                        this.quote = event.params.quote;
                    }
                }
            },
            beforeClose() {
            },
            close(){
                this.$modal.hide('content-modal');
            }
        },
        
    }
</script>