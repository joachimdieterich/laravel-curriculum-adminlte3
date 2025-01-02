<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
             class="modal-mask">
        <div class="modal-container">
            <div class="card-header">
                <h3 class="card-title">
                    <span v-if="method === 'post'">
                        {{ trans('global.contactDetail.create') }}
                    </span>
                    <span v-if="method === 'patch'">
                        {{ trans('global.contactDetail.edit') }}
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
                    :class="form.errors.email ? 'has-error' : ''"
                      >
                    <label for="title">
                        {{ trans('global.contactDetail.fields.email') }} *
                    </label>
                    <input
                        type="text"
                        id="email"
                        name="email"
                        class="form-control"
                        v-model="form.email"
                        :placeholder="trans('global.contactDetail.fields.email')"
                        required
                        />
                     <p class="help-block" v-if="form.errors.email" v-text="form.errors.email[0]"></p>
                </div>
                <div class="form-group "
                     :class="form.errors.phone ? 'has-error' : ''">
                    <label for="title">
                        {{ trans('global.contactDetail.fields.phone') }} *
                    </label>
                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        class="form-control"
                        v-model="form.phone"
                        :placeholder="trans('global.contactDetail.fields.phone')"
                        required
                    />
                    <p class="help-block" v-if="form.errors.phone" v-text="form.errors.phone[0]"></p>
                </div>
                <div class="form-group "
                     :class="form.errors.mobile ? 'has-error' : ''">
                    <label for="title">
                        {{ trans('global.contactDetail.fields.mobile') }} *
                    </label>
                    <input
                        type="text"
                        id="mobile"
                        name="mobile"
                        class="form-control"
                        v-model="form.mobile"
                        :placeholder="trans('global.contactDetail.fields.mobile')"
                        required
                    />
                    <p class="help-block" v-if="form.errors.mobile" v-text="form.errors.mobile[0]"></p>
                </div>

                <div class="form-group ">
                    <label for="notes">
                        {{ trans('global.contactDetail.fields.notes') }}
                    </label>
                    <Editor
                        id="notes"
                        name="notes"
                        class="form-control"
                        :init="tinyMCE"
                        :initial-value="form.notes"
                    />
                    <p class="help-block" v-if="form.errors.notes" v-text="form.errors.notes[0]"></p>
                </div>
            </div>
            <div class="card-footer">
                 <span class="pull-right">
                     <button
                         id="contactDetail-cancel"
                         type="button"
                         class="btn btn-default"
                         @click="globalStore?.closeModal($options.name)">
                         {{ trans('global.cancel') }}
                     </button>
                     <button
                         id="contactDetail-save"
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
    name: 'contact-modal',
    components:{
        Editor,
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
            url: '/contactDetails',
            form: new Form({
                'id':'',
                'email': '',
                'phone': '',
                'mobile': '',
                'notes': '',
            }),
            countries: [],
            states: [],
            tinyMCE: this.$initTinyMCE(
                [
                    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                    "searchreplace wordcount visualblocks visualchars code fullscreen",
                    "insertdatetime media nonbreaking save table directionality",
                    "emoticons template paste textpattern curriculummedia autoresize"
                ],
                {
                    'eventHubCallbackFunction': 'insertContent',
                    'eventHubCallbackFunctionParams': this.component_id,

                },
                " | customDateButton | insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | example link image media | insertFirstname insertLastname organizationTitle organizationStreet organizationPostcode organizationCity contactDetailDate | usersProgress",
                "span[id|class|style|name|reference_type|reference_id|min_value]",
            ),

            search: '',
        }
    },
    methods: {
        submit(method) {
            this.form.notes = tinyMCE.get('notes').getContent();

            if (method === 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            axios.post(this.url, this.form)
                .then(r => {
                    this.$eventHub.emit('contactDetail-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch(this.url + '/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('contactDetail-updated', r.data);
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
                    this.form.notes = this.$decodeHtml(this.form.notes)
                    if (this.form.id !== ''){
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
