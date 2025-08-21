<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
        >
            <div class="modal-container">
                <div class="modal-header">
                    <span class="card-title">
                        {{ method == 'post' ? trans('global.terminalObjective.create') : trans('global.terminalObjective.edit') }}
                    </span>
                    <button
                        type="button"
                        class="btn btn-icon text-secondary"
                        :title="trans('global.close')"
                        @click="globalStore?.closeModal($options.name)"
                    >
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="card">
                        <div
                            class="card-header border-bottom"
                            data-card-widget="collapse"
                        >
                            <span class="card-title">{{ trans('global.general') }}</span>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="title">
                                    {{ trans('global.terminalObjective.fields.title') }} *
                                </label>
                                <Editor
                                    id="title"
                                    name="title"
                                    class="form-control"
                                    licenseKey="gpl"
                                    :init="tinyMCE_title"
                                    v-model="form.title"
                                />
                            </div>
            
                            <div class="form-group">
                                <label for="description">
                                    {{ trans('global.map.fields.description') }}
                                </label>
                                <Editor
                                    id="description"
                                    name="description"
                                    class="form-control"
                                    licenseKey="gpl"
                                    :init="tinyMCE_description"
                                    v-model="form.description"
                                />
                            </div>
            
                            <Select2
                                id="objective_type_id"
                                name="objective_type_id"
                                url="/objectiveTypes"
                                model="objectiveType"
                                :label="trans('global.objectiveType.title_singular') + ' *'"
                                :selected="form.objective_type_id"
                                @selectedValue="(id) => {
                                    this.form.objective_type_id = id[0];
                                }"
                            />

                            <div>
                                <label for="time_approach">{{ trans('global.terminalObjective.fields.time_approach') }}</label>
                                <input
                                    id="time_approach"
                                    type="text"
                                    name="title"
                                    class="form-control"
                                    v-model="form.time_approach"
                                />
                                <p class="help-block" v-if="form.errors.time_approach" v-text="form.errors.time_approach[0]"></p>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div
                            class="card-header"
                            data-card-widget="collapse"
                        >
                            <span class="card-title">{{ trans('global.display') }}</span>
                        </div>
                        <div class="card-body">
                            <div class="d-flex">
                                <v-swatches
                                    style="height: 42px;"
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
                                />
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div
                            class="card-header"
                            data-card-widget="collapse"
                        >
                            <span class="card-title">{{ trans('global.permissions') }}</span>
                        </div>
                        <div class="card-body">
                            <div>
                                <span class="custom-control custom-switch custom-switch-on-green">
                                    <input
                                        :id="'visibility_' + form.id"
                                        type="checkbox"
                                        class="custom-control-input pt-1"
                                        v-model="form.visibility"
                                    >
                                    <label
                                        class="custom-control-label text-muted"
                                        :for="'visibility_' + form.id"
                                    >
                                        {{ trans('global.visibility') }}
                                    </label>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="terminalObjective-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="terminalObjective-save"
                            class="btn btn-primary ml-3"
                            :disabled="!form.title || !form.objective_type_id"
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
import axios from "axios";
import Editor from "@tinymce/tinymce-vue";
import Select2 from "../forms/Select2.vue";
import {useGlobalStore} from "../../store/global";
import {useToast} from "vue-toastification";

export default {
    name: 'terminal-objective-modal',
    components: {
        Editor,
        Select2,
    },
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
            form: new Form({
                id: '',
                title: '',
                description: '',
                color: '#008000',
                time_approach: '',
                curriculum_id: '',
                objective_type_id: 1,
                visibility: true,
            }),
            tinyMCE_title: this.$initTinyMCE(
                [
                    "autolink", "link", "table", "lists", "autoresize",
                ],
                {
                    public: 1,
                    subscribeSelected: true,
                    subscribable_id: this.form?.curriculum_id,
                    subscribable_type: 'App\\Curriculum',
                    callbackId: this.component_id,
                    placeholder: this.trans('global.objective_content'),
                },
                "bold underline italic | alignleft aligncenter alignright alignjustify",
                ""
            ),
            tinyMCE_description: this.$initTinyMCE(
                [
                    "autolink", "link", "table", "lists", "autoresize", "code", "fullscreen",
                ],
                {
                    public: 1,
                    subscribeSelected: true,
                    subscribable_id: this.form?.curriculum_id,
                    subscribable_type: 'App\\Curriculum',
                    callbackId: this.component_id,
                },
                "bold underline italic | alignleft aligncenter alignright alignjustify | bullist numlist | link code fullscreen",
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
        submit() {
            if (this.method == 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            axios.post('/terminalObjectives', this.form)
                .then(r => {
                    this.$eventHub.emit('terminal-objective-added', r.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.toast.error(this.form.description.length > 65535 ? this.trans('global.error.too_long') : this.trans('global.error.default'));
                    console.log(e);
                });
        },
        update() {
            axios.patch('/terminalObjectives/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('terminal-objective-updated', r.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.toast.error(this.form.description.length > 65535 ? this.trans('global.error.too_long') : this.trans('global.error.default'));
                    console.log(e);
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
                    this.form.title = this.$decodeHTMLEntities(this.form.title);
                    this.form.description = this.$decodeHtml(this.form.description);

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