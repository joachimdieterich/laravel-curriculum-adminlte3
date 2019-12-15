<template>
    <modal 
        id="content-modal" 
        name="content-modal" 
        height="auto" 
        :adaptive=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen"
        @opened="opened"
        @before-close="beforeClose"
        style="z-index: 1200">
        <div class="card" 
             style="margin-bottom: 0px !important">
            <div class="card-header">
                 <h3 class="card-title">
                    {{ content.title }}
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
                 v-html="content.content">
                {{ content.content }}
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
                    //console.log(event.params.quote);
                    if (event.params.quote) {
                        this.quote = event.params.quote;   
                    }
                }
            },
            opened(event){
                this.$nextTick(function () {
                    document.getElementById('quote_'+this.quote).scrollIntoView({ block: 'start', behavior: 'smooth' })
                });
               
            },
            beforeClose() {
            },
            close(){
                this.$modal.hide('content-modal');
            }
        },
        
        
        
    }
</script>