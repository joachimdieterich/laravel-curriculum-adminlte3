<template>
    <modal
        id="medium-export-modal"
        name="medium-export-modal"
        height="auto"
        :adaptive=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen"
        @opened="opened"
        style="z-index: 1200">
        <div class="card"
             style="margin-bottom: 0px !important">
            <div class="card-header">
                 <h3 class="card-title" v-html="this.header"></h3>

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
                {{ trans('global.waitForFile') }}
            </div>

            <div class="card-footer">
                <span class="pull-right">
                     <button id="btn_generate"
                             v-if="download_url == null"
                             class="btn btn-primary"  >
                         <div  class="text-center text-white">
                            <i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>
                            <span class="sr-only">Loading...</span>
                         </div>
                     </button>

                     <a v-if="download_url" id="btn_download"
                        class="btn btn-primary hidden"
                        :href="download_url"
                        target="_blank"
                        @click="$modal.hide('medium-export-modal')">
                         <i class="fa fa-download"></i>
                         {{ trans('global.downloadFile') }}
                     </a>
                </span>
            </div>
        </div>
    </modal>
</template>

<script>
    export default {
        data() {
            return {
                id: Number,
                url: String,
                header: String,
                download_url: null
            }
        },
        methods: {
            process() {
                this.download_url = null;
                axios.get(this.url)
                     .then((response) => {
                         this.download_url = response.data.path;
                     })
                    .catch(e => {

                    });
            },

            beforeOpen(event) {
                this.id = null;
                if  (event.params.id){
                    this.id = event.params.id;
                }
                this.url = event.params.url;
                this.header = event.params.header;

             },
            opened() {
                this.process()
            },
            close(){
                this.$modal.hide('medium-export-modal');
            },
        },
    }
</script>

