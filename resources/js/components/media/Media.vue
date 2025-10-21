<template>
<div>
    <div v-if="format == 'list'">
        <table
            id="sidebar_media_datatable"
            class="table table-hover datatable media_table"
        >
            <tbody>
                <tr v-for="subscription in subscriptions">
                    <td
                        style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 100px;"
                        class="link-muted text-sm px-2 pointer"
                        @click="show(subscription.medium)"
                    >
                        <i
                            class="pr-2"
                           :class="[iconCss(subscription.medium.mime_type)]"
                        ></i>
                        {{ subscription.medium.title }}
                        <span class="pull-right">
                            <i v-if="$userId == subscription.owner_id"
                                v-permission="'medium_delete'"
                                class="fa fa-trash text-danger"
                                @click.stop="destroy(subscription)"
                            ></i>
                        </span>
    <!--                    <i class="pull-right fa fa-graduation-cap text-muted"
                           v-if="subscription.visibility && ($userId == subscription.owner_id)"
                           v-permission="'artefact_create'"
                           @click.stop="setArtefact(subscription.medium.id)"
                        ></i>
    
                        <i v-else-if="$userId == subscription.owner_id"
                           v-permission="'artefact_delete'"
                           class="pull-right fa fa-trash text-danger"
                           @click.stop="destroyArtefact(subscription.medium.id)"
                        ></i>-->
                        <license
                            class="pull-right pr-4"
                            :licenseId="subscription.medium.license_id"
                        />
                    </td>
                </tr>

                <tr>
                    <td v-if="url == '/mediumSubscriptions' && (editable || checkPermission('is_admin'))"
                        v-permission="'medium_create'"
                        class="py-2 link-muted text-sm pointer"
                        @click="addMedia()"
                    >
                        <i class="fa fa-plus px-2 "></i>
                        {{ trans('global.medium.add')}}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div v-else
        v-for="subscription in subscriptions"
        :id="'medium_' + subscription.medium.id"
        class="box box-objective pointer p-0 my-1"
        style="height: 300px !important; min-width: 200px !important; background-size: 100%, 50%;"
        :style="{ 'background-image': 'url(/media/' + subscription.medium.id + ')' }"
        @click="show(subscription.medium)"
    >
        <div class="symbol"
            style="position: absolute;
                padding: 6px;
                z-index: 1;
                width: 30px;
                height: 40px;
                background-color: #0583C9;
                top: 0;
                font-size: 1.2em;
                left: 10px;
            "
        >
            <i v-if="subscription.medium.mime_type === 'pdf'" class="fa fa-file-pdf text-white pt-2"></i>
            <i v-if="subscription.medium.mime_type === 'url'" class="fa fa-link text-white pt-2"></i>
            <i v-else class="fa fa-photo-video text-white pt-2"></i>
        </div>

        <i v-if="subscription.medium.mime_type === 'pdf'" class="far fa-file-pdf text-primary text-center pt-2"
            style="position:absolute; top: 0; height: 150px !important; width: 100%; font-size:800%;"
        ></i>
        <i v-if="subscription.medium.mime_type === 'url'" class="fa fa-link text-primary text-center pt-2"
            style="position:absolute; top: 0; height: 150px !important; width: 100%; font-size:800%;"
        ></i>
        <span
            v-permission="'medium_delete'"
            class="p-1 pointer_hand"
            accesskey=""
            style="position:absolute; top:0; height: 30px; width:100%;"
        >
            <button
                :id="'delete-medium'+subscription.medium.id"
                type="submit"
                class="btn btn-danger btn-sm pull-right"
                v-on:click.stop="unlinkMedium(subscription);"
            >
                <small>
                    <i class="fa fa-unlink"></i>
                </small>
            </button>
        </span>
        <span class="bg-white text-center p-1 overflow-auto "
            style="position:absolute; bottom:0; height: 150px; width:100%;"
        >
            <h6 class="events-heading pt-1 hyphens" v-html="subscription.medium.title"></h6>
            <p class=" text-muted small" v-html="subscription.medium.description"></p>
        </span>
    </div>
</div>
</template>
<script>
import License from '../uiElements/License.vue';
import {useGlobalStore} from "../../store/global";

export default {
    props: {
        subscription: {
            type: Object,
            default: null,
        },
        subscribable_type: {
            type: String,
            default: null,
        },
        subscribable_id: {
            type: Number,
            default: null,
        },
        public: {
            type: Boolean,
            default: false,
        },
        medium: {
            type: Object,
            default: null,
        },
        format: {
            type: String,
            default: null,
        },
        url: {
            type: String,
            default: '/mediumSubscriptions',
        },
        editable: {
            type: Boolean,
            default: true,
        },
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            subscriptions: {},
            currentUser: {},
            currentMedium: null,
        }
    },
    methods: {
        loader() {
            axios.get(this.url + '?subscribable_type=' + this.subscribable_type + '&subscribable_id=' + this.subscribable_id)
            .then(response => {
                this.subscriptions = response.data.message;
            }).catch(e => {
                console.log(e);
            });
        },
        show(mediumObject) {
            if (mediumObject.mime_type === 'application/pdf') {
                window.open('/media/' + mediumObject.id, '_blank');
            } else {
                this.globalStore?.showModal('medium-preview-modal', { media: [mediumObject] });
            }
        },
        addMedia() {
            this.globalStore?.showModal('medium-modal', {
                'subscribeSelected': true,
                'subscribable_type': this.subscribable_type,
                'subscribable_id': this.subscribable_id,
                'public': this.public ? 1 : 0,
                'callbackId': this.component_id
            });
        },
        async unlinkMedium(subscription) { //id of external reference and value in db
            try {
                await axios.post(this.url + '/destroy', subscription).data;
            } catch (e) {
                console.log(e)
            }
            $("#medium_" + this.medium.id).hide();
        },
        iconCss(mimeType) {
            switch (true) {
                case mimeType.startsWith("image"):
                    return "fa fa-file-image";
                case mimeType.startsWith("video"):
                    return "fa fa-file-video";
                case mimeType.startsWith("application/pdf"):
                    return "fa fa-file-pdf";
                default:
                    return "fa fa-file";
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
        destroy(subscription) {
            axios.post('/mediumSubscriptions/destroy', {
                medium_id: subscription.medium_id,
                subscribable_id: subscription.subscribable_id,
                subscribable_type: subscription.subscribable_type,
                additional_data: true, // hack to skip setting medium_id of model to null
            })
            .then((response) => {
                this.loader();
            })
            .catch((e) => {
                console.log(e)
            });
        },
        /*destroyArtefact(medium_id) {
            axios.post('/artefacts/destroy', {
                subscribable_type: this.subscribable_type,
                subscribable_id: this.subscribable_id,
                medium_id: medium_id
            })
            .then((response) => {
                this.loader();
            });
        },*/
    },
    watch: {
        subscribable_id() {
            this.subscriptions = {};
            this.loader();
        },
    },
    mounted() {
        this.loader();

        this.$eventHub.on('medium-added', (e) => {
            if (this.component_id == e.id) {
                this.loader();
            }
        });
    },
    components: {
        License,
    }
}
</script>