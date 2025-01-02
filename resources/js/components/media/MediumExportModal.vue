<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask">
            <div class="modal-container">
                <div class="card-header">
                     <h3 class="card-title" v-dompurify-html="this.header"></h3>

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
                    <span  v-if="download_url == null">
                        {{ trans('global.waitForFile') }}
                    </span>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                         <button
                             id="btn_generate"
                             v-if="download_url == null"
                             class="btn btn-primary"  >
                             <div  class="text-center text-white">
                                <i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>
                                <span class="sr-only">Loading...</span>
                             </div>
                         </button>

                         <a v-if="download_url"
                            id="btn_download"
                            class="btn btn-primary hidden"
                            :href="download_url"
                            target="_blank"
                            @click="globalStore?.closeModal($options.name)">
                             <i class="fa fa-download"></i>
                             {{ trans('global.downloadFile') }}
                         </a>
                    </span>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script>
import {useGlobalStore} from "../../store/global";
    export default {
        name: 'medium-export-modal',
        components: {},
        props: {},
        setup() {
            const globalStore = useGlobalStore();
            return {
                globalStore,
            }
        },
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
                        console.log(e);
                    });
            },
        },
        mounted() {
            this.globalStore.registerModal(this.$options.name);
            this.globalStore.$subscribe((mutation, state) => {

                if (state.modals[this.$options.name].show) {
                    const params = state.modals[this.$options.name].params;
                    if (typeof (params) !== 'undefined') {
                        if  (params.id){
                            this.id = params.id;
                        }
                        this.url = params.url;
                        this.header = params.header;

                    }
                    this.process();
                }
            });
        },
    }
</script>

