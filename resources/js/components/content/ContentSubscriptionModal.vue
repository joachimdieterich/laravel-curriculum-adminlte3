<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
             class="modal-mask"
        >
        <div class="modal-container">
            <div class="card-header">
                <h3 class="card-title">
                    {{ trans('global.content.copy') }}
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
            </div>
            <Select2
                id="curricula"
                name="curricula"
                url="/curricula"
                model="curriculum"
                :selected="this.curriculum_id"
                @selectedValue="(id) => {
                    this.curriculum_id = id;
                    this.form.content_id = '';
                }"
            >
            </Select2>

            <Select2
                id="contents"
                name="contents"
                :url="'/curricula/' + this.curriculum_id + '/contents/'"
                :term="this.form.content_id"
                model="state"
                :selected="this.form.content_id"
                @selectedValue="(id) => {
                    this.form.content_id = id;
                }"
            >
            </Select2>

            <div class="card-footer">
                 <span class="pull-right">
                     <button
                         id="content-cancel"
                         type="button"
                         class="btn btn-default"
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
    import Select2 from "../forms/Select2.vue";
    import {useGlobalStore} from "../../store/global";


    export default {
        name: 'content-subscription-modal',
        components:{
            Editor,
            Select2
        },
        props: {},
        setup () {
            const globalStore = useGlobalStore();
            return {
                globalStore,
            }
        },
        data() {
            return {
                component_id: this.$.uid,
                url: '/contents',
                curriculum_id: null,
                form: new Form({
                    'content_id': [],
                    'subscribable_type': null,
                    'subscribable_id': null,
                }),
            }
        },
        methods: {
             submit() {
                 axios.post('/contentSubscriptions', this.form)
                     .then(r => {
                         this.$eventHub.emit('content-added', r.data);
                         // vorher: this.$parent.$emit('addContent', this.form);
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
