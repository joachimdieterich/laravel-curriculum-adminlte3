<template>
    <div class="col-12">

        <div id="loading" class="overlay text-center" style="width:100% !important;">
            <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
            <span class="sr-only">Loading...</span>
        </div>

        <table
            id="sidebar_media_datatable"
            class="table table-hover datatable media_table">
            <tr v-for="medium in media">
                <td style="overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"
                    class="link-muted text-sm px-2 pointer"
                    @click="show(medium)">

                    <i class="pr-2"
                       v-bind:class="[iconCss(medium.mime_type)]"></i>
                    {{ medium.title }}
                </td>
                <td>
                    <i class="fa fa-trash text-danger pull-right"
                       @click="unlinkMedium(medium)"></i>
                </td>
            </tr>
            <tr>
                <td  class="link-muted text-sm px-2 pointer"
                     @click="addMedia()">
                    <i class="fa fa-plus pr-2"></i>{{ trans('global.videoconference.add_presentation') }}
                </td>
            </tr>
        </table>
    </div>
</template>


<script>
import MediumForm from "../media/MediumForm";

    export default {
        props: {
                'model': {},
              },
        data() {
            return {
                component_id: this._uid,
                media:   null,
                page:    0,
                maxItems: 20,
                errors:  {},
                currentTab: 1,
                medium_id: ''
            }
        },
        methods: {
            addMedia() {
                this.$modal.show(
                    'medium-create-modal',
                    {
                        'referenceable_type': 'App\\\Videoconference',
                        'referenceable_id': this.model.id,
                        'subscribeSelected': true,
                        'eventHubCallbackFunction': 'reload_videoconference',
                        'eventHubCallbackFunctionParams': this.model.id,

                    });
            },
            unlinkMedium(medium) { //id of external reference and value in db
                axios.delete('/media/' + medium.id, {
                    data: {
                        subscribable_type: 'App\\\Videoconference',
                        subscribable_id: this.model.id }
                     })
                    .then(res => {
                        let index =  this.media.indexOf(medium);
                        this.media.splice(index, 1);
                    })
                    .catch(err => {
                        console.log(err.response);
                    });
            },
            show(medium) {
                window.open(medium.path, '_blank');
            },
            iconCss(mimeType) {
                switch (true) {
                    case mimeType.startsWith("image"):
                        return "fa fa-file-image";
                        break;
                    case mimeType.startsWith("video"):
                        return "fa fa-file-video";
                        break;
                    case mimeType.startsWith("application/pdf"):
                        return "fa fa-file-pdf";
                        break;
                    default:
                        return "fa fa-file";
                        break;
                }
            },

        },
        mounted() {
            //this.loader();
            this.media = this.model.media;
            this.$eventHub.$on('reload_videoconference', (e) => {
                if (this.model.id == e.id) {
                    this.media.push(...e.files);
                    //console.log(e.selectedMediumId + ' ' +e.files)
                }
            });
        },
        watch: {
            media: function (value, oldValue) {
                $("#loading").hide();
            }
        },
        components: {
            MediumForm,
        },

    }
</script>
