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
        style="z-index: 1300">
        <div class="card" style="margin-bottom: 0px !important">
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
                     <button v-can="'medium_edit'"
                             v-if="edit == true"
                             type="button"
                             class="btn btn-tool text-success"
                             @click="saveMedium()">
                         <i class="fa fa-save"></i>
                     </button>
                     <button v-can="'medium_edit'"
                             v-if="edit != true"
                             type="button"
                             class="btn btn-tool"
                             @click="edit_title()">
                         <i class="fa fa-pencil-alt"></i>
                     </button>
                     <button v-can="'medium_delete'"
                             type="button"
                             class="btn btn-tool"
                             @click="del()">
                         <i class="fa fa-trash text-danger"></i>
                     </button>
                     <button type="button"
                             class="btn btn-tool draggable" >
                        <i class="fa fa-arrows-alt"></i>
                     </button>
                     <button type="button" class="btn btn-tool" data-widget="remove" @click="close()">
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
                    v-html="medium.description"
                    class="text-muted text-sm p-2"></div>
                <div v-if="mime(medium.mime_type) === 'embed'"
                     style="height:500px">
                    <iframe :src="scr" height="500" width="600" frameborder="0"></iframe>
                </div>
                <div v-else-if="mime(medium.mime_type) === 'img'">
                    <img :src="scr" width="600"/>
                </div>
                <div v-else>
                    - Please download file -
                </div>
            </div>

            <div class="card-footer">
                <license class="pull-left pr-2"
                         :licenseId="medium.license_id">
                </license>
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
                         <a :href="scr + '?download=true'" class="text-white text-decoration-none" target="_blank">{{ trans('global.downloadFile') }}</a>
                     </button>
                </span>
            </div>

        </div>
    </modal>
</template>

<script>
import License from '../uiElements/License'
    export default {

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
            beforeOpen(event) {
                if (event.params.content) {
                    this.medium = event.params.content;
                }
                if (event.params.subscribable) {
                    this.subscribable = event.params.subscribable;
                }
                if (event.params.subscribable_type) {
                    this.subscribable_type = event.params.subscribable_type;
                }
                if (event.params.subscribable_id) {
                    this.subscribable_id = event.params.subscribable_id;
                }
            },
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
                    // default use <embed>

                    default:
                        return 'embed';
                        break;
                }
            },
            async del(){
                try {
                    var response =  await axios.post('/media/'+this.medium.id+'/destroy',  { 'subscribable': this.subscribable, 'subscribable_type': this.subscribable_type,'subscribable_id': this.subscribable_id } );
                }
                catch(error) {
                    this.errors = response.data.errors;
                }
                location.reload();
            },
            edit_title: function() {
                this.edit = !this.edit;
            },
            async saveMedium(){
                await axios.patch('/media/' + this.medium.id, {'title': this.medium.title, 'description': this.medium.description})
                .then(response =>{
                    this.medium = response.data.message;
                    this.edit_title();
                })
                .catch(error => {
                    //alert(error.response.data.errors);
                    this.errors = error.response.data.errors;
                });
            },
            beforeClose() {

            },
            close() {
                this.$modal.hide('medium-modal');
            }

        },
        computed: {
            scr: function () {
                return '/media/' + this.medium.id ;
            },
        },
        components: {
            License
        }

    }
</script>
