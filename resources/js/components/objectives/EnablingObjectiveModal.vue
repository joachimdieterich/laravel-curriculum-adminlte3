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
                            {{ trans('global.enablingObjective.create') }}
                        </span>
                        <span v-if="method === 'patch'">
                            {{ trans('global.enablingObjective.edit') }}
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
                            class="card-header"
                            data-card-widget="collapse"
                        >
                            <div class="card-title">{{ trans('global.general') }}</div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="description">
                                    {{ trans('global.title') }} *
                                </label>
                                <Editor
                                    id="title"
                                    name="title"
                                    class="form-control"
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
                                    :init="tinyMCE_description"
                                    v-model="form.description"
                                />
                            </div>
        
                            <Select2
                                id="level_id"
                                name="level_id"
                                url="/levels"
                                model="level"
                                :selected="this.form.level_id"
                                @selectedValue="(id) => {
                                    this.form.level_id = id[0];
                                }"
                            />
        
                            <div>
                                <label for="time_approach">{{ trans('global.enablingObjective.fields.time_approach') }}</label>
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
                            <div class="card-title">{{ trans('global.permissions') }}</div>
                        </div>
                        <div class="card-body">
                            <div>
                                <span class="custom-control custom-switch custom-switch-on-green">
                                    <input
                                        :id="'visibility_' + form.id"
                                        type="checkbox"
                                        class="custom-control-input pt-1 pointer"
                                        v-model="form.visibility"
                                    />
                                    <label
                                        class="custom-control-label text-muted pointer"
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
                            id="enablingObjective-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="enablingObjective-save"
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
import axios from "axios";
import Editor from "@tinymce/tinymce-vue";
import Select2 from "../forms/Select2.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'enabling-objective-modal',
    components: {
        Editor,
        Select2,
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
            form: new Form({
                id: '',
                title: '',
                description: '',
                time_approach: '',
                curriculum_id: '',
                terminal_objective_id: '',
                level_id: null,
                visibility: true,
            }),
            tinyMCE_title: this.$initTinyMCE(
                [
                    "autolink link table lists autoresize"
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
                    "autolink link table lists autoresize"
                ],
                {
                    public: 1,
                    subscribeSelected: true,
                    subscribable_id: this.form?.curriculum_id,
                    subscribable_type: 'App\\Curriculum',
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
        submit() {
            if (this.method == 'patch') {
                this.update();
            } else {
                this.add();
            }

            this.globalStore.closeModal(this.$options.name);
        },
        add() {
            axios.post('/enablingObjectives', this.form)
                .then(r => {
                    this.$eventHub.emit('enabling-objective-added', r.data);
                })
                .catch(e => {
                    console.log(e);
                });
        },
        update() {
            axios.patch('/enablingObjectives/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('enabling-objective-updated', r.data);
                })
                .catch(e => {
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