<template>
    <Modal
        ref="modal"
        model="curriculum"
        modalName="curriculum-modal"
        :method="method"
        :processing="processing"
        :disable-save-button="!form.title"
        @save="submit(form)"
    >
        <template #modal-body>
            <div class="modal-body accordion">
                <div v-if="method === 'post'"
                    class="nav nav-pills justify-content-center gap-2 border-bottom p-3"
                    role="tablist"
                >
                    <!-- Create -->
                    <li
                        class="nav-item"
                        role="presentation"
                    >
                        <button
                            id="create-curriculum-tab"
                            class="nav-link active"
                            type="button"
                            role="tab"
                            aria-controls="create-curriculum"
                            aria-selected="true"
                            data-bs-toggle="pill"
                            data-bs-target="#create-curriculum"
                        >
                            {{ trans('global.curriculum.create') }}
                        </button>
                    </li>
                    <!-- Import -->
                    <li
                        class="nav-item"
                        role="presentation"
                    >
                        <button
                            id="import-curriculum-tab"
                            class="nav-link"
                            type="button"
                            role="tab"
                            aria-controls="import-curriculum"
                            aria-selected="false"
                            data-bs-toggle="pill"
                            data-bs-target="#import-curriculum"
                        >
                            {{ trans('global.curriculum.import') }}
                        </button>
                    </li>
                </div>

                <div class="tab-content">
                    <div
                        id="create-curriculum"
                        class="tab-pane fade active show"
                        role="tabpanel"
                        aria-labelledby="create-curriculum-tab"
                        tabindex="0"
                    >
                        <div class="accordion-item">
                            <div class="accordion-header">
                                <span
                                    class="accordion-button"
                                    aria-expanded="true"
                                    aria-controls="curriculum-general"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#curriculum-general"
                                >
                                    {{ trans('global.general') }}
                                </span>
                            </div>
                            <div
                                id="curriculum-general"
                                class="accordion-collapse collapse show"
                            >
                                <div>
                                    <input
                                        id="title"
                                        name="title"
                                        type="text"
                                        class="form-control mb-3"
                                        maxlength="191"
                                        v-model.trim="form.title"
                                        :placeholder="trans('global.title') + ' *'"
                                        required
                                    />
                                    <div class="mb-3">
                                        <Editor
                                            id="description"
                                            name="description"
                                            licenseKey="gpl"
                                            :init="tinyMCE"
                                            v-model="form.description"
                                        />
                                    </div>
                                    <Select2 v-if="checkPermission('is_admin')"
                                        id="user_id"
                                        css="mb-3"
                                        :label="trans('global.change_owner')"
                                        model="User"
                                        url="/users"
                                        :selected="form.owner_id"
                                        @selectedValue="(id) => form.owner_id = id[0]"
                                    />
                                    <div class="mb-3">
                                        <TagMultiselect
                                            type="App\Curriculum"
                                            :model-id="form.id"
                                            :selectedTags="selectedTags"
                                            @selectedValue="(data) => form.tags = data"
                                            @cleared="() => form.tags = []"
                                            @tag-attached="(tag) => updateSelectedTags(tag.id)"
                                        />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="author">{{ trans('global.curriculum.fields.author') }}</label>
                                        <input
                                            id="author"
                                            name="author"
                                            type="text"
                                            class="form-control"
                                            maxlength="191"
                                            v-model.trim="form.author"
                                        />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="publisher">{{ trans('global.curriculum.fields.publisher') }}</label>
                                        <input
                                            id="publisher"
                                            name="publisher"
                                            type="text"
                                            class="form-control"
                                            maxlength="191"
                                            v-model.trim="form.publisher"
                                        />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="city">{{ trans('global.curriculum.fields.city') }}</label>
                                        <input
                                            id="city"
                                            name="city"
                                            type="text"
                                            class="form-control"
                                            maxlength="191"
                                            v-model.trim="form.city"
                                        />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="author">{{ trans('global.curriculum.fields.date') }}</label>
                                        <VueDatePicker
                                            v-model="form.date"
                                            format="dd.MM.yyy HH:mm"
                                            :teleport="true"
                                            locale="de"
                                            :select-text="trans('global.ok')"
                                            :cancel-text="trans('global.close')"
                                        />
                                    </div>
                                    <Select2
                                        :id="'grade_id'"
                                        css="mb-3"
                                        model="grade"
                                        url="/grades"
                                        :label="trans('global.grade.title_singular') + ' *'"
                                        :selected="form.grade_id"
                                        @selectedValue="(id) => form.grade_id = id"
                                    />
                                    <Select2
                                        :id="'subject_id'"
                                        css="mb-3"
                                        model="subject"
                                        url="/subjects"
                                        :label="trans('global.subject.title_singular') + ' *'"
                                        :selected="form.subject_id"
                                        @selectedValue="(id) => form.subject_id = id"
                                    />
                                    <Select2
                                        id="organization_type_id"
                                        css="mb-3"
                                        model="organizationType"
                                        url="/organizationTypes"
                                        :label="trans('global.organizationType.title_singular') + ' *'"
                                        :selected="form.organization_type_id"
                                        @selectedValue="(id) => form.organization_type_id = id"
                                    />
                                    <Select2 v-if="checkPermission('is_admin')"
                                        id="type_id"
                                        css="mb-3"
                                        model="curriculumtype"
                                        url="/curriculumTypes"
                                        :label="trans('global.curriculumtype.title_singular') + ' *'"
                                        :selected="form.type_id"
                                        @selectedValue="(id) => form.type_id = id"
                                    />
                                    <Select2
                                        id="country_id"
                                        css="mb-3"
                                        option_id="alpha2"
                                        option_label="lang_de"
                                        model="country"
                                        url="/countries"
                                        :label="trans('global.country.title_singular') + ' *'"
                                        :selected="form.country_id"
                                        @selectedValue="(id) => {
                                            form.country_id = id;
                                            form.state_id = null;
                                        }"
                                    />
                                    <Select2
                                        id="state_id"
                                        option_id="code"
                                        option_label="lang_de"
                                        model="state"
                                        :url="'/countries/' + form.country_id + '/states'"
                                        :term="form.country_id"
                                        :selected="form.state_id"
                                        @selectedValue="(id) => form.state_id = id"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div
                                class="card-header border-bottom"
                                data-card-widget="collapse"
                            >
                                <span class="card-title">{{ trans('global.display') }}</span>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <v-swatches
                                        style="height: 42px;"
                                        :swatches="$swatches"
                                        row-length="5"
                                        popover-y="top"
                                        v-model="form.color"
                                        show-fallback
                                        fallback-input-type="color"
                                    />

                                    <MediumForm v-if="form.id"
                                        :id="'medium_form' + component_id"
                                        :medium_id="form.medium_id"
                                        accept="image/*"
                                        :subscribable_id="form.id"
                                        subscribable_type="App\Curriculum"
                                        @selectedValue="(id) => {
                                            // on removal of medium, directly update the resource
                                            if (this.form.medium_id !== null && id === null) {
                                                this.$eventHub.emit('curriculum-updated', {
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
                                <span class="card-title">{{ trans('global.settings') }}</span>
                            </div>
                            <div class="card-body">
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
                        </div>
                    </div>

                    <div
                        id="import-curriculum"
                        class="tab-pane fade"
                        role="tabpanel"
                        aria-labelledby="import-curriculum-tab"
                        tabindex="0"
                    >
                        <div class="d-flex justify-content-center align-items-center p-3">
                            <label for="imports" class="me-3 mb-0">{{ trans('global.file') }}</label>
                            <input
                                id="imports"
                                name="imports"
                                type="file"
                                class="form-control"
                                multiple
                                @change="onChange($event)"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Editor from '@tinymce/tinymce-vue';
import MediumForm from "../media/MediumForm.vue";
import Select2 from "../forms/Select2.vue";
import TagMultiselect from "../tag/TagMultiselect.vue";
import VueDatePicker from '@vuepic/vue-datepicker';
import {useGlobalStore} from "../../store/global";
import {useToast} from "vue-toastification";

export default {
    name: 'curriculum-modal',
    components: {
        Modal,
        TagMultiselect,
        Editor,
        MediumForm,
        Select2,
        VueDatePicker,
    },
    props: {},
    setup() {
        const globalStore = useGlobalStore();
        const toast = useToast();
        return {
            globalStore,
            toast,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            processing: false,
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
                tags: [],
            }),
            selectedTags: [],
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink", "link", "lists", "autoresize",
                ],
                {
                    callbackId: this.component_id,
                },
                "bold underline italic | alignleft aligncenter alignright alignjustify | bullist numlist | link",
                ""
            ),
        }
    },
    computed: {
        textColor: function() {
            return this.$textcolor(this.form.color, '#333333');
        }
    },
    methods: {
        submit(formData) {
            this.form.populate(formData);
            this.processing = true;

            if (this.method === 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            axios.post('/curricula', this.form)
                .then(r => {
                    this.$eventHub.emit('curriculum-added', r.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.processing = false;
                    this.toast.error(this.errorMessage(e));
                    console.log(e.response);
                });
        },
        update() {
            axios.patch('/curricula/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('curriculum-updated', r.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(error => {
                    this.processing = false;
                    this.toast.error(this.errorMessage(e));
                    console.log(error);
                });
        },
        onChange($events) {
            this.files = $events.target.files;

            axios.post('curricula/import/store', this.files)
                .then(r => {
                    this.$eventHub.emit('curriculum-imported', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        getSelectedTags(tags) {
            if (tags && tags[0] && tags[0]?.name){
                return tags.map(p => p.id);
            }

            return tags;
        },
        updateSelectedTags(newTag) {
            if (newTag !== undefined) {
                this.form.tags.push(newTag)
            }

            this.selectedTags = this.getSelectedTags(this.form.tags);
        }
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show && !state.modals[this.$options.name].lock) {
                this.globalStore.lockModal(this.$options.name);
                this.processing = false;
                this.form.reset();
                
                const params = state.modals[this.$options.name].params;
                if (typeof (params) !== 'undefined') {
                    params.tags = this.getSelectedTags(params.tags);
                    this.form.populate(params);
                    this.updateSelectedTags();
                    this.method = this.form.id ? 'patch' : 'post';
                }

                this.$refs.modal.resetForm(this.form);
            }
        });
    },
}
</script>