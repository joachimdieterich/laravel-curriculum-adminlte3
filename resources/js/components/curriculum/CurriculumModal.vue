<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @click.self="globalStore.closeModal($options.name)"
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
                    <ul v-if="method === 'post'"
                        class="nav nav-pills"
                    >
                        <!-- Create -->
                        <li class="nav-item">
                            <a
                                href="#create_curriculum"
                                class="nav-link active show"
                                data-toggle="tab"
                            >
                                {{ trans('global.curriculum.create') }}
                            </a>
                        </li>
                        <!-- Import -->
                        <li class="nav-item">
                            <a
                                href="#import_curriculum"
                                class="nav-link"
                                data-toggle="tab"
                            >
                                {{ trans('global.curriculum.import') }}
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content pt-2">
                        <div
                            id="create_curriculum"
                            class="tab-pane active show"
                        >
                            <div
                                class="form-group"
                                :class="form.errors.title ? 'has-error' : ''"
                            >
                                <input
                                    type="text"
                                    id="title"
                                    name="title"
                                    class="form-control float"
                                    v-model="form.title"
                                    :placeholder="trans('global.title') + ' *'"
                                    required
                                />
                                <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                            </div>
                            <div class="form-group">
                                <Editor
                                    id="description"
                                    name="description"
                                    :placeholder="trans('global.curriculum.fields.description')"
                                    class="form-control"
                                    :init="tinyMCE"
                                    v-model="form.description"
                                />
                            </div>
                            <div class="form-group">
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
                            <div class="form-group">
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

                            <div class="form-group">
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
                                />
                            </div>

                            <div class="form-group">
                                <Select2
                                    :id="'grade_id'"
                                    model="grade"
                                    :label="trans('global.grade.title_singular') + ' *'"
                                    :selected="this.form.grade_id"
                                    url="/grades"
                                    :placeholder="trans('global.pleaseSelect')"
                                    @selectedValue="(id) => this.form.grade_id = id"
                                />
                            </div>

                            <div class="form-group">
                                <Select2
                                    :id="'subject_id'"
                                    model="subject"
                                    :label="trans('global.subject.title_singular') + ' *'"
                                    :selected="this.form.subject_id"
                                    url="/subjects"
                                    :placeholder="trans('global.pleaseSelect')"
                                    @selectedValue="(id) => this.form.subject_id = id"
                                />
                            </div>
                            <!-- variants -->
                            <div class="form-group">
                                <Select2
                                    id="organization_type_id"
                                    name="organization_type_id"
                                    url="/organizationTypes"
                                    model="organizationType"
                                    :label="trans('global.organizationType.title_singular') + ' *'"
                                    option_id="id"
                                    option_label="title"
                                    :selected="this.form.organization_type_id"
                                    @selectedValue="(id) => {
                                        this.form.organization_type_id = id;
                                    }"
                                />
                            </div>
                            <div class="form-group">
                                <Select2
                                    id="type_id"
                                    name="type_id"
                                    url="/curriculumTypes"
                                    model="curriculumtype"
                                    :label="trans('global.curriculumtype.title_singular') + ' *'"
                                    option_id="id"
                                    option_label="title"
                                    :selected="this.form.type_id"
                                    @selectedValue="(id) => {
                                        this.form.type_id = id;
                                    }"
                                />
                            </div>
                            <Select2
                                id="country_id"
                                name="country_id"
                                option_id="alpha2"
                                option_label="lang_de"
                                url="/countries"
                                model="country"
                                :label="trans('global.country.title_singular') + ' *'"
                                :selected="this.form.country_id"
                                @selectedValue="(id) => {
                                    this.form.country_id = id;
                                    this.form.state_id = '';
                                }"
                            />

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
                            />
                            <div class="card-body pb-0">
                                <v-swatches
                                    :swatch-size="49"
                                    :trigger-style="{}"
                                    popover-to="right"
                                    v-model="this.form.color"
                                    show-fallback
                                    fallback-input-type="color"
                                    @input="(id) => {
                                        if (id.isInteger) {
                                            this.form.color = id;
                                        }
                                    }"
                                    :max-height="300"
                                />

                                <MediumForm
                                    class="pull-right"
                                    id="medium_id"
                                    :medium_id="form.medium_id"
                                    accept="image/*"
                                    :selected="this.form.medium_id"
                                    @selectedValue="(id) => {
                                        this.form.medium_id = id;
                                    }"
                                />
                            </div>
                            <span class="custom-control custom-switch custom-switch-on-green">
                                <input
                                    v-model="form.archived"
                                    type="checkbox"
                                    class="custom-control-input pt-1"
                                    :id="'archived_' + form.id"
                                />
                                <label
                                    class="custom-control-label text-muted"
                                    :for="'archived_' + form.id"
                                >
                                    {{ trans('global.curriculum.fields.archived') }}
                                </label>
                            </span>
                        </div>

                        <div
                            id="import_curriculum"
                            class="tab-pane"
                        >
                            <div class="form-group row">
                                <label for="imports" class="col-md-4 col-form-label text-md-right">
                                    {{ trans('global.file') }}
                                </label>
                                <div class="col-md-6 pt-6">
                                    <input
                                        id="imports"
                                        type="file"
                                        name="imports[]"
                                        @change="onChange($event)"
                                        class="form-control"
                                        multiple
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="curriculum-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="curriculum-save"
                            class="btn btn-primary ml-3"
                            @click="submit(method)"
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
import MediumModal from "../media/MediumModal.vue";
import MediumForm from "../media/MediumForm.vue";
import Select2 from "../forms/Select2.vue";
import VueDatePicker from '@vuepic/vue-datepicker';
import axios from "axios";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'curriculum-modal',
    components: {
        Editor,
        MediumModal,
        MediumForm,
        Select2,
        VueDatePicker,
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
            files: null,
            form: new Form({
                id:'',
                title: '',
                description: '',
                author: '',
                publisher: '',
                city: '',
                date: '',
                color: '#F2C511',
                grade_id: 1,
                subject_id: 1,
                organization_type_id: 1,
                state_id: 'DE-RP',
                country_id: 'DE',
                medium_id: null,
                owner_id: '',
                type_id: 4,
                archived: false,
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink link table lists autoresize"
                ],
                {
                    'callback': 'insertContent',
                    'callbackId': this.component_id
                }
            ),
        }
    },
    computed: {
        textColor: function() {
            return this.$textcolor(this.form.color, '#333333');
        }
    },
    methods: {
        submit(method) {
            if (method === 'patch') {
                this.update();
            } else {
                this.add();
            }

            this.globalStore.closeModal(this.$options.name);
        },
        add() {
            axios.post('/curricula', this.form)
                .then(r => {
                    this.$eventHub.emit('curriculum-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch('/curricula/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('curriculum-updated', r.data);
                })
                .catch(error => {
                    console.log(error);
                });
        },
        onChange($events) {
            this.files = $events.target.files;
            console.log(this.files);
            axios.post('curricula/import/store', this.files)
                .then(r => {
                    this.$eventHub.emit('curriculum-imported', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params;
                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                    if (this.form.id !== ''){
                        this.form.description = this.$decodeHTMLEntities(this.$decodeHtml(this.form.description));
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