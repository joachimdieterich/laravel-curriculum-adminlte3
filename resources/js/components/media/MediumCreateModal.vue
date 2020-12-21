<template>
    <modal
        id="medium-create-modal"
        name="medium-create-modal"
        height="auto"
        :adaptive=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen"
        @before-close="beforeClose"
        style="z-index: 1200">
        <div class="card"
             style="margin-bottom: 0px !important; min-height: 400px">
            <div class="card-header">
                 <h3 class="card-title">
                     <i class="fa fa-photo-video"></i>
                    <span v-if="method === 'post'">
                        {{ trans('global.media.add') }}
                    </span>

                    <span v-if="method === 'patch'">
                        {{ trans('global.media.edit') }}
                    </span>
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
            <!-- left-menu -->
            <div class="d-md-flex">
                <div class="card-pane-left p-0">
                    <ul class="nav flex-column">
                        <li class="nav-link text-sm" v-can="'medium_create'">
                            <a class="active show link-muted"
                               href="#media"
                               data-toggle="tab"
                               @click="setTab('media')">
                                {{ trans('global.media.title_singular') }}
                            </a>
                        </li>
                        <li class="nav-link text-sm" v-can="'link_create'">
                            <a class="link-muted"
                               href="#link"
                               data-toggle="tab"
                               @click="setTab('link')">
                                {{ trans('global.media.link') }}
                            </a>
                        </li>
                        <li class="nav-link text-sm" v-can="'external_medium_create'">
                            <a class="link-muted"
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
                     style="min-height: 350px">
                    <div class="tab-content p-2">
                        <div class="tab-pane active show"
                             id="media"
                             v-can="'medium_create'">

                            <form action="javascript:void(0)"
                                  @submit.prevent="uploadSubmit"
                                  enctype="multipart/form-data"
                                  method="post"
                                  v-if="isInitial || isSaving">
                                <div class="alert alert-success" v-if="message">
                                    {{ message[0] }}
                                </div>
                                <div class="dropbox text-secondary">
                                    <input type="file"
                                           class="input-file"
                                           multiple
                                           :disabled="isSaving"
                                           ref="file"
                                           name="file"
                                           @change="filesChange($event.target.name, $event.target.files); fileCount = $event.target.files.length"
                                           required>
                                    <p v-if="isInitial">
                                        <i class="fa fa-upload"></i><br>
                                        Drag your file(s) here to begin<br> or click to browse
                                    </p>
                                    <p v-if="isSaving">
                                        Uploading {{ fileCount }} files...
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


                            <table id="media_create_datatable" class="table table-hover datatable">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>{{ trans('global.media.fields.title') }}</th>
                                   <!-- <th>{{ trans('global.media.fields.size') }}</th>
                                    <th>{{ trans('global.media.fields.created_at') }}</th>
                                    <th>{{ trans('global.media.fields.public') }}</th>
                                    <th>{{ trans('global.datatables.action') }}</th>-->
                                </tr>
                                </thead>
                                <tr v-for="file in files">
                                    <th><input
                                        v-model="selectedFiles"
                                        :value="file.id"
                                        type="checkbox"
                                        id="medium"></th>
                                    <th><a :href="'/media/'+file.id">{{ file.title }}</a></th>
                                 <!--   <th>{{ file.size }}</th>
                                    <th>{{ file.created_at }}</th>
                                    <th>{{ file.public }}</th>
                                    <th>{{ file.action }}</th>-->
                                </tr>
                            </table>

                        </div><!-- /.tab-pane -->

                        <div class="tab-pane" id="link" v-can="'link_create'">
                            <div class="form-group " >
                                <input
                                    type="text" id="link"
                                    name="search"
                                    class="form-control"
                                    v-model="link"
                                    required
                                    @keyup.enter="submit()" />
                            </div>
                        </div><!-- /.tab-pane -->

                        <div class="tab-pane"
                             id="external"
                             v-can="'external_medium_create'">
                            <!--<repository-plugin-create
                                :model="form"></repository-plugin-create>-->
                        </div><!-- /.tab-pane -->

                    </div>
                    <!-- /.description-block -->
                </div>
            </div>
            <div class="card-footer">
                <span class="pull-right">
                     <button type="button"
                             class="btn pl-1"
                             data-widget="remove"
                             @click="close()">
                         {{ trans('global.close') }}
                     </button>
                    <button type="button"
                            class="btn btn-primary pull-right"
                            @click="saveToForm()" >
                        {{ trans('global.save') }}
                    </button>
                </span>
            </div>

        </div>
    </modal>
</template>

<script>
    import Form from 'form-backend-validation'
    import RepositoryPluginCreate from '../../../../app/Plugins/Repositories/resources/js/components/Create';
    require('datatables.net/js/jquery.dataTables.min.js')

    const STATUS_INITIAL = 0, STATUS_SAVING = 1, STATUS_SUCCESS = 2, STATUS_FAILED = 3;
    export default {
        data() {
            return {
                method: 'post',
                requestUrl: '/mediaSubscriptions',
                tab: 'media',
                target:'medium_id',
                form: new Form({
                    'path': '',
                    'subscribable_type': null,
                    'subscribable_id': null,
                    'repository':null
                }),
                endpoints: {},
                endpoint:'',
                link:'https://',
                uploadError: null,

                currentStatus: null,
                progressBar: false,
                message: '',
                file: '',
                files: [],
                selectedFiles: []
            }
        },
        created() {
            this.getAllFile();
        },
        methods: {

            getAllFile() {
                /*axios.get('/media/list')
                     .then((response)=>{
                    for(let i = 0; i < response.data.length;i++)
                    {
                        this.files.push({
                            title: response.data[i].title,
                            size: response.data[i].size,
                            created_at: response.data[i].created_at,
                            public: response.data[i].public,
                            action: response.data[i].action
                        })
                    }

                });*/
               /* .then(()=>{
                    $("#media_create_datatable").DataTable({
                        "paging": true,
                        "ordering": false,
                        "info": true,
                        "autoWidth": false
                    });
                });*/
            },

            uploadSubmit(formData) {
                this.currentStatus = STATUS_SAVING;
                this.message = '';
                formData.append('path', this.form.path);
                formData.append('subscribable_type', this.form.subscribable_type);
                formData.append('subscribable_id', this.form.subscribable_id);
                formData.append('repository', this.form.repository);
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
                            this.files = response.data;
                            this.selectedFiles = this.files[0].id; //todo: select all files
                            this.message = 'OK';
                            this.reset();
                            this.getAllFile();
                        })
                    });
            },
            reset() {
                this.currentStatus = STATUS_INITIAL;
                this.uploadError = null;
                this.progressBar = false;
                //this.$refs.file.value = '';
            },
            beforeOpen(event) {
                if (event.params.referenceable_type){
                    this.form.subscribable_type = event.params.referenceable_type;
                }
                if (event.params.referenceable_id){
                    this.form.subscribable_id = event.params.referenceable_id;
                }
                if (event.params.subscribable_type){
                    this.form.subscribable_type = event.params.subscribable_type;
                }
                if (event.params.subscribable_id){
                    this.form.subscribable_id = event.params.subscribable_id;
                }
                if (event.params.target){
                    this.form.target = event.params.target;
                }
            },
            setTab(tab){
                this.tab = tab;
            },

            beforeClose() {
            },
            saveToForm() {
                $('#'+this.target).val(this.selectedFiles);
                $('#'+this.target).trigger("change");
                this.$modal.hide('medium-create-modal');
            },
            close(){
                this.$modal.hide('medium-create-modal');
            },
            filesChange(fieldName, fileList) {
                const formData = new FormData();

                if (!fileList.length) return;

                Array.from(Array(fileList.length).keys())
                     .map(x => {
                         formData.append(fieldName+'[]', fileList[x], fileList[x].name);// append the files to FormData
                     });

                this.uploadSubmit(formData);
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
        mounted() {
            this.reset();
        },
        components: {
            RepositoryPluginCreate
        }
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
