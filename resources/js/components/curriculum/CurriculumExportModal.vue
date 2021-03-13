<template>
    <modal
        id="curriculum-export-modal"
        name="curriculum-export-modal"
        height="auto"
        :adaptive=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen"
        @opened="opened"
        @before-close="beforeClose"
        style="z-index: 1000">
        <div class="card"
             style="margin-bottom: 0px !important">
            <div class="card-header">
                 <h3 class="card-title">
                    {{ trans('global.curriculum.export') }}
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


            </div>

            <div class="card-footer">
                <span class="pull-right">
                     <button type="button" class="btn btn-default" data-widget="remove" @click="close()">{{ trans('global.close') }}</button>
                     <button class="btn btn-primary" @click="submit()" >{{ trans('global.save') }}</button>
                </span>
            </div>
        </div>
    </modal>
</template>

<script>
    export default {
        data() {
            return {
                id: Number
            }
        },
        methods: {
            async load(id) {
                try {
                    //this.form.populate((await axios.get('/tasks/'+id)).data.message);
                } catch(error) {
                    //console.log('loading failed')
                }
            },

            process() {
                axios.get('/curricula/' + this.id + '/export')
                     .then((response) => {
                         this.$modal.hide('curriculum-export-modal');
                     });
            },

            beforeOpen(event) {
                if  (event.params.id){
                    this.id = event.params.id;
                    this.process()
                }
             },
            opened(){},
            beforeClose(event) {},

            close(){
                this.$modal.hide('curriculum-export-modal');
            },


        },
        components: {

        },
    }
</script>

