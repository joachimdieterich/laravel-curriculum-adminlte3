<template>
    <Transition name="modal">
        <div v-if="show"
             class="modal-mask"
        >
        <div class="modal-container">
            <div class="card-header">
                <h3 class="card-title">
                    <span v-if="method === 'post'">
                        {{ trans('global.organizationType.create') }}
                    </span>
                    <span v-if="method === 'patch'">
                        {{ trans('global.organizationType.edit') }}
                    </span>
                </h3>
                <div class="card-tools">
                    <button type="button"
                            class="btn btn-tool"
                            @click="$emit('close')">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>

                <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                    <div class="form-group "
                        :class="form.errors.title ? 'has-error' : ''"
                          >
                        <label for="title">{{ trans('global.organizationType.fields.title') }} *</label>
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

                    <div class="form-group ">
                        <VueDatePicker
                            v-model="form.date"
                            :range="{ partialRange: false }"
                            :teleport="true"
                            locale="de"
                            :select-text="trans('global.ok')"
                            :cancel-text="trans('global.close')"
                        ></VueDatePicker>
                    </div>

                </div>
                <div class="card-footer">
                     <span class="pull-right">
                         <button
                             id="organization-cancel"
                             type="button"
                             class="btn btn-default"
                             @click="$emit('close')">
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
    import Select2 from "../forms/Select2";
    import VueDatePicker from '@vuepic/vue-datepicker';
    import '@vuepic/vue-datepicker/dist/main.css';

    export default {
        components:{
            Editor,
            Select2,
            VueDatePicker
        },
        props: {
            show: {
                type: Boolean
            },
            params: {
                type: Object
            },  //{ 'modelId': curriculum.id, 'modelUrl': 'curriculum' , 'shareWithToken': true, 'canEditCheckbox': false}
        },
        data() {
            return {
                component_id: this.$.uid,
                method: 'post',
                url: '/periods',
                form: new Form({
                    'id':'',
                    'title': '',
                    'date': null,
                    'begin': new Date(),
                    'end': null,
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
                search: '',
            }
        },
        watch: {
            params: function(newVal, oldVal) {
                this.form.reset();
                this.form.populate(newVal);
                this.form.date = [this.form.begin, this.form.end];
                if (this.form.id != ''){
                    this.method = 'patch';
                } else {
                    this.method = 'post';
                }
            },
        },
        methods: {
             submit(method) {
                 this.form.begin = this.form.date[0];
                 this.form.end = this.form.date[1];

                 if (method == 'patch') {
                     this.update();
                 } else {
                     this.add();
                 }
            },
            add(){
                 console.log('add');
                axios.post(this.url, this.form)
                    .then(r => {
                        this.$eventHub.emit('period-added', r.data);
                    })
                    .catch(e => {
                        console.log(e.response);
                    });
            },
            update() {
                console.log('update');
                axios.patch(this.url + '/' + this.form.id, this.form)
                    .then(r => {
                        this.$eventHub.emit('period-updated', r.data);
                    })
                    .catch(e => {
                        console.log(e.response);
                    });
            }
        },
        mounted() {
            const startDate = new Date();
            const endDate = new Date(new Date().setDate(startDate.getDate() + 7));
            this.form.date = [startDate, endDate];
        },
    }
</script>

