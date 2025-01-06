<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
             class="modal-mask"
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
                    <button type="button"
                            class="btn btn-tool"
                            @click="globalStore?.closeModal($options.name)">
                        <i class="fa fa-times"></i>
                    </button>
                 </div>
            </div>
            <div class="modal-body">
                <div v-if="(!onlyAddress && !onlyLmsUrl)"
                    v-permission="'is_admin'"
                    class="form-group "
                    :class="form.errors.common_name ? 'has-error' : ''"
                >
                    <label for="title">{{ trans('global.organization.fields.common_name') }}</label>
                    <input
                        type="text" id="common_name"
                        name="common_name"
                        class="form-control"
                        v-model="form.common_name"
                        readonly
                    />
                    <p class="help-block" v-if="form.errors.common_name" v-text="form.errors.common_name[0]"></p>
                </div>
                <div v-if="(!onlyAddress && !onlyLmsUrl)"
                     class="form-group "
                    :class="form.errors.title ? 'has-error' : ''"
                      >
                    <label for="title">{{ trans('global.organization.fields.title') }} *</label>
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
                <div v-if="(!onlyAddress && !onlyLmsUrl)"
                     class="form-group ">
                    <label for="description">{{ trans('global.organization.fields.description') }}</label>
                    <Editor
                        id="description"
                        name="description"
                        class="form-control"
                        :init="tinyMCE"
                        :initial-value="form.description"
                    />
                    <p class="help-block" v-if="form.errors.description" v-text="form.errors.description[0]"></p>
                </div>
                <div v-if="(!onlyAddress && !onlyLmsUrl) || onlyAddress"
                     class="form-group ">
                    <label for="street">{{ trans('global.organization.fields.street') }}</label>
                    <input
                        type="text" id="street"
                        name="street"
                        class="form-control"
                        v-model="form.street"
                        placeholder="Street"
                        />
                    <p class="help-block" v-if="form.errors.street" v-text="form.errors.street[0]"></p>
                </div>
                <div v-if="(!onlyAddress && !onlyLmsUrl) || onlyAddress"
                     class="form-group ">
                    <label for="postcode">{{ trans('global.organization.fields.postcode') }}</label>
                    <input
                        type="text" id="postcode"
                        name="postcode"
                        class="form-control"
                        v-model="form.postcode"
                        placeholder="Postcode"
                        />
                    <p class="help-block" v-if="form.errors.postcode" v-text="form.errors.postcode[0]"></p>
                </div>
                <div v-if="(!onlyAddress && !onlyLmsUrl) || onlyAddress"
                     class="form-group ">
                    <label for="city">{{ trans('global.organization.fields.city') }}</label>
                    <input
                        type="text" id="city"
                        name="city"
                        class="form-control"
                        v-model="form.city"
                        placeholder="City"
                        />
                    <p class="help-block" v-if="form.errors.city" v-text="form.errors.city[0]"></p>
                </div>
                <div
                    v-if="(!onlyAddress && !onlyLmsUrl) || onlyLmsUrl"
                    class="form-group ">
                    <label for="lms_url">{{ trans('global.organization.fields.lms_url') }}</label>
                    <input
                        type="text" id="lms_url"
                        name="lms_url"
                        class="form-control"
                        v-model="form.lms_url"
                        placeholder="https:\\[your_lms_url]"
                        />
                    <p class="help-block" v-if="form.errors.lms_url" v-text="form.errors.lms_url[0]"></p>
                </div>
                <div
                    v-if="(!onlyAddress && !onlyLmsUrl)"
                    class="form-group ">
                    <label for="phone">{{ trans('global.organization.fields.phone') }}</label>
                    <input
                        type="text" id="phone"
                        name="phone"
                        class="form-control"
                        v-model="form.phone"
                        placeholder="Phone"
                    />
                    <p class="help-block" v-if="form.errors.phone" v-text="form.errors.phone[0]"></p>
                </div>
                <div
                    v-if="(!onlyAddress && !onlyLmsUrl)"
                    class="form-group ">
                    <label for="email">{{ trans('global.organization.fields.email') }}</label>
                    <input
                        type="text" id="email"
                        name="email"
                        class="form-control"
                        v-model="form.email"
                        placeholder="Email"
                        />
                    <p class="help-block" v-if="form.errors.email" v-text="form.errors.email[0]"></p>
                </div>

                <Select2
                    v-if="(!onlyAddress && !onlyLmsUrl)"
                    id="country_id"
                    name="country_id"
                    option_id="alpha2"
                    option_label="lang_de"
                    url="/countries"
                    model="country"
                    :selected="this.form.country_id"
                    @selectedValue="(id) => {
                        console.log(id);
                        this.form.country_id = id;
                        this.form.state_id = '';
                    }"
                >
                </Select2>

                <Select2
                    v-if="(!onlyAddress && !onlyLmsUrl)"
                    id="state_id"
                    name="state_id"
                    option_id="code"
                    option_label="lang_de"
                    :list="this.states"
                    :url="'/countries/' + this.form.country_id + '/states/'"
                    :term="this.form.country_id"
                    model="state"
                    :selected="this.form.state_id"
                    @selectedValue="(id) => {
                        this.form.state_id = id;
                    }"
                >
                </Select2>

                <Select2
                    v-if="(!onlyAddress && !onlyLmsUrl)"
                    id="organization_type_id"
                    name="organization_type_id"
                    url="/organizationTypes"
                    model="organizationType"
                    option_id="id"
                    option_label="title"
                    :selected="this.form.organization_type_id"
                    @selectedValue="(id) => {
                        this.form.organization_type_id = id;
                    }"
                >
                </Select2>

                <Select2
                    v-if="(!onlyAddress && !onlyLmsUrl)"
                    id="status_definition_id"
                    name="status_definition_id"
                    url="/statusdefinitions"
                    model="status"
                    option_id="status_definition_id"
                    option_label="lang_de"
                    :selected="this.form.status_id"
                    @selectedValue="(id) => {
                        this.form.status_id = id;
                    }"
                >
                </Select2>
            </div>
            <div class="card-footer">
                 <span class="pull-right">
                     <button
                         id="organization-cancel"
                         type="button"
                         class="btn btn-default"
                         @click="globalStore?.closeModal($options.name)">
                         {{ trans('global.cancel') }}
                     </button>
                     <button
                         id="organization-save"
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
    name: 'organization-modal',
    components: {
        Editor,
        Select2
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
            url: '/organizations',
            form: new Form({
                'id':'',
                'common_name':'',
                'title':'',
                'description': '',
                'street': '',
                'postcode': '',
                'city': '',
                'state_id': 'DE-RP',
                'country_id': 'DE',
                'organization_type_id': 1,
                'phone': null,
                'email': null,
                'status_id': 1,
                'lms_url': '',
            }),
            countries: [],
            states: [],
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink link curriculummedia"
                ],
                {
                    'eventHubCallbackFunction': 'insertContent',
                    'eventHubCallbackFunctionParams': this.component_id,
                }
            ),
            onlyAddress: {
                type: Boolean,
                default: false
            },
            onlyLmsUrl: {
                type: Boolean,
                default: false
            },
            search: '',
        }
    },
    methods: {
        submit(method) {
            if(tinyMCE.get('description')){
                this.form.description = tinyMCE.get('description').getContent();
            }
            if (method === 'patch') {
            this.update();
            } else {
            this.add();
            }
        },
        add() {
            axios.post(this.url, this.form)
                .then(r => {
                    this.$eventHub.emit('organization-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch(this.url + '/' + this.form.id, this.form)
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
