<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @mouseup.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        <span v-if="method === 'post'">
                            {{ trans('global.organization.create') }}
                        </span>
                        <span v-if="method === 'patch'">
                            {{ trans('global.organization.edit') }}
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
                                v-permission="'is_admin'"
                                class="form-group"
                                :class="form.errors.common_name ? 'has-error' : ''"
                            >
                                <label for="title">{{ trans('global.organization.fields.common_name') }}</label>
                                <input
                                    id="common_name"
                                    type="text"
                                    name="common_name"
                                    class="form-control"
                                    v-model="form.common_name"
                                    readonly
                                />
                                <p class="help-block" v-if="form.errors.common_name" v-text="form.errors.common_name[0]"></p>
                            </div>
        
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
                                    :placeholder="trans('global.organization.fields.title') + ' *'"
                                    :disabled="!checkPermission('is_admin')"
                                    required
                                />
                                <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                            </div>
        
                            <div class="form-group">
                                <Editor
                                    id="description"
                                    name="description"
                                    class="form-control"
                                    :init="tinyMCE"
                                    v-model="form.description"
                                />
                            </div>
        
                            <div class="form-group">
                                <label for="street">{{ trans('global.organization.fields.street') }}</label>
                                <input
                                    id="street"
                                    type="text"
                                    name="street"
                                    class="form-control"
                                    v-model="form.street"
                                    :placeholder="trans('global.organization.fields.street')"
                                />
                                <p class="help-block" v-if="form.errors.street" v-text="form.errors.street[0]"></p>
                            </div>
        
                            <div class="form-group">
                                <label for="postcode">{{ trans('global.organization.fields.postcode') }}</label>
                                <input
                                    id="postcode"
                                    type="text"
                                    name="postcode"
                                    class="form-control"
                                    v-model="form.postcode"
                                    :placeholder="trans('global.organization.fields.postcode')"
                                />
                                <p class="help-block" v-if="form.errors.postcode" v-text="form.errors.postcode[0]"></p>
                            </div>
        
                            <div class="form-group">
                                <label for="city">{{ trans('global.organization.fields.city') }}</label>
                                <input
                                    id="city"
                                    type="text"
                                    name="city"
                                    class="form-control"
                                    v-model="form.city"
                                    :placeholder="trans('global.organization.fields.city')"
                                />
                                <p class="help-block" v-if="form.errors.city" v-text="form.errors.city[0]"></p>
                            </div>
        
                            <div class="form-group">
                                <label for="lms_url">{{ trans('global.organization.fields.lms_url') }}</label>
                                <input
                                    id="lms_url"
                                    type="text"
                                    name="lms_url"
                                    class="form-control"
                                    v-model="form.lms_url"
                                    placeholder="https:\\[your_lms_url]"
                                />
                                <p class="help-block" v-if="form.errors.lms_url" v-text="form.errors.lms_url[0]"></p>
                            </div>
        
                            <div class="form-group">
                                <label for="phone">{{ trans('global.organization.fields.phone') }}</label>
                                <input
                                    id="phone"
                                    type="text"
                                    name="phone"
                                    class="form-control"
                                    v-model="form.phone"
                                    :placeholder="trans('global.organization.fields.phone')"
                                />
                                <p class="help-block" v-if="form.errors.phone" v-text="form.errors.phone[0]"></p>
                            </div>
        
                            <div class="form-group">
                                <label for="email">{{ trans('global.organization.fields.email') }}</label>
                                <input
                                    id="email"
                                    type="text"
                                    name="email"
                                    class="form-control"
                                    v-model="form.email"
                                    :placeholder="trans('global.organization.fields.email')"
                                />
                                <p class="help-block" v-if="form.errors.email" v-text="form.errors.email[0]"></p>
                            </div>
        
                            <Select2
                                id="country_id"
                                name="country_id"
                                url="/countries"
                                model="country"
                                option_id="alpha2"
                                option_label="lang_de"
                                :selected="form.country_id"
                                @selectedValue="(id) => {
                                    this.form.country_id = id[0];
                                    this.form.state_id = null;
                                }"
                            />
        
                            <Select2
                                id="state_id"
                                name="state_id"
                                :url="'/countries/' + form.country_id + '/states'"
                                model="state"
                                :term="form.country_id"
                                option_id="code"
                                option_label="lang_de"
                                :selected="form.state_id"
                                :readOnly="form.country_id == null"
                                @selectedValue="(id) => {
                                    this.form.state_id = id[0];
                                }"
                            />
        
                            <Select2 v-if="checkPermission('is_admin')"
                                id="organization_type_id"
                                name="organization_type_id"
                                url="/organizationTypes"
                                model="organizationType"
                                option_id="id"
                                option_label="title"
                                :selected="form.organization_type_id"
                                @selectedValue="(id) => {
                                    this.form.organization_type_id = id[0];
                                }"
                            />
        
                            <Select2 v-if="checkPermission('is_admin')"
                                id="status_definition_id"
                                name="status_definition_id"
                                url="/statusdefinitions"
                                model="status"
                                css="mb-0"
                                option_id="status_definition_id"
                                option_label="lang_de"
                                :selected="form.status_id"
                                @selectedValue="(id) => {
                                    this.form.status_id = id[0];
                                }"
                            />
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="organization-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="organization-save"
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
import {useGlobalStore} from "../../store/global";

export default {
    name: 'organization-modal',
    components: {
        Editor,
        Select2,
    },
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
                id: '',
                common_name: '',
                title: '',
                description: '',
                street: '',
                postcode: '',
                city: '',
                state_id: 'DE-RP',
                country_id: 'DE',
                organization_type_id: 1,
                phone: null,
                email: null,
                status_id: 1,
                lms_url: '',
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink link curriculummedia autoresize",
                ],
                {
                    'callback': 'insertContent',
                    'callbackId': this.component_id,
                }
            ),
            onlyAddress: {
                type: Boolean,
                default: false,
            },
            onlyLmsUrl: {
                type: Boolean,
                default: false,
            },
        }
    },
    methods: {
        submit() {
            if (this.method === 'patch') {
                this.update();
            } else {
                this.add();
            }

            this.globalStore.closeModal(this.$options.name);
        },
        add() {
            axios.post('/organizations', this.form)
                .then(r => {
                    this.$eventHub.emit('organization-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch('/organizations/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('organization-updated', r.data);
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
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                    this.form.description = this.$decodeHtml(this.form.description);
                    this.onlyAddress = params.onlyAddress ?? false;
                    this.onlyLmsUrl = params.onlyLmsUrl ?? false;
                    if (this.form.id !== '') {
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