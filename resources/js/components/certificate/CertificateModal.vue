<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
             class="modal-mask"
        >
        <div class="modal-container">
            <div class="card-header">
                <h3 class="card-title">
                    <span v-if="method === 'post'">
                        {{ trans('global.certificate.create') }}
                    </span>
                    <span v-if="method === 'patch'">
                        {{ trans('global.certificate.edit') }}
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
                    <label for="title">{{ trans('global.certificate.fields.title') }} *</label>
                    <input
                        type="text" id="title"
                        name="title"
                        class="form-control"
                        v-model="form.title"
                        :placeholder="trans('global.certificate.fields.title')"
                        required
                        />
                     <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                </div>
                <div class="form-group "
                     :class="form.errors.title ? 'has-error' : ''"
                >
                    <label for="title">{{ trans('global.certificate.fields.description') }} *</label>
                    <input
                        type="text" id="description"
                        name="title"
                        class="form-control"
                        v-model="form.description"
                        placeholder="Description"
                    />
                    <p class="help-block" v-if="form.errors.description" v-text="form.errors.description[0]"></p>
                </div>
                <div class="form-group ">
                    <label for="body">{{ trans('global.certificate.fields.body') }}</label>
                    <Editor
                        id="body"
                        name="body"
                        class="form-control"
                        :init="tinyMCE"
                        :initial-value="form.body"
                    />
                    <p class="help-block" v-if="form.errors.body" v-text="form.errors.body[0]"></p>
                </div>

                <Select2
                    id="curriculum_id"
                    name="curriculum_id"
                    url="/curricula"
                    model="curriculum"
                    :selected="this.form.curriculum_id"
                    @selectedValue="(id) => {
                        this.form.curriculum_id = id;
                    }"
                >
                </Select2>

                <Select2
                    id="organization_id"
                    name="organization_id"
                    :url="'/organizations'"
                    model="organization"
                    :selected="this.form.organization_id"
                    @selectedValue="(id) => {
                        this.form.organization_id = id;
                    }"
                >
                </Select2>
                <Select2
                    id="type"
                    name="type"
                    model="typ"
                    :list="[
                        {
                            'id':'group',
                            'title':'Gruppenzertifikat'
                        },
                        {
                            'id':'user',
                            'title':'Benutzerzertifikat'
                        }
                    ]"
                    :selected="this.form.type"
                >
                </Select2>
                <Switch
                    id="global"
                    name="global"
                    :label="trans('global.global.title_singular')"
                    v-model:checked="this.form.global"
                    @update:checked="this.form.global = $event"
                ></Switch>
            </div>
            <div class="card-footer">
                 <span class="pull-right">
                     <button
                         id="certificate-cancel"
                         type="button"
                         class="btn btn-default"
                         @click="globalStore?.closeModal($options.name)">
                         {{ trans('global.cancel') }}
                     </button>
                     <button
                         id="certificate-save"
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
    import Switch from "../forms/Switch.vue";
    import {useGlobalStore} from "../../store/global";

    export default {
        name: 'certificate-modal',
        components:{
            Switch,
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
                method: 'post',
                url: '/certificates',
                form: new Form({
                    'id':'',
                    'title': '',
                    'description': '',
                    'body': '',
                    'curriculum_id': '',
                    'organization_id': '',
                    'type': '',
                    'global': '',
                }),
                countries: [],
                states: [],
                tinyMCE: this.$initTinyMCE(
                    [
                        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                        "searchreplace wordcount visualblocks visualchars code fullscreen",
                        "insertdatetime media nonbreaking save table directionality",
                        "emoticons template paste textpattern curriculummedia"
                    ],
                    {
                        'eventHubCallbackFunction': 'insertContent',
                        'eventHubCallbackFunctionParams': this.component_id,

                    },
                    " | customDateButton | insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | example link image media | insertFirstname insertLastname organizationTitle organizationStreet organizationPostcode organizationCity certificateDate | usersProgress",
                    "span[id|class|style|name|reference_type|reference_id|min_value]",
                ),
                search: '',
            }
        },
        methods: {
             submit(method) {
                this.form.body = tinyMCE.get('body').getContent();

                if (method === 'patch') {
                    this.update();
                } else {
                    this.add();
                }
            },
            add(){
                axios.post(this.url, this.form)
                    .then(r => {
                        this.$eventHub.emit('certificate-added', r.data);
                    })
                    .catch(e => {
                        console.log(e.response);
                    });
            },
            update(){
                axios.patch(this.url + '/' + this.form.id, this.form)
                    .then(r => {
                        this.$eventHub.emit('certificate-updated', r.data);
                    })
                    .catch(e => {
                        console.log(e.response);
                    });
            }
        },
        mounted() {
            this.globalStore.registerModal(this.$options.name);
            this.globalStore.$subscribe((mutation, state) => {
                if (mutation.events.key === this.$options.name){
                    const params = state.modals[this.$options.name].params;
                    this.form.reset();
                    if (typeof (params) !== 'undefined'){
                        this.form.populate(params);
                        if (this.form.id !== ''){
                            this.form.body = this.$decodeHtml(this.form.body)
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
