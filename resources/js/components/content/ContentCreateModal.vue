<template>
    <modal
        id="content-create-modal"
        name="content-create-modal"
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
                        {{ trans('global.create')  }}
                    </span>

                    <span v-if="method === 'patch'">
                        {{ trans('global.update')  }}
                    </span>

                    {{ trans('global.content.title_singular') }}
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
                <div class="form-group "
                    :class="form.errors.title ? 'has-error' : ''"
                      >
                    <label for="title">{{ trans('global.content.fields.title') }} *</label>
                    <input
                        type="text" id="title"
                        name="title"
                        class="form-control"
                        v-model="form.title"
                        placeholder="Title"
                        required
                        />
                     <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                </div>
                <div class="form-group ">
                    <label for="content">{{ trans('global.content.fields.content') }}</label>
                    <textarea
                    id="content"
                    name="content"
                    class="form-control description my-editor "
                    v-model="form.content"
                    ></textarea>
                    <p class="help-block" v-if="form.errors.content" v-text="form.errors.content[0]"></p>
                </div>

                <div class="form-group " >
                    <label for="categorie">
                        {{ trans('global.categorie.title_singular') }}
                    </label>
                    <select name="categorie[]"
                            id="categorie"
                            class="form-control select2 "
                            style="width:100%;"
                            multiple=true
                            v-model="form.categorie_ids"
                       >
                         <option v-for="(item,index) in categories" v-bind:value="item.id">{{ item.title }}</option>
                    </select>
                </div>

                <div class="form-group "
                     v-can="'categorie_create'">
                    <label for="add_categorie"
                           class="pull-right" >
                        <a @click="toggle_categorie_input()">
                            <i class="fa fa-plus"></i> {{ trans('global.categorie.title_singular') }}
                        </a>
                    </label>
                    <div class="input-group"
                         >
                        <input id="add_categorie"
                               type="text"
                               class="form-control "
                               :class="this.show_add_categorie"
                               data-original-title=""
                               title="">
                        <div class="input-group-append"
                              :class="this.show_add_categorie">
                          <a class="input-group-text" @click="addCategorie()"><i class="fas fa-save"></i></a>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <span class="pull-right">
                     <button type="button" class="btn btn-primary" data-widget="remove" @click="close()">{{ trans('global.close') }}</button>
                     <button class="btn btn-primary" @click="submit()" >{{ trans('global.save') }}</button>
                </span>
            </div>

        </div>
    </modal>
</template>

<script>
    import Form from 'form-backend-validation'

    export default {
        data() {
            return {
                method: 'post',
                requestUrl: '/contents',
                show_add_categorie: 'invisible',
                categories: {},
                form: new Form({
                    'id':'',
                    'title': '',
                    'content': '',
                    'categorie_ids': [],
                    'referenceable_type': null,
                    'referenceable_id': null,
                }),
            }
        },
        methods: {
            async submit( ) {
                try {
                    if (this.method === 'patch'){
                        this.form.content = tinyMCE.get('content').getContent();
                        this.form.categorie_ids = $("#categorie").val()
                        this.location = (await axios.patch('/contents/'+this.form.id, this.form)).data.message;
                        location.reload(true);
                    } else {
                        this.form.content = tinyMCE.get('content').getContent();
                        this.form.categorie_ids = $("#categorie").val()
                        this.location = (await axios.post('/contents', this.form)).data.message;
                        location.reload(true);
                    }
                } catch(error) {
                    this.form.errors = error.response.data.form.errors;
                }
            },
            beforeOpen(event) {
                this.getCategories();
                if (event.params.id){
                    this.load(event.params.id)
                }
                if (event.params.referenceable_type){
                    this.form.referenceable_type = event.params.referenceable_type;
                }
                if (event.params.referenceable_id){
                    this.form.referenceable_id = event.params.referenceable_id;
                }
                if (event.params.categorie_ids){
                    this.form.categorie_ids = event.params.categorie_ids;
                }
                this.method = event.params.method;
            },
            async getCategories() {
                try {
                    this.categories = (await axios.get('/categories/')).data.message;
                } catch(error) {
                    console.log('loading failed')
                }
            },
            async addCategorie() {
                if ($("#add_categorie").val() !== ''){
                    try {
                        this.categories = (await axios.post('/categories', {'title': $("#add_categorie").val() })).data.message;
                    } catch(error) {
                        alert(error.response.data.form.errors);
                    }
                    this.initSelect2();
                    this.toggle_categorie_input();
                }
            },
            opened(){
                this.$initTinyMCE();
                this.initSelect2();
            },
            initSelect2(){
                $("#categorie").select2({
                    dropdownParent: $("#categorie").parent(),
                    allowClear: true
                });
            },
            toggle_categorie_input() {
                this.show_add_categorie = this.show_add_categorie === 'invisible' ? '' : 'invisible';
            },
            beforeClose() {
                //console.log('close')
            },
            onChange(value){
                this.form.categorie_ids = value.id;
            },
            async load(id) {
                try {
                    this.form.populate((await axios.get('/contents/'+id)).data.message);
                } catch(error) {
                    //console.log('loading failed')
                }
            },
            close(){
                this.$modal.hide('content-create-modal');
            }
        },
        mounted() {
        },
    }
</script>
