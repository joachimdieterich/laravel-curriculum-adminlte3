<template>
    <Transition name="modal">
        <div v-if="show"
             class="modal-mask"
        >
        <div class="modal-container">
            <div class="card-header">
                <h3 class="card-title">
                    <span v-if="method === 'post'">
                        {{ trans('global.content.create') }}
                    </span>
                    <span v-if="method === 'patch'">
                        {{ trans('global.content.edit') }}
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
                        :placeholder="trans('global.content.fields.title')"
                        required
                        />
                     <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                </div>

                <div class="form-group">
                    <label for="content">
                        {{ trans('global.content.fields.content') }}
                    </label>
                    <Editor
                        id="content"
                        name="content"
                        :placeholder="trans('global.content.fields.content')"
                        class="form-control"
                        :init="tinyMCE"
                        :initial-value="form.content"
                    ></Editor>
                </div>
            </div>
                <div class="card-footer">
                     <span class="pull-right">
                         <button
                             id="content-cancel"
                             type="button"
                             class="btn btn-default mr-2"
                             @click="$emit('close')">
                             {{ trans('global.cancel') }}
                         </button>
                         <button
                             id="content-save"
                             class="btn btn-primary"
                             @click="submit(method)" >
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
    import Editor from '@tinymce/tinymce-vue';


    export default {
        components:{
            Editor,
        },
        props: {
            show: {
                type: Boolean
            },
            params: {
                type: Object
            },  //{ 'modelId': curriculum.id, 'modelUrl': 'curriculum' , 'shareWithToken': true, 'canEditCheckbox': false}

        },
        data() {
            return {
                component_id: this._uid,
                method: 'post',
                url: '/contents',
                form: new Form({
                    'id':'',
                    'title': '',
                    'content': '',
                    'subscribable_type': null,
                    'subscribable_type': null,
                }),
                tinyMCE: this.$initTinyMCE(
                    [
                        "autolink link curriculummedia table lists"
                    ],
                    {
                        'eventHubCallbackFunction': 'insertContent',
                        'eventHubCallbackFunctionParams': this.component_id,
                    }
                ),
                search: '',
            }
        },
        watch: {
            params: function(newVal, oldVal) {
                this.form.reset();
                this.form.subscribable_type = newVal.subscribable_type;
                this.form.subscribable_id = newVal.subscribable_id;
                this.form.populate(newVal);
                if (this.form.id != ''){
                    this.method = 'patch';
                } else {
                    this.method = 'post';
                }
            },

        },
        methods: {
             submit(method) {
                 this.form.content = tinyMCE.get('content').getContent();
                 if (method == 'patch') {
                     this.update();
                 } else {
                     this.add();
                 }
            },
            add(){
                axios.post(this.url, this.form)
                    .then(r => {
                        this.$eventHub.emit('content-added', r.data);
                    })
                    .catch(e => {
                        console.log(e.response);
                    });
            },
            update() {
                axios.patch(this.url + '/' + this.form.id, this.form)
                    .then(r => {
                        this.$eventHub.emit('content-updated', r.data);
                        // vorher: this.$parent.$emit('addContent', this.form);
                    })
                    .catch(e => {
                        console.log(e.response);
                    });
            }
        },
        mounted() {
        },
    }
</script>

