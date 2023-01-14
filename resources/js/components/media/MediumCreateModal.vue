<template>
    <modal
        id="medium-create-modal"
        name="medium-create-modal"
        height="auto"
        width="100%"
        :maxWidth=900
        :adaptive=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen"
        @before-close="beforeClose"
        style="z-index: 100000 !important; ">
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
                               href="#upload"
                               data-toggle="tab"
                               @click="setTab('upload')">
                                {{ trans('global.media.upload') }}
                            </a>
                        </li>
                        <li class="nav-link text-sm" v-can="'medium_create'">
                            <a class="link-muted"
                               href="#media"
                               data-toggle="tab"
                               @click="setTab('media')">
                                {{ trans('global.media.title') }}
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
                             id="upload"
                             v-can="'medium_create'">

                            <form action="javascript:void(0)"
                                  @submit.prevent="uploadSubmit"
                                  enctype="multipart/form-data"
                                  method="post"
                                  v-if="isInitial || isSaving">
                                <div class="alert alert-success" v-if="message">
                                    {{ message }}
                                </div>
                                <div class="dropbox text-secondary">
                                    <input type="file"
                                           class="input-file"
                                           multiple
                                           :disabled="isSaving"
                                           :accept="accept"
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
                        </div><!-- /.tab-pane -->

                        <div class="tab-pane" id="media" v-can="'medium_create'">
                            <div class="form-group table-responsive" style="height: 300px;">
                                <table id="media_create_datatable" class="table table-head-fixed">
                                    <thead>
                                    <tr>
                                        <th style="width: 60px" ></th>
                                        <th>{{ trans('global.media.fields.title') }}</th>
                                        <th style="width: 20px"></th>
                                        <!-- <th>{{ trans('global.media.fields.size') }}</th>
                                         <th>{{ trans('global.media.fields.created_at') }}</th>
                                         <th>{{ trans('global.media.fields.public') }}</th>
                                         <th>{{ trans('global.datatables.action') }}</th>-->
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="file in files">
                                        <td style="width: 60px" class="pointer"
                                            @click="edit(file)">
                                            <img v-if="file.mime_type.includes('image')"
                                                 :src="'/media/' + file.id + '/thumb'" height="50" /></td>
                                        <td class="pointer"
                                            @click="edit(file)">{{ file.title }}</td>
                                        <td style="width: 20px"><input
                                            v-model="selectedFiles"
                                            :value="file.id"
                                            type="checkbox"
                                            id="medium"
                                        ></td>
                                        <!--   <th>{{ file.size }}</th>
                                           <th>{{ file.created_at }}</th>
                                           <th>{{ file.public }}</th>
                                           <th>{{ file.action }}</th>-->
                                    </tr>
                                    </tbody>
                                </table>

                            </div>
                            <div class="row">
                                <div class="dataTables_paginate paging_full_numbers col-6" id="media_create_datatable_paginate">
                                    <ul class="pagination">
                                        <li class="first" id="media_create_datatable_first">
                                            <a class="page-link"
                                               @click="getFiles(first_page_url)">
                                                <i class="fa fa-angle-double-left"></i></a>
                                        </li>
                                        <li class=" previous disabled" id="media_create_datatable_previous">
                                            <a class="page-link"
                                               @click="getFiles(prev_page_url)">
                                                <i class="fa fa-angle-left"></i>
                                            </a>
                                        </li>
                                        <li class=" active">
                                            <a class="page-link">{{this.current_page}}</a>
                                        </li>
                                        <li class=" next" id="media_create_datatable_next">
                                            <a class="page-link"
                                               @click="getFiles(next_page_url)">
                                                <i class="fa fa-angle-right"></i>
                                            </a>
                                        </li>
                                        <li class=" last" id="media_create_datatable_last">
                                            <a class="page-link"
                                               @click="getFiles(last_page_url)">
                                                <i class="fa fa-angle-double-right"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="col-6 pt-1" id="media_create_datatable_length">
                                    <label class="small font-weight-light pull-right">
                                        <select
                                            name="media_create_datatable_length"
                                            class="custom-select custom-select-sm form-control form-control-sm"
                                            style="width:60px"
                                            v-model="per_page">
                                            <option value="10" :selected="per_page === 10" >10</option>
                                            <option value="25" :selected="per_page === 25">25</option>
                                            <option value="50" :selected="per_page === 50">50</option>
                                            <option value="100" :selected="per_page === 100">100</option>
                                        </select> {{ trans('global.perPage') }}</label>
                                </div>
                            </div>
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
                            <repository-plugin-create
                                :model="form"></repository-plugin-create>
                        </div><!-- /.tab-pane -->

                    </div>
                    <!-- /.description-block -->
                </div>
            </div>
            <div v-if="tab !== 'external'"
                 class="card-footer">
                <span class="pull-right">
                     <button type="button"
                             class="btn pl-1"
                             data-widget="remove"
                             @click="close()">
                         {{ trans('global.close') }}
                     </button>
                    <button
                        name="medium-create-modal-submit"
                        type="button"
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
            target: 'medium_id',
            callbackFunction: null,
            callbackParentComponent: null,
            callbackComponent: null,
            eventHubCallbackFunctioneventHubCallbackFunction: null,
            eventHubCallbackFunctionParams: null,

            form: new Form({
                'path': '',
                'thumb_path': '',
                'external_id': '',
                'subscribable_type': null,
                'subscribable_id': null,
                'repository': 'local',
                'public': 0
            }),
            endpoints: {},
            endpoint: '',
            link: 'https://',
            uploadError: null,

            currentStatus: null,
            progressBar: false,
            message: '',
            file: '',
            files: [],
            selectedFiles: [],
            accept: '',
            datatable: null,
            //pagination
            current_page: 1,
            first_page_url: "/media?page=1",
            from: 1,
            last_page: 166,
            last_page_url: null,
            next_page_url: null,
            path: "/media",
            per_page: 10,
            prev_page_url: null,
            to: null,
            total: null
        }
    },

    methods: {
        uploadSubmit(formData) {
            this.currentStatus = STATUS_SAVING;
            this.message = '';
            formData.append('path', this.form.path);
            formData.append('subscribable_type', this.form.subscribable_type);
            formData.append('subscribable_id', this.form.subscribable_id);
            formData.append('repository', this.form.repository);
            formData.append('public', this.form.public);
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
                        //this.getFiles();
                    })
                });
        },
        reset() {
            this.currentStatus = STATUS_INITIAL;
            this.uploadError = null;
            this.progressBar = false;
        },
        beforeOpen(event) {
            this.selectedFiles = [];
            this.message = '';

            if (event.params.referenceable_type){
                this.form.subscribable_type = event.params.referenceable_type;
            }
            if (event.params.referenceable_id){
                this.form.subscribable_id = event.params.referenceable_id;
            }
            if (event.params.subscribable_type){
                this.form.subscribable_type = event.params.subscribable_type;
            }
            if (event.params.subscribable_id) {
                this.form.subscribable_id = event.params.subscribable_id;
            }
            if (event.params.target) {
                this.form.target = event.params.target;
            }
            if (event.params.accept) {
                this.accept = event.params.accept;
            }
            if (event.params.public) {
                this.form.public = event.params.public;
            }
            if (event.params.callbackComponent) {
                this.callbackComponent = event.params.callbackComponent;
            }
            if (event.params.callbackParentComponent) {
                this.callbackParentComponent = event.params.callbackParentComponent;
            }
            if (event.params.callbackFunction) {
                this.callbackFunction = event.params.callbackFunction;
            }
            if (event.params.eventHubCallbackFunction) {
                this.eventHubCallbackFunction = event.params.eventHubCallbackFunction;
            }
            if (event.params.eventHubCallbackFunctionParams) {
                this.eventHubCallbackFunctionParams = event.params.eventHubCallbackFunctionParams;
            }
        },
        setTab(tab){
            this.tab = tab;
        },
        edit(file){
            this.$modal.show('medium-modal', { 'content': {'id': file.id , 'title': file.title, 'description': file.description}});
        },
        beforeClose() {
        },
        saveToForm() {
            if (this.eventHubCallbackFunction) {
                this.$eventHub.$emit(this.eventHubCallbackFunction, {'id': this.eventHubCallbackFunctionParams, 'selectedMediumId': this.selectedFiles});
            } else if (this.callbackComponent) {
                if (this.callbackParentComponent) {
                    app.__vue__.$refs[this.callbackParentComponent].$refs[this.callbackComponent][0].reload();
                } else {
                    app.__vue__.$refs[this.callbackComponent][0][this.callbackFunction]();
                }
            } else {
                $('#' + this.target).val(this.selectedFiles);
                $('#' + this.target).trigger("change");
            }


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
        },
        getFiles(path = this.path) {

            axios.get(path, { params: { per_page: this.per_page } })
                .then((response)=>{
                    this.files          = response.data.data;
                    this.current_page   = response.data.current_page;
                    this.first_page_url = response.data.first_page_url;
                    this.from           = response.data.from;
                    this.last_page      = response.data.last_page;
                    this.last_page_url  = response.data.last_page_url;
                    this.next_page_url  = response.data.next_page_url;
                    this.path           = response.data.path;
                    this.per_page       = response.data.per_page;
                    this.prev_page_url  = response.data.prev_page_url;
                    this.to             = response.data.to;
                    this.total          = response.data.total;
                })
                .catch((e) => {
                    console.log(e);
                });
        },
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

        this.getFiles();

        this.$eventHub.$on('external_add', (form) => {
            console.log(form);
            this.form = form;
            axios.post('/media?repository=' + this.form.repository, this.form)
                 .then((response) => {
                    this.saveToForm()
                 });
        });
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

