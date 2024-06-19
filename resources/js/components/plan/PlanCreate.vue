<template>
    <div class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <b v-if="this.method === 'post'" class="modal-title">
                        {{ trans('global.plan.create') }}
                    </b>
                    <b v-else>
                        {{ trans('global.plan.edit') }}
                    </b>
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-0">
                    <div class="card mb-0">
                        <div class="card-header border-bottom"
                            data-card-widget="collapse"
                        >
                            <h5 class="card-title">
                                Allgemein
                            </h5>
                        </div>
                        <div class="card-body pb-0">
                            <div class="form-group">
                                <Select2
                                    id="type_id"
                                    :label="trans('global.plan.fields.type') + ' *'"
                                    model="PlanType"
                                    :selected="this.form.type_id"
                                    url="/plans/getTypes"
                                    :list="[{id: 4, title: 'Trainingsplan'}]"
                                    style="width: 100%;"
                                    :placeholder="trans('global.pleaseSelect')"
                                    :readOnly="true"
                                    @selectedValue="(id) => this.form.type_id = id"
                                ></Select2>
                                <p v-if="errors.type_id == true" class="error-block" style="margin-top: -0.75rem;">
                                    {{ trans('validation.required') }}
                                </p>
                            </div>

                            <div class="form-group input-group">
                                <color-picker-input
                                    style="height: 42px;"
                                    class="input-group-prepend"
                                    v-model="form.color"
                                ></color-picker-input>
                                <input
                                    type="text"
                                    id="title"
                                    name="title"
                                    class="form-control ml-3"
                                    style="height:42px"
                                    v-model.trim="form.title"
                                    :placeholder="trans('global.title') + ' *'"
                                    required
                                />
                                <p v-if="errors.title == true" class="error-block">
                                    {{ trans('validation.required') }}
                                </p>
                            </div>

                            <div class="form-group">
                                <textarea
                                    id="description"
                                    name="description"
                                    :placeholder="trans('global.description')"
                                    class="form-control description "
                                    v-model.trim="form.description"
                                ></textarea>
                            </div>

                            <div class="form-group">
                                <label for="begin">
                                    {{ trans('global.plan.fields.begin') + ' *' }}
                                </label>
                                <date-picker
                                    v-model="form.begin"
                                    :input-attr="{ id: 'begin', required: true }"
                                    class="w-100"
                                    type="datetime"
                                    value-type="YYYY-MM-DD HH:mm:ss"
                                    :placeholder="trans('global.plan.fields.begin')"
                                ></date-picker>
                                <p v-if="errors.begin == true" class="error-block">
                                    {{ trans('validation.required') }}
                                </p>
                            </div>

                            <div class="form-group pb-2">
                                <label for="end">
                                    {{ trans('global.plan.fields.end') + ' *' }}
                                </label>
                                <date-picker
                                    v-model="form.end"
                                    :input-attr="{ id: 'end', required: true }"
                                    class="w-100"
                                    type="datetime"
                                    value-type="YYYY-MM-DD HH:mm:ss"
                                    :placeholder="trans('global.plan.fields.end')"
                                ></date-picker>
                                <p v-if="errors.end == true" class="error-block">
                                    {{ trans('validation.required') }}
                                </p>
                            </div>

                            <div class="form-group">
                                <input
                                    type="text"
                                    id="duration"
                                    name="duration"
                                    class="form-control"
                                    style="height:42px"
                                    v-model.trim="form.duration"
                                    :placeholder="trans('global.plan.fields.duration')"
                                />
                                <p class="help-block">
                                    {{ trans('global.plan.fields.duration_helper') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-0">
                        <div class="card-header  border-bottom"
                            data-card-widget="collapse"
                        >
                            <h5 class="card-title">
                                Berechtigungen
                            </h5>
                        </div>
                        <div class="card-body">
                            <span class="custom-control custom-switch custom-switch-on-green">
                                <input
                                    id="allow_copy"
                                    v-model="form.allow_copy"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                />
                                <label class="custom-control-label font-weight-light"
                                    for="allow_copy"
                                >
                                    {{ trans('global.plan.allow_copy') }}
                                </label>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button
                        type="button"
                        class="btn btn-default"
                        data-dismiss="modal"
                    >
                        {{ trans('global.cancel') }}
                    </button>
                    <button
                        type="button"
                        class="btn btn-primary"
                        @click="submit()"
                    >
                        {{ trans('global.save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Form from "form-backend-validation";
import FontAwesomePicker from "../../../views/forms/input/FontAwesomePicker";
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
import { list } from "postcss";
const Select2 =
    () => import('../forms/Select2');

export default {
    name: 'PlanCreate',
    props: {
        plan: {},
        method: '',
    },
    data() {
        return {
            component_id: this._uid,
            requestUrl: '/plans',
            form: new Form({
                'id': '',
                'type_id': 4,
                'title':  '',
                'description':  '',
                'begin': '',
                'end': '',
                'duration': '',
                'color':'#27AF60',
                'allow_copy': true,
            }),
            errors: { // required fields need to be initialised
                type_id: false,
                title: false,
                begin: false,
                end: false,
            },
        };
    },
    methods: {
        submit() {
            if (!this.checkRequired()) {
                return;
            }

            let method = this.method.toLowerCase();

            if (method === 'patch') {
                axios.patch(this.requestUrl + '/' + this.form.id, this.form)
                    .then(res => { // Tell the parent component we've updated a plan
                        this.$eventHub.$emit("plan-updated", res.data.plan);
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error);
                    });
            } else {
                axios.post(this.requestUrl, this.form)
                    .then(res => {
                        window.location = res.data.message;
                        //this.$eventHub.$emit("plan-added", res.data.message);
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error)
                    });
            }

            $('#modal-plan-form').modal('hide');
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
        updateForm(data) {
            this.form.id = data.id;
            // TODO: #462 temporary solution for select2 not working in safari/firefox
            this.form.type_id = 4;
            // this.form.type_id = data.type_id;
            this.form.title = data.title;
            this.form.description = this.htmlToText(data.description);
            this.form.begin = data.begin;
            this.form.end = data.end;
            this.form.duration = data.duration;
            this.form.color = data.color;
            this.form.allow_copy = data.allow_copy;
        },
    },
    mounted() {
        $('#modal-plan-form').on('hidden.bs.modal', () => {
            // reset errors to hide error-blocks
            Object.keys(this.errors).forEach(key => this.errors[key] = false);
        });
    },
    watch: {
        plan: function(newVal, oldVal) {
            this.updateForm(newVal);
        },
        method: function (newVal, oldVal) {
            if (newVal == 'post') {
                this.form.reset();
            } else if (this.form.id == '') { // edge case when edit -> open create -> edit again
                this.updateForm(this.plan)
            }
        }
    },
    computed:{
        textColor: function() {
            return this.$textcolor(this.form.color, '#333333');
        }
    },
    components: {
        FontAwesomePicker,
        DatePicker,
        Select2,
    },
}
</script>
