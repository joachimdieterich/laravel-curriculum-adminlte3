<template>
    <modal 
        id="enabling-objective-modal" 
        name="enabling-objective-modal" 
        height="auto" 
        :adaptive=true
        :scrollable=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen"
        @before-close="beforeClose"
        style="z-index: 1100">
        <div class="card" style="margin-bottom: 0px !important">
            <div class="card-header">
                <h3 class="card-title">
                    <span v-if="method === 'post'">
                        {{ trans('global.create')  }} 
                    </span>
                    
                    <span v-if="method === 'patch'">
                        {{ trans('global.update')  }} 
                    </span>
                   
                    {{ trans('global.enablingObjective.title_singular') }}
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
            <form>
            <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                <div class="form-group "
                    :class="form.errors.title ? 'has-error' : ''"
                      >
                    <label for="title">{{ trans('global.enablingObjective.fields.title') }} *</label>
                    <Editor
                    api-key="no-api-key"
                    id="title"
                    name="title"
                    initialValue="Title"
                    v-model="form.title"
                    :init="editorConfig"
                    ></Editor>
                     <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                </div>
                <div class="form-group "
                    :class="form.errors.description ? 'has-error' : ''"
                    >
                    <label for="description">{{ trans('global.enablingObjective.fields.description') }}</label>
                    <Editor
                    api-key="no-api-key"
                    id="description"
                    name="description"
                    initialValue="Description"
                    v-model="form.description"
                    :init="editorConfig"
                    ></Editor>
                    <p class="help-block" v-if="form.errors.description" v-text="form.errors.description[0]"></p>
                </div>
                
                <div class="form-group" 
                    :class="form.errors.title ? 'has-error' : ''">
                    <label for="level_id" >{{ trans("global.objectiveType.title_singular") }}</label>

                    <multiselect v-model="value" 
                                :options="levels" 
                                :multiple="false" 
                                :close-on-select="true" 
                                :clear-on-select="false" 
                                :preserve-search="true" 
                                placeholder="Pick some" 
                                label="title" 
                                track-by="id" 
                                :preselect-first="true"
                                @input="onChange">
                        <template slot="selection" slot-scope="{ values, search, isOpen }">
                               <span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">
                                   {{ value.length }} options selected
                               </span>
                        </template>
                    </multiselect>
                    <p class="help-block" v-if="form.errors.objective_type_id" v-text="form.errors.objective_type_id[0]"></p>   
                </div>
            </div>
                <div class="card-footer">
                    <span class="pull-right">
                         <button type="button" class="btn btn-info" data-widget="remove" @click="close()">{{ trans('global.cancel') }}</button>
                         <button class="btn btn-primary" @click="submit()" >{{ trans('global.save') }}</button>
                    </span>
                </div>
            </form>
        </div>
    </modal>
</template>

<script>
    import Form from 'form-backend-validation';
    import Multiselect from 'vue-multiselect';
    import Editor from '@tinymce/tinymce-vue'
    
    export default {
        data() {
            return {
                value: null,
                levels: [],
                method: 'post',
                requestUrl: '/enablingObjectives',
                form: new Form({
                    'id': '',
                    'title': '',
                    'description': '',
                    'time_approach': '',
                    'curriculum_id': '',
                    'terminal_objective_id': '',
                    'level_id': null,
                }),
                editorConfig: {
                    menubar: false,
                    branding: false,
                    plugins: [
                      'advlist autolink lists link image charmap print preview anchor',
                      'searchreplace visualblocks code fullscreen',
                      'insertdatetime media table paste code help wordcount'
                    ],
                    toolbar:
                      'undo redo | formatselect | bold italic backcolor | \
                      alignleft aligncenter alignright alignjustify | \
                      bullist numlist outdent indent | removeformat | code | help'
                }
            }
        },
        
        methods: {
            onChange(value) {
                this.form.level_id = value.id
            },
            findObjectByKey(array, key, value) {
                for (var i = 0;
                i < array.length; i++) {
                    if (array[i][key] === value) {
                        return array[i];
                    }
                }
                return null;
            },
            beforeOpen(event) { 
                if (event.params.objective){
                    this.method = event.params.method;
                    this.form.populate( event.params.objective );
                    //set selected
                    this.value = {
                        'id': this.form.level_id,
                        'title': this.findObjectByKey(this.levels, 'id', this.form.level_id).title
                    };
                }
            },
            
            beforeClose() { 
                //console.log('close') 
            },
            loadData: function () {
                axios.get('/levels').then(response => {
                    this.levels = response.data;
                }).catch(e => {
                    this.form.errors = error.response.data.errors;
                });
            },
            submit() {
                var method = this.method.toLowerCase();
                
                if (method === 'patch'){
                    this.requestUrl += '/'+this.form.id;
                } 
                
                this.form.submit(method, this.requestUrl)
                    .then(/*response => alert('Your objective was created'+response.message.title)*/)
                    .catch(response => function ()
                        {
                            if (response.errors) 
                            {
                               alert(response.errors); 
                            }
                        });
                //todo .then .catch
            },
            close(){
                this.$modal.hide('enabling-objective-modal');
            }
        },
        created() {
            this.loadData();
            
        },
        mounted() {
            //console.log('Component mounted.')
        },
        components: {
            Multiselect,
            Editor
        }
    }
</script>