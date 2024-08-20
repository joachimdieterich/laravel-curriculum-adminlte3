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
        width="900"
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
                    :class="form.errors.title ? 'has-error' : ''">
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
                    <label for="create_content_modal_content">{{ trans('global.content.fields.content') }}</label>
                    <textarea
                        id="create_content_modal_content"
                        name="create_content_modal_content"
                        class="form-control description my-editor "
                        v-model="form.content"
                    ></textarea>
                    <p class="help-block" v-if="form.errors.content" v-text="form.errors.content[0]"></p>
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
                component_id: this._uid,
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
                        this.form.content = tinyMCE.get('create_content_modal_content').getContent();
                        this.location = (await axios.patch('/contents/' + this.form.id, this.form)).data.message;
                        this.$parent.$emit('addContent', this.form);
                    } else {
                        this.form.content = tinyMCE.get('create_content_modal_content').getContent();
                        this.location = (await axios.post('/contents', this.form)).data.message;
                        this.$parent.$emit('addContent', this.form);
                    }
                    this.close();
                } catch(error) {
                    this.form.errors = error.response.data.form.errors;
                }
            },
            beforeOpen(event) {
                this.form.id = '';
                this.form.title = '';
                this.form.content = '';

                if (event.params.referenceable_type) {
                    this.form.referenceable_type = event.params.referenceable_type;
                }
                if (event.params.referenceable_id) {
                    this.form.referenceable_id = event.params.referenceable_id;
                }
                this.method = event.params.method;

                if (event.params.id) {
                    this.load(event.params.id);
                }
            },
            opened() {
                const plugins = "autolink link table lists code" + (this.method === 'patch' ? ' example' : '');
                this.$initTinyMCE([
                        plugins
                    ],
                    {
                        height: 300,
                        'referenceable_type': this.form.referenceable_type,
                        'referenceable_id': this.form.referenceable_id,
                        'eventHubCallbackFunction': 'insertContent',
                        'eventHubCallbackFunctionParams': this.component_id,
                    }
                );
            },
            beforeClose() {
                //console.log('close')
            },
            async load(id) {
                try {
                    this.form.populate((await axios.get('/contents/' + id)).data.message);
                    tinyMCE.get('create_content_modal_content').setContent(this.form.content);
                } catch(error) {
                    //console.log('loading failed')
                }
            },
            close() {
                this.$modal.hide('content-create-modal');
            }
        },
        mounted() {

        },
    }
</script>
