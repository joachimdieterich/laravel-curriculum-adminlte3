<template>
    <Transition name="modal" >
        <div v-if="globalStore.modals[$options.name]?.show"
             class="modal-mask "
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
                            @click="globalStore?.closeModal($options.name)">
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
                         @click="globalStore?.closeModal($options.name)">
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
    import {useGlobalStore} from "../../store/global";

    export default {
        name: 'content-modal',
        components:{
            Editor,
        },
        props: {
            params: {
                type: Object
            },  //{ 'modelId': curriculum.id, 'modelUrl': 'curriculum' , 'shareWithToken': true, 'canEditCheckbox': false}
        },
        setup () { //use database store
            const globalStore = useGlobalStore();

            return {
                globalStore,
            }
        },
        data() {
            return {
                component_id: this.$.uid,
                method: 'post',
                url: '/contents',
                form: new Form({
                    'id':'',
                    'title': '',
                    'content': '',
                    'subscribable_id': null,
                    'subscribable_type': null,
                }),
                tinyMCE: this.$initTinyMCE(
                    [
                        "autolink link curriculummedia table lists code"
                    ],
                    {
                        'eventHubCallbackFunction': 'insertContent',
                        'eventHubCallbackFunctionParams': this.component_id,
                    }
                ),

                search: '',
            }
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
                    })
                    .catch(e => {
                        console.log(e.response);
                    });
            },
        },
        mounted() {
            this.globalStore.registerModal(this.$options.name);
            this.globalStore.$subscribe((mutation, state) => {
                if (mutation.events.key === this.$options.name){
                    const params = state.modals[this.$options.name].params;

                    this.form.reset();
                    if (typeof (params) !== 'undefined'){
                        this.form.subscribable_type = params.subscribable_type;
                        this.form.subscribable_id = params.subscribable_id;
                        this.form.populate(params);
                        if (this.form.id != ''){
                            this.method = 'patch';
                        } else {
                            this.method = 'post';
                        }
                    }
                }
            });
        },
    }
</script>
<style scoped>

</style>
