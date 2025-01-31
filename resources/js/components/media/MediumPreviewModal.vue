<template>
    <div v-if="globalStore.modals[$options.name]?.show"
         class="modal-mask">
        <div class="modal-container">
            <div class="card-header">
                <h3 class="card-title">
                     <span v-if="edit == true">
                         <input
                             id="save-medium"
                             type="text"
                             v-model="medium.title"
                             style="font-size: 1.1rem; font-weight: 400; border: 0; border-bottom: 1px; border-style:solid; margin: 0; width: 400px;"
                             @keyup.enter="saveMedium()"/>
                     </span>
                    <span v-else>{{ medium.title }}</span>
                </h3>

                <div class="card-tools">
                    <button v-permission="'medium_edit'"
                            v-if="edit == true"
                            type="button"
                            class="btn btn-tool text-success"
                            @click="saveMedium()">
                        <i class="fa fa-save"></i>
                    </button>
                    <button v-permission="'medium_edit'"
                            v-if="edit != true"
                            type="button"
                            class="btn btn-tool"
                            @click="edit_title()">
                        <i class="fa fa-pencil-alt"></i>
                    </button>
                    <button v-permission="'medium_delete'"
                            type="button"
                            class="btn btn-tool"
                            @click="del()">
                        <i class="fa fa-trash text-danger"></i>
                    </button>
                    <button type="button"
                            class="btn btn-tool draggable" >
                        <i class="fa fa-arrows-alt"></i>
                    </button>
                    <button type="button"
                            class="btn btn-tool"
                            @click="globalStore?.closeModal($options.name)">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

            </div>

            <div class="card-body"
                 style="overflow-y: auto;padding:0;">
                <div v-if="edit == true"
                     class="p-2">
                     <textarea
                         style="width:100%"
                         v-model="medium.description"/>
                </div>
                <div v-else-if="medium.description != ''"
                     v-dompurify-html="medium.description"
                     class="text-muted text-sm p-2"></div>
               <render-usage
               :medium="medium"
               >
               </render-usage>
<!--                    <div v-if="mime(medium.mime_type) === 'embed'"
                     style="height:500px">
                    <iframe :src="scr" height="500" width="600" frameborder="0"></iframe>
                </div>
                <div v-else-if="mime(medium.mime_type) === 'img'">
                    <img :src="scr" width="600"/>
                </div>
                <div v-else>
                    - Please download file -
                </div>-->


            </div>

            <div class="card-footer">
                <license class="pull-left pr-2"
                         :licenseId="medium.license_id">
                </license>
                <span class="pull-right">
                     <button type="button"
                             class="btn btn-info mr-2"
                             data-widget="remove"
                             @click="globalStore?.closeModal($options.name)">
                         {{ trans('global.close') }}
                     </button>
                     <button
                         v-if="medium.mime_type == 'url'"
                         class="btn btn-primary"
                         data-widget="remove"
                         @click="globalStore?.closeModal($options.name)">
                         <a :href="scr"
                            class="text-white text-decoration-none"
                            target="_blank">
                             {{ trans('global.open') }}
                         </a>
                     </button>
                     <button
                         v-else
                         class="btn btn-primary"
                         data-widget="remove"
                         @click="download()">
                         <a class="text-white text-decoration-none">
                             <span id="loading_spinner"
                                   style="display: none;">
                                <i class="fa fa-spinner fa-pulse fa-fw"></i>
                                <span class="sr-only">Loading...</span>
                            </span>
                             {{ trans('global.downloadFile') }}/{{ trans('global.open') }}
                         </a>
                     </button>
                </span>
            </div>
        </div>
    </div>
</template>

<script>
import License from '../uiElements/License.vue'
import RenderUsage from "../../../../app/Plugins/Repositories/edusharing/resources/js/components/RenderUsage.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'medium-preview-modal',
    props:{
        show: Boolean,
        params: {
            type: Object
        },  //{ 'modelId': curriculum.id, 'modelUrl': 'curriculum' , 'shareWithToken': true, 'canEditCheckbox': false}
    },
    setup() { //use database store
        const globalStore = useGlobalStore();

        return {
            globalStore,
        }
    },
    data() {
        return {
            medium: [],
            subscribable: null,
            subscribable_type: null,
            subscribable_id: null,
            edit: false,
            errors: {}
        }
    },
    methods: {
        mime(type) {
            switch (type) {
                //Images use <img>
                case 'image/jpg' :
                case 'image/jpeg':
                case 'image/png':
                case 'image/gif':
                case 'image/bmp':
                case 'image/tiff':
                case 'image/ico':
                case 'image/svg':
                case 'edusharing':
                    return 'img';
                    break;
                default:
                    return 'embed';
                    break;
            }
        },
        async del() {
            try {
                var response =  await axios.post('/media/'+this.medium.id+'/destroy',  { 'subscribable': this.subscribable, 'subscribable_type': this.subscribable_type,'subscribable_id': this.subscribable_id } );
            }
            catch(e) {
                console.log(e);
            }
            location.reload();
        },
        edit_title: function() {
            this.edit = !this.edit;
        },
        async saveMedium() {
            await axios.patch('/media/' + this.medium.id, {'title': this.medium.title, 'description': this.medium.description})
            .then(response => {
                this.medium = response.data.message;
                this.edit_title();
            })
            .catch(e => {
                console.log(e);
            });
        },
        download() {
            $("#loading_spinner").show();
            if (this.medium.adapter == 'local') {
                window.location.assign(this.scr + '?download=true');
            } else {
                axios.get('/media/' + this.medium.id + '?download=true')
                    .then((response) => {
                        window.location.assign(response.data.url);
                        $("#loading_spinner").hide();
                        this.globalStore?.closeModal('medium-preview-modal');
                    })
                    .catch((error) => {
                        console.log(error);
                        $("#loading_spinner").hide();
                    });
            }
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params;
                //console.log(params);
                if (typeof (params) !== 'undefined') {
                    this.medium = params;
                    /* this.subscribable= event.params.subscribable;
                        this.subscribable_type = event.params.subscribable_type;
                        this.subscribable_id = event.params.subscribable_id;*/
                }
            }
        });
    },
    computed: {
        scr: function () {
            return '/media/' + this.medium.id ;
        },
    },
    components: {
        RenderUsage,
        License
    }

}
</script>

<style>
.modal-mask {
    position: fixed;
    z-index: 9998;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    transition: opacity 0.3s ease;
}

.modal-container {
    max-width: 900px;
    margin: auto;
    padding: 0;
    background-color: #fff;
    border-radius: 2px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
    transition: all 0.3s ease;
}


.modal-default-button {
    float: right;
}

/*
 * The following styles are auto-applied to elements with
 * transition="modal" when their visibility is toggled
 * by Vue.js.
 *
 * You can easily play with the modal transition by editing
 * these styles.
 */

.modal-enter-from {
    opacity: 0;
}

.modal-leave-to {
    opacity: 0;
}

.modal-enter-from .modal-container,
.modal-leave-to .modal-container {
    -webkit-transform: scale(1.1);
    transform: scale(1.1);
}
</style>
