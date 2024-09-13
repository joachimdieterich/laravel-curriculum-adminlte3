<template>
    <Transition name="modal">
        <div v-if="show"
             class="modal-mask"
        >
        <div class="modal-container">
            <div class="card-header">
                <h3 class="card-title">
                    <span v-if="method === 'post'">
                        {{ trans('global.curriculum.create') }}
                    </span>
                    <span v-if="method === 'patch'">
                        {{ trans('global.curriculum.edit') }}
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
                        :class="form.errors.title ? 'has-error' : ''">
                        <label for="title">
                            {{ trans('global.curriculum.fields.title') }}
                        </label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            class="form-control float"
                            v-model="form.title"
                            placeholder="Title"
                            required
                            />
                         <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                    </div>
                    <div class="form-group">
                        <label for="description">
                            {{ trans('global.curriculum.fields.description') }}
                        </label>
                        <Editor
                            id="description"
                            name="description"
                            :placeholder="trans('global.curriculum.fields.description')"
                            class="form-control"
                            :init="tinyMCE"
                            :initial-value="form.description"
                        ></Editor>
                    </div>
                    <div class="form-group ">
                        <label for="author">
                            {{ trans('global.curriculum.fields.author') }}
                        </label>
                        <input
                            type="text"
                            id="author"
                            name="author"
                            class="form-control"
                            required
                            v-model.trim="form.author"
                        />
                        <p class="help-block" v-if="form.errors?.author" v-text="form.errors?.author[0]"></p>
                    </div>
                    <div class="form-group ">
                        <label for="publisher">
                            {{ trans('global.curriculum.fields.publisher') }}
                        </label>
                        <input
                            type="text"
                            id="publisher"
                            name="publisher"
                            class="form-control"
                            required
                            v-model.trim="form.publisher"
                        />
                        <p class="help-block" v-if="form.errors?.publisher" v-text="form.errors?.publisher[0]"></p>
                    </div>

                    <div class="form-group ">
                        <label for="city">
                            {{ trans('global.curriculum.fields.city') }}
                        </label>
                        <input
                            type="text"
                            id="city"
                            name="city"
                            class="form-control"
                            required
                            v-model.trim="form.city"
                        />
                        <p class="help-block" v-if="form.errors?.city" v-text="form.errors?.city[0]"></p>
                    </div>

                    <div class="form-group pt-2">
                        <label for="author">
                            {{ trans('global.curriculum.fields.date') }}
                        </label>
                        <VueDatePicker
                            v-model="form.date"
                            format="dd.MM.yyy HH:mm"
                            :teleport="true"
                            locale="de"
                            :select-text="trans('global.ok')"
                            :cancel-text="trans('global.close')"
                        ></VueDatePicker>
                    </div>

                    <div class="form-group ">
                        <Select2
                            :id="'grade_id'"
                            model="grade"
                            :selected="this.form.grade_id"
                            url="/grades"
                            :placeholder="trans('global.pleaseSelect')"
                            @selectedValue="(id) => this.form.grade_id = id"
                        ></Select2>
                    </div>

                    <div class="form-group ">
                        <Select2
                            :id="'subject_id'"
                            model="subject"
                            :selected="this.form.subject_id"
                            url="/subjects"
                            :placeholder="trans('global.pleaseSelect')"
                            @selectedValue="(id) => this.form.subject_id = id"
                        ></Select2>
                    </div>
                    <!--                variants-->
                    <div class="form-group ">
                        <Select2
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
                    </div>
                    <div class="form-group ">
                        <Select2
                            id="type_id"
                            name="type_id"
                            url="/curriculumTypes"
                            model="curriculumtype"
                            option_id="id"
                            option_label="title"
                            :selected="this.form.type_id"
                            @selectedValue="(id) => {
                        this.form.type_id = id;
                    }"
                        >
                        </Select2>
                    </div>
                    <Select2
                        id="country_id"
                        name="country_id"
                        option_id="alpha2"
                        option_label="lang_de"
                        url="/countries"
                        model="country"
                        :selected="this.form.country_id"
                        @selectedValue="(id) => {
                        this.form.country_id = id;
                        this.form.state_id = '';
                    }"
                    >
                    </Select2>

                    <Select2
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
                </div>

            <div class="card-body pb-0">
                <v-swatches
                    :swatch-size="49"
                    :trigger-style="{}"
                    popover-to="right"
                    v-model="this.form.color"
                    @input="(id) => {
                        this.form.color = id;
                    }"
                    :max-height="300"
                ></v-swatches>

                <MediumForm
                    class="pull-right"
                    id="medium_id"
                    :medium_id="form.medium_id"
                    accept="image/*"
                    :selected="this.form.medium_id"
                    @selectedValue="(id) => {
                        this.form.medium_id = id;
                    }"
                >
                </MediumForm>
            </div>

            <div class="card-footer">
                 <span class="pull-right">
                     <button
                         id="curriculum-cancel"
                         type="button"
                         class="btn btn-default"
                         @click="$emit('close')">
                         {{ trans('global.cancel') }}
                     </button>
                     <button
                         id="curriculum-save"
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
    import MediumModal from "../media/MediumModal.vue";
    import MediumForm from "../media/MediumForm.vue";
    import Select2 from "../forms/Select2.vue";
    import VueDatePicker from '@vuepic/vue-datepicker';
    import axios from "axios";


    export default {
        components:{
            Editor,
            MediumModal,
            MediumForm,
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
                url: '/curricula',
                form: new Form({
                    'id':'',
                    'title': '',
                    'description': '',
                    'author': '',
                    'publisher': '',
                    'city': '',
                    'date': '',
                    'color': '#F2C511',
                    'grade_id': 1,
                    'subject_id': 1,
                    'organization_type_id': 1,
                    'state_id': 'DE-RP',
                    'country_id': 'DE',
                    'medium_id': null,
                    'owner_id': '',
                    'type_id': 4,
                }),
                tinyMCE: this.$initTinyMCE(
                    [
                        "autolink link curriculummedia table lists"
                    ],
                    {
                        'eventHubCallbackFunction': 'insertContent',
                        'eventHubCallbackFunctionParams': this.component_id,
                    }
                ),
                search: '',
            }
        },
        computed:{
            textColor: function(){
                return this.$textcolor(this.form.color, '#333333');
            }
        },
        watch: {
            params: function(newVal, oldVal) {
                this.form.reset();
                this.form.populate(newVal);
                this.form.description = this.$decodeHTMLEntities(newVal.description);
                if (this.form.id != ''){
                    this.method = 'patch';
                } else {
                    this.method = 'post';
                }
            },
        },
        methods: {
            submit(method) {
                this.form.description = tinyMCE.get('description').getContent();
                if (method == 'patch') {
                    this.update();
                } else {
                    this.add();
                }
            },
            add(){
                axios.post(this.url, this.form)
                    .then(r => {
                        this.$eventHub.emit('curriculum-added', r.data);
                    })
                    .catch(e => {
                        console.log(e.response);
                    });
            },
            update() {
                axios.patch(this.url + '/' + this.form.id, this.form)
                    .then(r => {
                        this.$eventHub.emit('curriculum-updated', r.data);
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error);
                    });

            }
        },
        mounted() {

        },
    }
</script>

