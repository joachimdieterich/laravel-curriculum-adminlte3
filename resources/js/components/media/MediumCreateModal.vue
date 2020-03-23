<template>
    <modal 
        id="medium-create-modal" 
        name="medium-create-modal" 
        height="auto" 
        :adaptive=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen"
        @opened="opened"
        @before-close="beforeClose"
        style="z-index: 1200">
        <div class="card" 
             style="margin-bottom: 0px !important">
            <div class="card-header">
                 <h3 class="card-title">
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
            
            <div class="card-body" style="max-height: 80vh; overflow-y: auto;">

                <ul class="nav nav-pills">
                  <li class="nav-item" v-can="'medium_create'"><a class="nav-link active show" href="#media" data-toggle="tab" @click="setTab('media')">{{ trans('global.media.title_singular') }}</a></li>
                  <li class="nav-item" v-can="'link_create'"><a class="nav-link" href="#link" data-toggle="tab" @click="setTab('link')">{{ trans('global.media.link') }}</a></li>
                  <li class="nav-item" v-can="'external_medium_create'"><a class="nav-link" href="#external" data-toggle="tab" @click="setTab('external')">{{ trans('global.externalRepositorySubscription.title_singular') }}</a></li>
                </ul>
              
             
                <div class="tab-content pt-2">
                    <div class="tab-pane active show" id="media" v-can="'medium_create'">
                        <div id="lfm" 
                             class="pull-left"
                           data-input="thumbnail" 
                           data-preview="holder" >
                            <button 
                                class="btn btn-primary pull-left" 
                                onclick="$(this).hide();">
                                <i class="fa fa-plus"></i>
                                {{ trans('global.media.add') }}
                            </button>
                            <img id="holder" 
                                style="height:100px;" 
                                src="" >
                        </div>
                          <input id="thumbnail" 
                                  name="filepath"
                                  v-model="form.path"
                                  type="text" 
                                  class="invisible"
                          >
                          <button class="btn btn-primary pull-right" @click="submit()" >{{ trans('global.save') }}</button>
                    </div><!-- /.tab-pane -->
                    <div class="tab-pane" id="link" v-can="'link_create'">
                        <div class="form-group " >
                            <label for="title">{{ trans('global.media.link') }}</label>
                            <input
                                type="text" id="link"
                                name="search"
                                class="form-control"
                                v-model="link"
                                required
                                @keyup.enter="submit()" />
                        </div>
                        <button class="btn btn-primary pull-right" @click="submit()" >
                            {{ trans('global.save') }}
                        </button>
                    </div><!-- /.tab-pane -->

                    <div class="tab-pane" id="external" v-can="'external_medium_create'">
                        <repository-plugin-create :model="form"></repository-plugin-create>
                    </div><!-- /.tab-pane -->
                </div><!-- /.tab-content -->
             
                     
            </div>
            <div class="card-footer">
                <span class="pull-right">
                     <button type="button" class="btn btn-primary" data-widget="remove" @click="close()">{{ trans('global.close') }}</button>
                </span>
            </div>
            
        </div>
    </modal>
</template>

<script>
    import Form from 'form-backend-validation'
    import RepositoryPluginCreate from '../../../../app/Plugins/Repositories/resources/js/components/Create';

    export default {
        data() {
            return {
                method: 'post',
                requestUrl: '/mediaSubscriptions',
                tab: 'media',
                form: new Form({
                    'path': '',
                    'subscribable_type': null,
                    'subscribable_id': null,
                    'repository':null
                }),
                endpoints: {},
                endpoint:'',
                link:'https://',
                
            }
        },
        methods: {
            async submit() {
                try {
                    switch (this.tab) {
                        case "media": 
                            this.form.path = $("#thumbnail").val().slice(20); //remove "/laravel-filemanager" -->now done by getMediumByEventPath
                            this.location = (await axios.post('/mediumSubscriptions', this.form)).data.message;
                            location.reload(true); 
                        break;
                        case "link": 
                            this.form.path = this.link; //remove "/laravel-filemanager" -->now done by getMediumByEventPath
                            this.location = (await axios.post('/mediumSubscriptions', this.form)).data.message;
                            location.reload(true); 
                        break;
                        case "external": 
                            
                        break;
                    }
                   
                } catch(error) {
                   // this.errors = error.response.data.errors;
                }
            },
            beforeOpen(event) {
                if (event.params.referenceable_type){
                    this.form.subscribable_type = event.params.referenceable_type;
                }   
                if (event.params.referenceable_id){
                    this.form.subscribable_id = event.params.referenceable_id;
                }
            },
            setTab(tab){
                this.tab = tab;
            },
                    
            opened(){
                $('#lfm').filemanager('files');
            },
          
            beforeClose() {
                //console.log('close')
            },
           
//            async load(id) {
//                try {
//                    this.form.populate((await axios.get('/contents/'+id)).data.message);
//                } catch(error) {
//                    //console.log('loading failed')
//                }
//            },
            close(){
                this.$modal.hide('medium-create-modal');
            }
        },
        mounted() {
        },   
        components: {
            RepositoryPluginCreate,
        }
    }
</script>