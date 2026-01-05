<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            style="z-index: 10000000 !important;"
            @mouseup.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="modal-header">
                    <span class="card-title">
                        {{ method == 'post' ? trans('global.medium.add') : trans('global.medium.edit') }}
                    </span>
                    <button
                        type="button"
                        class="btn btn-icon text-secondary"
                        :title="trans('global.close')"
                        @click="globalStore?.closeModal($options.name)"
                    >
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="d-md-flex">
                        <!-- left side of menu only visible to admins -->
                        <div
                            v-permission="'is_admin'"
                            class="card-pane-left p-0"
                        >
                            <ul class="nav flex-column">
                                <li
                                    v-permission="'is_admin'"
                                    class="nav-link text-sm"
                                >
                                    <a
                                        href="#upload"
                                        class="link-muted"
                                        data-toggle="tab"
                                        @click="setTab('upload');"
                                    >
                                        {{ trans('global.medium.upload') }}
                                    </a>
                                </li>
                                <li
                                    v-permission="'is_admin'"
                                    class="nav-link text-sm"
                                >
                                    <a
                                        href="#local-media"
                                        class="link-muted"
                                        data-toggle="tab"
                                        @click="setTab('media');"
                                    >
                                        {{ trans('global.medium.title') }}
                                    </a>
                                </li>
    <!--                            <li class="nav-link text-sm"
                                    v-can="'link_create'">
                                    <a class="link-muted"
                                    data-toggle="tab"
                                    @click="setTab('link')">
                                        {{ trans('global.medium.link') }}
                                    </a>
                                </li>-->
                                <li
                                    v-permission="'external_medium_create'"
                                    class="nav-link text-sm"
                                >
                                    <a
                                        href="#external"
                                        class="link-muted active show"
                                        data-toggle="tab"
                                        @click="setTab('external')"
                                    >
                                        {{ trans('global.externalRepositorySubscription.title_singular') }}
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="p-1 flex-fill border-left">
                            <div class="tab-content">
                                <div
                                    v-permission="'is_admin'"
                                    id="upload"
                                    class="tab-pane"
                                >
                                    <form v-if="isInitial || isSaving"
                                        action="javascript:void(0)"
                                        enctype="multipart/form-data"
                                        method="post"
                                        @submit.prevent="uploadSubmit"
                                    >
                                        <div v-if="message != ''"
                                            class="alert alert-success"
                                        >
                                            {{ message }}
                                        </div>
                                        <div class="dropbox text-secondary p-1 m-1">
                                            <input
                                                id="file"
                                                name="file"
                                                type="file"
                                                class="input-file"
                                                multiple
                                                :disabled="isSaving"
                                                :accept="accept"
                                                ref="file"
                                                @change="filesChange($event.target.name, $event.target.files); fileCount = $event.target.files.length"
                                                required
                                            />
                                            <p v-if="isInitial">
                                                <i class="fa fa-upload"></i><br>
                                                <span v-html="trans('global.medium.upload_helper')"></span>
                                            </p>
                                            <p v-if="isSaving">
                                                {{ fileCount }} {{ trans('global.medium.upload') }}...
                                            </p>
                                        </div>
                                    </form>

                                    <div v-if="progressBar"
                                        class="progress"
                                    >
                                        <div
                                            class="progress-bar" role="progressbar"
                                            :style="{width: progressBar + '%'}"
                                            :aria-valuenow="progressBar"
                                            aria-valuemin="0"
                                            aria-valuemax="100"
                                        ></div>
                                    </div>
                                </div>

                                <div v-if="checkPermission('is_admin')"
                                    id="local-media"
                                    class="tab-pane m-2"
                                >
                                    <div
                                        id="media_create_datatable_filter"
                                        class="dataTables_filter"
                                    >
                                        <input
                                            id="media_search_datatable"
                                            name="media_search_datatable"
                                            type="search"
                                            class="form-control form-control-sm"
                                            v-model="search"
                                            placeholder="Suchbegriff"
                                            aria-controls="media_create_datatable"
                                        />
                                    </div>
                                    <div style="width: 600px;">
                                        <div
                                            id="media-datatable-wrapper"
                                            class="w-100 dataTablesWrapper"
                                        >
                                            <DataTable
                                                id="media-datatable"
                                                :columns="columns"
                                                :options="options"
                                                ajax="/media/list"
                                                :search="search"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div
                                    v-permission="'external_medium_create'"
                                    id="external"
                                    class="tab-pane active show"
                                >
                                    <RepositoryPluginCreate v-if="!postProcess"
                                        :model="this.form"
                                    />
                                    <div v-if="postProcess"
                                        :id="'loading_'+this.component_id"
                                        class="overlay text-center w-100 h-100"
                                    >
                                        <i class="fa fa-spinner fa-pulse fa-fw"></i>
                                        <span>Fertigstellen...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="tab !== 'external'"
                    class="card-footer"
                >
                    <span class="pull-right">
                        <button
                            id="medium-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="medium-save"
                            class="btn btn-primary ml-3"
                            @click="add()"
                        >
                            {{ trans('global.save') }}
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </Transition>
</template>
<script>
import Form from 'form-backend-validation';
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import 'datatables.net-select-bs5'
import RepositoryPluginCreate from '../../../../app/Plugins/Repositories/edusharing/resources/js/components/Create.vue';
import {useGlobalStore} from "../../store/global.js";

DataTable.use(DataTablesCore);

const STATUS_INITIAL = 0, STATUS_SAVING = 1, STATUS_SUCCESS = 2, STATUS_FAILED = 3;

export default {
    name: 'medium-modal',
    components: {
        RepositoryPluginCreate,
        DataTable,
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            tab: 'external',
            progressBar: false,
            subscribable_type: null,
            subscribable_id: null,
            form: new Form({
                path: '',
                thumb_path: '',
                medium_name: '',
                title: '',
                author: '',
                size: '',
                mimetype: '',
                license_id: '',
                external_id: '',
                subscribable_type: null,
                subscribable_id: null,
                repository: 'local',
                public: 0,
            }),
            currentStatus: STATUS_INITIAL,
            subscribeSelected: false,
            accept: '',
            callback: 'medium-added',
            callbackId: null,
            search: '',
            message: '',
            columns: [
                {
                    title: 'img',
                    data: 'id',
                    render: function(data, type, full, meta) {
                        return '<img src="/media/' + data + '" width="60"/>';
                    },
                },
                { title: 'title', data: 'title', searchable: true },
                { title: 'size', data: 'size' },
                { title: 'created_at', data: 'created_at' },
            ],
            options : this.$dtOptions,
            postProcess: false,
        }
    },
    computed: {
        isInitial() {
            return this.currentStatus === STATUS_INITIAL;
        },
        isSaving() {
            return this.currentStatus === STATUS_SAVING;
        },
        isSuccess() {
            return this.currentStatus === STATUS_SUCCESS;
        },
        isFailed() {
            return this.currentStatus === STATUS_FAILED;
        }
    },
    methods: {
        filesChange(fieldName, fileList) {
            const formData = new FormData();

            if (!fileList.length) return;
            Array.from(fileList).map(file => {
                formData.append(fieldName, file, file.name); // append the files to FormData
            });
            this.uploadSubmit(formData);
        },
        uploadSubmit(formData) {
            this.currentStatus = STATUS_SAVING;
            this.message = '';
            formData.append('path', this.form.path);
            formData.append('subscribable_type', this.form.subscribable_type);
            formData.append('subscribable_id', this.form.subscribable_id);
            formData.append('repository', this.form.repository);
            formData.append('public', this.form.public ?? 1);
            axios.post('/media', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                onUploadProgress: function( progressEvent ) {
                    this.progressBar = parseInt(Math.round((progressEvent.loaded * 100) / progressEvent.total))
                }.bind(this)
            })
                .then((response) => {
                    this.globalStore.setSelectedMedia(response.data);
                    this.message = 'OK';
                    this.form.reset();
                    this.progressBar = false;
                });
        },
        subscribe() {
            this.globalStore.selectedMedia.forEach((medium) => {
                axios.post('/mediumSubscriptions', {
                    subscribable_type: this.subscribable_type,
                    subscribable_id: this.subscribable_id,
                    medium_id: medium.id
                }).then((response) => {
                    Object.assign(medium, response.data);
                });
            });
        },
        add() {
            // creating additional subscription is only neccessary for local media
            if (this.subscribeSelected && !this.postProcess) this.subscribe();

            this.$eventHub.emit(
                this.callback, //default callback == 'medium-added'
                {
                    id: this.callbackId,
                    selectedMedia:  this.globalStore.selectedMedia,
                    //'selectedMediumId':  this.globalStore.selectedMedia,
                    //'files': this.globalStore.selectedMedia,
                }
            );
            this.reset();
            this.globalStore?.closeModal(this.$options.name);
        },
        reset() {
            this.globalStore.resetSelectedMedia();
            this.form.reset();

            this.currentStatus = STATUS_INITIAL;
            this.accept = '';
            this.callback = 'medium-added'; //previous eventHubCallbackFunction
            this.callbackId =  null; //previous eventHubCallbackParams
            this.public = 0;
            this.repository = 'local';
            this.subscribeSelected = false;
            this.message = '';
            this.progressBar = false;
        },
        setTab(tab) {
            this.tab = tab;

            if (tab === 'media') {
                const datatable = $('#media-datatable').DataTable();

                $('#media_search_datatable').on('keyup', function () {
                    datatable.search(this.search).draw();
                }.bind(this));

                datatable.on('select deselect', (e, dt) => {
                    let selection = datatable.rows({page: 'current'}).data()[dt[0][0]];
                    this.globalStore.addSelectedMedia(selection);
                });
            }
        },
        externalAdd(form) {
            this.postProcess = true;
            axios.post('/media?repository=edusharing', form)
                .then((response) => {
                    this.globalStore.setSelectedMedia([response.data]);
                    this.add();
                })
                .catch((err) => {
                    console.log(err);
                });
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show && !state.modals[this.$options.name].lock) {
                this.globalStore.lockModal(this.$options.name);
                const params = state.modals[this.$options.name].params;
                this.reset();

                this.postProcess = false;
                this.subscribeSelected = params.subscribeSelected;
                this.callback = params.callback ?? this.callback;
                this.callbackId = params.callbackId;
                this.accept = params.accept;
                this.subscribable_type = params.subscribable_type;
                this.subscribable_id = params.subscribable_id;
                this.currentStatus = STATUS_INITIAL;

                this.form.populate(params);

                if (this.form.id !== '') {
                    this.method = 'patch';
                } else {
                    this.method = 'post';
                }
            }
        });

        this.$eventHub.on('external_add', (form) => {
            this.externalAdd(form);
        });
    },
}
</script>
<style lang="scss">
.dropbox {
    outline: 2px dashed grey; /* the dash box */
    padding: 10px 10px;
    background: #fbfbfc;
    min-height: 200px; /* minimum height */
    position: relative;
    cursor: pointer;
}

.input-file {
    opacity: 0; /* invisible but it's there! */
    width: 100%;
    height: 200px;
    position: absolute;
    cursor: pointer;
}

.dropbox:hover {
    background: #f7f6f8; /* when mouse over to the drop zone, change color */
}

.dropbox p {
    font-size: 1em;
    text-align: center;
    padding: 50px 0;
}
</style>
