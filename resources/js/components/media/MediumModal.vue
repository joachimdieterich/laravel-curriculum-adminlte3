<template>
    <Transition name="modal">
        <div v-if="this.mediumStore.mediumModalParams.show"
             class="modal-mask"
        >
        <div class="modal-container">
            <div class="card-header">
                <h3 class="card-title">
                    <span v-if="method === 'post'">
                        {{ trans('global.medium.add') }}
                    </span>
                    <span v-if="method === 'patch'">
                        {{ trans('global.medium.edit') }}
                    </span>
                </h3>
                <div class="card-tools">
                    <button type="button"
                            class="btn btn-tool"
                            @click="$emit('close')">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="card-body p-0" style="max-height: 80vh; overflow-y: auto;">
                <div class="d-md-flex">
                    <div class="card-pane-left p-0">
                        <ul class="nav flex-column">
                            <li class="nav-link text-sm"
                                v-permission="'medium_access'">
                                <a class="link-muted"
                                   href="#upload"
                                   data-toggle="tab"
                                   @click="setTab('upload')">
                                    {{ trans('global.medium.upload') }}
                                </a>
                            </li>
                            <li class="nav-link text-sm"
                                v-can="'medium_access'">
                                <a class="link-muted"
                                   href="#media"
                                   data-toggle="tab"
                                   @click="setTab('media');">
                                    {{ trans('global.medium.title') }}
                                </a>
                            </li>
<!--                            <li class="nav-link text-sm"
                                v-can="'link_create'">
                                <a class="link-muted"
                                   href="#link"
                                   data-toggle="tab"
                                   @click="setTab('link')">
                                    {{ trans('global.medium.link') }}
                                </a>
                            </li>-->
                            <li class="nav-link text-sm"
                                v-can="'external_medium_create'">
                                <a class="link-muted active show"
                                   href="#external"
                                   data-toggle="tab"
                                   @click="setTab('external')">
                                    {{ trans('global.externalRepositorySubscription.title_singular') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- /.left-menu -->

                    <div class="p-1 flex-fill border-left"
                         style="min-height: 550px">
                        <div class="tab-content p-2">
                            <div class="tab-pane"
                                 id="upload"
                                 v-permission="'medium_create'">
                                <form action="javascript:void(0)"
                                    @submit.prevent="uploadSubmit"
                                      enctype="multipart/form-data"
                                      method="post"
                                      v-if="isInitial || isSaving">
                                    <div class="alert alert-success" v-if="this.message != ''">
                                        {{ message }}
                                    </div>
                                    <div class="dropbox text-secondary">
                                        <input type="file"
                                               class="input-file"
                                               multiple
                                               :disabled="isSaving"
                                               :accept="mediumStore.mediumModalParams.accept"
                                               ref="file"
                                               name="file"
                                               @change="filesChange($event.target.name, $event.target.files); fileCount = $event.target.files.length"
                                               required>
                                        <p v-if="isInitial">
                                            <i class="fa fa-upload"></i><br>
                                            <span v-dompurify-html="trans('global.medium.upload_helper')"></span>
                                        </p>
                                        <p v-if="isSaving">
                                             {{ fileCount }} {{ trans('global.medium.upload') }}...
                                        </p>
                                    </div>
                                </form>

                                <div class="progress"
                                     v-if="progressBar">
                                    <div
                                        class="progress-bar" role="progressbar"
                                        :style="{width: progressBar + '%'}"
                                        :aria-valuenow="progressBar"
                                        aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                            </div><!-- /.tab-pane -->

                            <div class="tab-pane"
                                 id="media"
                                 v-can="'medium_create'">
                                <div id="media_create_datatable_filter" class="dataTables_filter">
                                    <input
                                        id="media_search_datatable"
                                        type="search"
                                        class="form-control form-control-sm"
                                        v-model="search"
                                        placeholder="Suchbegriff"
                                        aria-controls="media_create_datatable"
                                    />
                                </div>
                                <div class="form-group table-responsive" style="min-height: 400px;">
                                    <div id="media-datatable-wrapper"
                                         class="w-100 dataTablesWrapper">
                                        <DataTable
                                            id="media-datatable"
                                            :columns="columns"
                                            :options="options"
                                            :ajax="url+'/list'"
                                            :search="search"
                                            width="100%"
                                        ></DataTable>
                                    </div>
                                </div>
                            </div><!-- /.tab-pane -->

<!--                            <div class="tab-pane"
                                 id="link"
                                 v-can="'link_create'">
                                <div class="form-group " >
                                    <input
                                        type="text" id="link"
                                        name="search"
                                        class="form-control"
                                        v-model="form.link"
                                        required
                                    />
                                </div>
                            </div>--><!-- /.tab-pane -->

                            <div class="tab-pane active show"
                                 id="external"
                                 v-can="'external_medium_create'">
                                <RepositoryPluginCreate
                                    :model="this.mediumStore.mediumModalParams">
                                </RepositoryPluginCreate>
                            </div><!-- /.tab-pane -->

                        </div>
                        <!-- /.description-block -->
                    </div>
                </div>
            </div>

            <div v-if="tab !== 'external'"
                 class="card-footer">
                 <span class="pull-right">
                     <button
                         id="medium-cancel"
                         type="button"
                         class="btn btn-default mr-2"
                         @click="$emit('close')">
                         {{ trans('global.cancel') }}
                     </button>
                     <button
                         id="medium-save"
                         class="btn btn-primary"
                         @click="add()" >
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
    import {useMediumStore} from "../../store/media";
    import DataTable from 'datatables.net-vue3';
    import DataTablesCore from 'datatables.net-bs5';
    import 'datatables.net-select-bs5'

    DataTable.use(DataTablesCore);
    import RepositoryPluginCreate from '../../../../app/Plugins/Repositories/edusharing/resources/js/components/Create.vue';

    const STATUS_INITIAL = 0, STATUS_SAVING = 1, STATUS_SUCCESS = 2, STATUS_FAILED = 3;

    export default {
        components: {
            RepositoryPluginCreate,
            DataTable,
        },
        props: {
            show: {
                type: Boolean
            },
            params: {
                type: Object
            },  //{ 'modelId': curriculum.id, 'modelUrl': 'curriculum' , 'shareWithToken': true, 'canEditCheckbox': false}

        },
        setup () { //use database store
            const mediumStore = useMediumStore();
            return {
                mediumStore,
            }
        },
        data() {
            return {
                component_id: this.$.uid,
                method: 'post',
                url: '/media',
                tab: 'external',
                progressBar: false,
                form: new Form({
                    'path': '',
                    'thumb_path': '',
                    'medium_name': '',
                    'title': '',
                    'author': '',
                    'size': '',
                    'mimetype': '',
                    'license_id': '',
                    'external_id': '',
                    'subscribable_type': null,
                    'subscribable_id': null,
                    'repository': 'local',
                    'public': 0
                }),
                search: '',
                message: '',
                columns: [
                    {
                        title: 'img',
                        data: 'id',
                        render: function( data, type, full, meta ) {
                            return '<img src="/media/'+ data +'" width="60"/>';
                        }
                     },
                    { title: 'title', data: 'title', searchable: true},
                    { title: 'size', data: 'size'},
                    { title: 'created_at', data: 'created_at'},
                ],
                options : this.$dtOptions,
            }
        },
        watch: {
            params: function(newVal, oldVal) {
                this.form.reset();
                this.form.populate(newVal);
                if (this.form.id != ''){
                    this.method = 'patch';
                } else {
                    this.method = 'post';
                }
            },
        },
        computed: {
            isInitial() {
                return this.mediumStore.mediumModalParams.currentStatus === STATUS_INITIAL;
            },
            isSaving() {
                return this.mediumStore.mediumModalParams.currentStatus === STATUS_SAVING;
            },
            isSuccess() {
                return this.mediumStore.mediumModalParams.currentStatus === STATUS_SUCCESS;
            },
            isFailed() {
                return this.mediumStore.mediumModalParams.currentStatus === STATUS_FAILED;
            }
        },
        methods: {
            filesChange(fieldName, fileList) {
                const formData = new FormData();

                if (!fileList.length) return;

                Array.from(Array(fileList.length).keys())
                    .map(x => {
                        formData.append(fieldName+'[]', fileList[x], fileList[x].name);// append the files to FormData
                    });
                this.uploadSubmit(formData);
            },
            uploadSubmit(formData) {
                this.mediumStore.mediumModalParams.currentStatus = STATUS_SAVING;
                this.message = '';
                formData.append('path', this.form.path);
                formData.append('subscribable_type', this.mediumStore.mediumModalParams.subscribable_type);
                formData.append('subscribable_id', this.mediumStore.mediumModalParams.subscribable_id);
                formData.append('repository', this.mediumStore.mediumModalParams.repository);
                formData.append('public', this.mediumStore.mediumModalParams.public);
                axios.post('/media', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                    onUploadProgress: function( progressEvent ) {
                        this.progressBar = parseInt(Math.round((progressEvent.loaded * 100) / progressEvent.total))
                    }.bind(this)
                })
                    .then((response) => {
                        setTimeout(() => {
                            this.mediumStore.media = response.data;
                            this.selectedFiles = this.mediumStore.media[0].id; //todo: select all files
                            this.message = 'OK';
                            this.form.reset();
                            this.progressBar = false;
                        })
                    });
            },
            subscribe(){
                this.mediumStore.selectedMedia.forEach((medium) => {
                    axios.post('/mediumSubscriptions', {
                        subscribable_type: this.mediumStore.mediumModalParams.subscribable_type,
                        subscribable_id: this.mediumStore.mediumModalParams.subscribable_id,
                        medium_id: medium.id
                    }).then((response) => {
                        console.log(medium.id + 'subscribed');
                    });
                });
            },

            add() {
                if (this.mediumStore.mediumModalParams.subscribeSelected){ //subscribe selected
                    this.subscribe();
                }
                this.$eventHub.emit(
                    this.mediumStore.mediumModalParams.callback, //default callback == 'medium-added'
                    {
                        'id': this.mediumStore.mediumModalParams.callbackId,
                        'selectedMedia':  this.mediumStore.selectedMedia,
                        //'selectedMediumId':  this.mediumStore.selectedMedia,
                        //'files': this.mediumStore.selectedMedia,
                    }
                );
                this.reset();
                this.$emit('close');

            },
            reset(){
                this.form.reset();
                this.mediumStore.setMediumModalParams();
                this.progressBar = false;
                this.message = '';
            },
            setTab(tab) {
                this.tab = tab;

                if (tab === 'media') {
                    const dt = $('#media-datatable').DataTable();

                    $('#media_search_datatable').on('keyup', function () {
                        dt.search(this.search).draw();
                    }.bind(this));

                    dt.on('select', function(e, dt, type, indexes) {
                        let selection = dt.rows('.selected').data().toArray()
                        this.mediumStore.setSelectedMedia(selection);

                    }.bind(this));
                }
            },
            externalAdd(form){
                //console.log(form);
                //this.form = form;

                axios.post('/media?repository=edusharing', form)
                    .then((response) => {
                        //console.log(response);
                        this.mediumStore.setSelectedMedia([response.data]);
                        this.add();
                    })
                    .catch((err) => {
                        console.log(err);
                    });
            }
        },
        mounted() {
            this.mediumStore.setMediumModalParams();
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
