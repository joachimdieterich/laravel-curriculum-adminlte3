<template>
    <modal 
        id="curriculum-description-modal" 
        name="curriculum-description-modal" 
        height="auto" 
        :adaptive=true
        :scrollable=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen"
        style="z-index: 1100">
        <div class="card" style="margin-bottom: 0px !important">
            <div class="card-header">
                 <h3 class="card-title">
                    {{ trans('global.information') }}
                 </h3>
                
                 <div class="card-tools">
                    <button type="button" 
                            class="btn btn-tool draggable">
                        <i class="fa fa-arrows-alt"></i>
                    </button>
                    <button type="button" 
                            class="btn btn-tool" 
                            data-widget="remove" @click="close()">
                        <i class="fa fa-times"></i>
                    </button>
                 </div>
            </div>
            
            <div class="card-body" style="max-height: 80vh; overflow-y: auto;"
                 v-html="description">
            </div>
            
            <div class="card-footer">
                <span class="pull-right">
                    <button type="button" 
                            class="btn btn-primary" 
                            data-widget="remove" 
                            @click="close()">
                        {{ trans('global.close') }}
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
                description: [],
                errors: {},
            }
        },
        methods: {
            beforeOpen(event) {
                if (event.params.description) {
                    this.description = event.params.description;
                }
            },
            close(){
                this.$modal.hide('curriculum-description-modal');
            }
        }
        
    }
</script>