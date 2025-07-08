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
                            {{ trans('global.plan.create') }}
                        </span>
                        <span v-else>
                            {{ trans('global.plan.edit') }}
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
                        <div
                            class="card-header border-bottom"
                            data-card-widget="collapse"
                        >
                            <h5 class="card-title">{{ trans('global.general') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <Select2
                                    id="type_id"
                                    :label="trans('global.plan.fields.type') + ' *'"
                                    model="PlanType"
                                    :selected="this.form.type_id"
                                    url="/planTypes"
                                    style="width: 100%;"
                                    :readOnly="true || (method == 'patch')"
                                    @selectedValue="(id) => this.form.type_id = id"
                                />
                                <p v-if="errors.type_id == true"
                                    class="error-block"
                                    style="margin-top: -0.75rem;"
                                >
                                    {{ trans('validation.required') }}
                                </p>
                            </div>

                            <div class="form-group">
                                <input
                                    type="text"
                                    id="title"
                                    name="title"
                                    class="form-control"
                                    v-model.trim="form.title"
                                    :placeholder="trans('global.title') + ' *'"
                                    required
                                />
                                <span v-if="errors.title == true"
                                    class="error-block"
                                    style="flex-basis: 100%;"
                                >
                                    {{ trans('validation.required') }}
                                </span>
                            </div>

                            <Editor
                                :id="'description' + component_id"
                                :name="'description' + component_id"
                                :init="tinyMCE"
                                v-model="form.description"
                            />
                            <!-- currently not in use -->
                            <!-- <div class="form-group">
                                <VueDatePicker
                                    v-model="form.date"
                                    :teleport="true"
                                    locale="de-DE"
                                    format="dd.MM.yyyy"
                                    range
                                    :partialRange="false"
                                    :enable-time-picker="false"
                                    :placeholder="trans('global.selectDateRange')"
                                    :select-text="trans('global.ok')"
                                    :cancel-text="trans('global.close')"
                                ></VueDatePicker>
                            </div> -->

                            <!-- currently not in use -->
                            <!-- <div class="form-group">
                                <input
                                    type="text"
                                    id="duration"
                                    name="duration"
                                    class="form-control"
                                    v-model.trim="form.duration"
                                    :placeholder="trans('global.plan.fields.duration')"
                                />
                                <p class="help-block" style="width: 0; min-width: 100%;">
                                    {{ trans('global.plan.fields.duration_helper') }}
                                </p>
                            </div> -->

                            <Select2 v-if="checkPermission('is_admin')"
                                id="user_id"
                                :label="trans('global.change_owner')"
                                css="mb-0 mt-3"
                                model="User"
                                url="/users"
                                :selected="form.owner_id"
                                @selectedValue="(id) => this.form.owner_id = id[0]"
                            />
                        </div>
                    </div>

                    <div class="card">
                        <div
                            class="card-header border-bottom"
                            data-card-widget="collapse"
                        >
                            <h5 class="card-title">{{ trans('global.display') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <v-swatches
                                    style="height: 42px"
                                    :swatches="$swatches"
                                    row-length="5"
                                    v-model="form.color"
                                    show-fallback
                                    fallback-input-type="color"
                                    @input="(id) => {
                                        if (id.isInteger) {
                                            this.form.color = id;
                                        }
                                    }"
                                    :max-height="300"
                                />
                                <MediumForm v-if="form.id"
                                    :id="'medium_form' + component_id"
                                    :medium_id="form.medium_id"
                                    :subscribable_id="form.id"
                                    subscribable_type="App\Plan"
                                    accept="image/*"
                                    @selectedValue="(id) => {
                                        // on removal of medium, directly update the resource
                                        if (this.form.medium_id !== null && id === null) {
                                            this.$eventHub.emit('plan-updated', {
                                                id: this.form.id,
                                                medium_id: null,
                                            });
                                        }
                                        this.form.medium_id = id;
                                    }"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div
                            class="card-header border-bottom"
                            data-card-widget="collapse"
                        >
                            <h5 class="card-title">{{ trans('global.permissions') }}</h5>
                        </div>
                        <div class="card-body">
                            <span class="custom-control custom-switch custom-switch-on-green">
                                <input
                                    id="allow_copy"
                                    v-model="form.allow_copy"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                />
                                <label
                                    class="custom-control-label font-weight-light"
                                    for="allow_copy"
                                >
                                    {{ trans('global.plan.allow_copy') }}
                                </label>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="plan-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="plan-save"
                            class="btn btn-primary ml-3"
                            :disabled="!form.title"
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
import Select2 from "../forms/Select2.vue";
import Editor from '@tinymce/tinymce-vue';
import MediumForm from "../media/MediumForm.vue";
import {useGlobalStore} from "../../store/global";
import VueDatePicker from "@vuepic/vue-datepicker";

export default {
    name: 'plan-modal',
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
            url: '/plans',
            form: new Form({
                id: '',
                type_id: 4,
                title:  '',
                description:  '',
                owner_id: null,
                date: null,
                begin: '',
                end: '',
                duration: '',
                color: '#27AF60',
                medium_id: null,
                allow_copy: true,
            }),
            errors: { // required fields need to be initialised
                type_id: false,
                title: false,
                begin: false,
                end: false,
            },
            search: '',
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink link lists code autoresize"
                ],
                {
                    'callback': 'insertContent',
                    'callbackId': this.component_id
                },
                "bold underline italic | alignleft aligncenter alignright alignjustify | bullist numlist | link code",
                ""
            ),
        }
    },
    methods: {
        submit() {
            if (!this.checkRequired()) {
                return;
            }
            // currently not in use
            // if (this.form.date !== null && this.form.date[1].toString() !== 'Invalid Date') {
            //     // format dates as 'yyyy-mm-dd'
            //     this.form.begin = this.form.date[0].toISOString().slice(0, 10);
            //     this.form.end = this.form.date[1].toISOString().slice(0, 10);
            // }

            if (this.method == 'patch') {
                this.update();
            } else {
                this.add();
            }

            this.globalStore.closeModal(this.$options.name);
        },
        add() {
            axios.post(this.url, this.form)
                .then(r => {
                    this.$eventHub.emit('plan-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch(this.url + '/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('plan-updated', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        checkRequired() {
            let filledOut = true;
            const fields = this.$el.querySelectorAll('[required]');

            for (const field of fields) {
                if (field.value.trim() === '') { // activate error-helper
                    this.errors[field.id] = true;
                    filledOut = false;
                } else { // deactivate error-helper
                    this.errors[field.id] = false;
                }
            }
            // needs to be set separately, because select2
            if (this.form.type_id == '') {
                this.errors['type_id'] = true;
                filledOut = false;
            } else {
                this.errors["type_id"] = false;
            }

            return filledOut;
        },
    },
    mounted() {
        Object.values(this.errors).forEach(value => value = false);
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show && !state.modals[this.$options.name].lock) {
                this.globalStore.lockModal(this.$options.name);
                const params = state.modals[this.$options.name].params;
                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                    this.form.date = [new Date(this.form.begin) ?? '', new Date(this.form.end) ?? ''];
                    this.form.description = this.$decodeHTMLEntities(params.description);

                    if (this.form.id != '') {
                        this.method = 'patch';
                    } else {
                        this.method = 'post';
                    }
                }
            }
        });
    },
    components: {
        VueDatePicker,
        Select2,
        Editor,
        MediumForm,
    },
}
</script>