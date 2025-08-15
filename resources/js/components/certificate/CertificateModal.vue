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
                        <button
                            type="button"
                            class="btn btn-tool"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div
                                class="form-group"
                                :class="form.errors.title ? 'has-error' : ''"
                            >
                                <input
                                    id="title"
                                    type="text"
                                    name="title"
                                    class="form-control"
                                    v-model="form.title"
                                    :placeholder="trans('global.title') + ' *'"
                                    required
                                />
                                <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                            </div>
        
                            <div
                                class="form-group"
                                :class="form.errors.title ? 'has-error' : ''"
                            >
                                <input
                                    id="description"
                                    type="text"
                                    name="title"
                                    class="form-control"
                                    v-model="form.description"
                                    :placeholder="trans('global.description')"
                                />
                                <p class="help-block" v-if="form.errors.description" v-text="form.errors.description[0]"></p>
                            </div>

                            <div class="form-group">
                                <Editor
                                    id="body"
                                    name="body"
                                    class="form-control"
                                    :init="tinyMCE"
                                    v-model="form.body"
                                />
                                <p class="help-block" v-if="form.errors.body" v-text="form.errors.body[0]"></p>
                            </div>

                            <Select2
                                id="curriculum_id"
                                name="curriculum_id"
                                url="/curricula"
                                model="curriculum"
                                :label="trans('global.curriculum.title_singular') + ' *'"
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
                                :label="trans('global.organization.title_singular') + ' *'"
                                :selected="this.form.organization_id"
                                @selectedValue="(id) => {
                                    this.form.organization_id = id;
                                }"
                            />

                            <Select2
                                id="type"
                                name="type"
                                model="type"
                                :label="trans('global.type') + ' *'"
                                :list="[
                                    {
                                        id: 'group',
                                        title: 'Gruppenzertifikat',
                                    },
                                    {
                                        id: 'user',
                                        title: 'Benutzerzertifikat',
                                    }
                                ]"
                                :selected="this.form.type"
                            />

                            <Switch
                                id="global"
                                :label="trans('global.global.title_singular')"
                                v-model:checked="form.global"
                            />
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="certificate-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="certificate-save"
                            class="btn btn-primary ml-3"
                            @click="submit()"
                        >
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
    components: {
        Switch,
        Editor,
        Select2,
    },
    props: {},
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            form: new Form({
                id: null,
                title: '',
                description: '',
                body: '',
                curriculum_id: '',
                organization_id: '',
                type: '',
                global: false,
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                    "searchreplace wordcount visualblocks visualchars code fullscreen",
                    "insertdatetime media nonbreaking save table directionality",
                    "emoticons template paste textpattern autoresize"
                ],
                {
                    callback: 'insertContent',
                    callbackId: this.component_id,
                    placeholder: this.trans('global.certificate.fields.body') + ' *',
                },
                " | customDateButton | insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | example link image media | insertFirstname insertLastname organizationTitle organizationStreet organizationPostcode organizationCity certificateDate | usersProgress",
                "span[id|class|style|name|reference_type|reference_id|min_value]",
            ),
        }
    },
    methods: {
        submit() {
            if (this.form.body == '') { //body can be empty for group certificates
                this.form.body = '<p></p>';
            }

            if (this.method === 'patch') {
                this.update();
            } else {
                this.add();
            }

            this.globalStore.closeModal(this.$options.name);
        },
        add() {
            axios.post('/certificates', this.form)
                .then(r => {
                    this.$eventHub.emit('certificate-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch('/certificates/' + this.form.id, this.form)
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
            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params;
                this.form.reset();
                if (params !== undefined) {
                    this.form.populate(params);
                    console.log(this.form.id);
                    if (this.form.id == null) {
                        this.method = 'post';
                    } else {
                        this.form.body = this.$decodeHtml(this.form.body)
                        this.method = 'patch';
                    }
                }
            }
        });
    },
}
</script>