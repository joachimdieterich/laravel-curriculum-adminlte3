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
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-0">
                    <div class="card mb-0">
                        <div class="card-header border-bottom"
                            data-card-widget="collapse">
                            <h5 class="card-title">
                                Allgemein
                            </h5>
                        </div>
                        <div class="card-body pb-0">
                            <div class="form-group ">
                                <label for="type_id">
                                    {{ trans('global.plan.fields.type') + ' *' }}
                                </label>
                                <select
                                    id="type_id"
                                    v-model="form.type_id"
                                    class="form-control select2"
                                    style="width:100%;"
                                >
                                    <option
                                        v-for="(type) in types"
                                        :id="type.id"
                                        :value="type.title"
                                    >
                                        {{ trans('global.plan.types.' + type.title) }}
                                    </option>
                                </select>
                            </div>

                            <div class="input-group pb-1">
                                <color-picker-input
                                    class="input-group-prepend"
                                    v-model="form.color">
                                </color-picker-input>
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
                                <p class="help-block" v-if="form.errors?.title" v-text="form.errors?.title[0]"></p>
                            </div>

                            <div class="form-group">
                                <textarea
                                    id="description"
                                    name="description"
                                    :placeholder="trans('global.description')"
                                    class="form-control description "
                                    v-model.trim="form.description"
                                ></textarea>
                                <p class="help-block" v-if="form.errors.description" v-text="form.errors.description[0]"></p>
                            </div>

                            <div class="form-group pt-2">
                                <label for="begin">
                                    {{ trans('global.plan.fields.begin') + ' *' }}
                                </label>
                                <date-picker
                                    v-model="form.begin" style="width:100%;"
                                    valueType="YYYY-MM-DD HH:mm:ss"
                                    :placeholder="trans('global.plan.fields.begin')"
                                ></date-picker>
                            </div>

                            <div class="form-group pt-2">
                                <label for="end">
                                    {{ trans('global.plan.fields.end') + ' *' }}
                                </label>
                                <date-picker
                                    v-model="form.end" style="width:100%;"
                                    valueType="YYYY-MM-DD HH:mm:ss"
                                    :placeholder="trans('global.plan.fields.end')"
                                ></date-picker>
                            </div>

                            <div class="form-group">
                                <input
                                    type="text"
                                    id="duration"
                                    name="duration"
                                    class="form-control ml-3"
                                    style="height:42px"
                                    v-model.trim="form.duration"
                                    :placeholder="trans('global.plan.fields.duration')"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="card mb-0">
                        <div class="card-header  border-bottom"
                            data-card-widget="collapse">
                            <h5 class="card-title">
                                Berechtigungen
                            </h5>
                        </div>
                        <div class="card-body">
                            <span class="custom-control custom-switch custom-switch-on-green">
                                <input
                                    v-model="form.allow_copy"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    :id="'allow_copy_'+ form.id">
                                <label class="custom-control-label font-weight-light"
                                    :for="'allow_copy_'+ form.id">
                                    {{ trans('global.plan.allow_copy') }}
                                </label>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button"
                            class="btn btn-default"
                            data-dismiss="modal">
                        {{ trans('global.cancel') }}
                    </button>
                    <button type="button"
                            class="btn btn-primary"
                            data-dismiss="modal"
                            @click="submit()">
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
                'type_id': '',
                'title':  '',
                'description':  '',
                'begin': '',
                'end': '',
                'duration': '',
                'color':'#27AF60',
                'allow_copy': true,
            }),
            types: {},
        };
    },
    methods: {
        submit() {
            let method = this.method.toLowerCase();

            if (method === 'patch') {
                axios.patch(this.requestUrl + '/' + this.form.id, this.form)
                    .then(res => { // Tell the parent component we've updated a task
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
        },
    },
    watch: {
        plan: function(newVal, oldVal) {
            this.form.id = newVal.id;
            this.form.type_id = newVal.type_id;
            this.form.title = newVal.title;
            this.form.description = this.htmlToText(newVal.description);
            this.form.begin = newVal.begin;
            this.form.end = newVal.end;
            this.form.duration = newVal.duration;
            this.form.color = newVal.color;
            this.form.allow_copy = newVal.allow_copy;
        },
        method: function (newVal, oldVal) {
            if (newVal == 'post') {
                this.form.reset();
            }
        }
    },
    computed:{
        textColor: function(){
            return this.$textcolor(this.form.color, '#333333');
        }
    },
    components: {
        FontAwesomePicker,
    }
}
</script>