<template >
<div>
    <div v-if="format =='list'">
        <table
               id="sidebar_media_datatable"
               class="table table-hover datatable media_table">
            <tr v-for="subscription in subscriptions">
                <td style="overflow: hidden;text-overflow: ellipsis;white-space: nowrap; max-width: 100px;"
                    class="link-muted text-sm px-2 pointer"
                    @click="show('medium', subscription.medium)">

                    <i class="pr-2"
                       v-bind:class="[iconCss(subscription.medium.mime_type)]"></i>
                    {{ subscription.medium.title }}

                    <i class="pull-right fa fa-graduation-cap text-muted"
                       v-if="subscription.visibility && currentUser.id === subscription.owner_id"
                       v-permission="'artefact_create'"
                       @click.stop="setArtefact(subscription.medium.id)"></i>
                    <i v-else-if="currentUser.id === subscription.user_id"
                       v-permission="'artefact_delete'"
                       class="pull-right fa fa-trash text-danger"
                       @click.stop="destroyArtefact(subscription.medium.id)"></i>

                    <license class="pull-right pr-2"
                             :licenseId="subscription.medium.license_id">
                    </license>
                </td>
            </tr>
            <tr>
                <td
                    class="py-2 link-muted text-sm pointer"
                    v-permission="'medium_create'"
                    v-if="url == '/mediumSubscriptions'"
                    @click="show('medium-create', subscription)">
                    <i class="fa fa-plus px-2 "></i> {{ trans('global.media.add')}}
                </td>
            </tr>
        </table>
    </div>


    <div v-else
        v-for="subscription in subscriptions"
        v-bind:id="'medium_'+subscription.medium.id"
        class="box box-objective pointer my-1"
        style="height: 300px !important; min-width: 200px !important; padding: 0; background-size: 100%,50%;"
        :style="{'background-image':'url('+href(subscription.medium.id)+')'}"
        @click="show('medium', subscription.medium)"
    >
    <div class="symbol"
         style="position: absolute;
                padding: 6px;
                z-index: 1;
                width: 30px;
                height: 40px;
                background-color: #0583C9;
                top: 0px;
                font-size: 1.2em;
                left: 10px;">

        <i v-if="subscription.medium.mime_type === 'pdf'" class="fa fa-file-pdf text-white pt-2"></i>
        <i v-if="subscription.medium.mime_type === 'url'" class="fa fa-link text-white pt-2"></i>
        <i v-else class="fa fa-photo-video text-white pt-2"></i>
    </div>

    <i v-if="subscription.medium.mime_type === 'pdf'" class="far fa-file-pdf text-primary text-center pt-2"
       style="position:absolute; top: 0px; height: 150px !important; width: 100%; font-size:800%;"></i>
    <i v-if="subscription.medium.mime_type === 'url'" class="fa fa-link text-primary text-center pt-2"
       style="position:absolute; top: 0px; height: 150px !important; width: 100%; font-size:800%;"></i>
    <span
        v-permission="'medium_delete'"
        class="p-1 pointer_hand"
        accesskey=""
        style="position:absolute; top:0px; height: 30px; width:100%;" >
                <button
                    :id="'delete-medium'+subscription.medium.id"
                    type="submit"
                    class="btn btn-danger btn-sm pull-right"
                    v-on:click.stop="unlinkMedium(subscription);">
                    <small>
                        <i class="fa fa-unlink"
                        ></i>
                    </small>
                </button>
        </span>
    <span class="bg-white text-center p-1 overflow-auto "
          style="position:absolute; bottom:0px; height: 150px; width:100%;">
            <h6 class="events-heading pt-1 hyphens" v-html="subscription.medium.title"></h6>
            <p class=" text-muted small" v-html="subscription.medium.description"></p>
    </span>

    </div>
</div>

</template>


<script>

    import License from '../uiElements/License'
    export default {
        props: {
            subscription: {},
            subscribable_type: '',
            subscribable_id: '',
            public: 0,
            medium: {},
            format: '',
            url: {
                type: String,
                default: '/mediumSubscriptions'
            }
        },
        data() {
            return {
                subscriptions: {},
                errors: {},
                currentUser: {}
            }
        },
        methods: {
            loader() { //todo: remove duplicate in beforMount.
                axios.get(this.url + '?subscribable_type=' + this.subscribable_type + '&subscribable_id=' + this.subscribable_id).then(response => {
                    this.subscriptions = response.data.message;
                }).catch(e => {
                    this.errors = error.response.data.errors;
                });
            },
            show(model, entry) {
                this.$modal.show(model.toLowerCase() + '-modal', {
                    'content': entry,
                    'subscribable_type': this.subscribable_type,
                    'subscribable_id': this.subscribable_id,
                    'public': this.public
                });
            },
            async unlinkMedium(subscription) { //id of external reference and value in db
                try {
                    await axios.post(this.url + '/destroy', subscription).data;
                } catch (error) {
                    //this.errors = error.response.data.errors;
                }
                $("#medium_" + this.medium.id).hide();
            },
            href: function (id) {
                return '/media/' + id;
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
            setArtefact(medium_id) {
                axios.post('/artefacts', {
                    subscribable_type: this.subscribable_type,
                    subscribable_id: this.subscribable_id,
                    medium_id: medium_id
                }).then((response) => {
                    this.loader();
                    alert('Artefakt hinzugefügt!');

                });

            },
            destroyArtefact(medium_id) {
                axios.post('/artefacts/destroy', {
                    subscribable_type: this.subscribable_type,
                    subscribable_id: this.subscribable_id,
                    medium_id: medium_id
                })
                .then((response) => {
                    this.loader();
                });


            }
        },
        beforeMount() {
            axios.get('/users/current').then(response => {
                this.currentUser =  response.data.user
            })
            if (this.subscribable_type  != ''){
                axios.get(this.url + '?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id).then(response => {
                    this.subscriptions = response.data.message;
                }).catch(e => {
                    this.errors = error.response.data.errors;
                });
            }
        },
        components: {
            License
        }

    }
</script>
