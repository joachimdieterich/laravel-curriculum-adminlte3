<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
             class="modal-mask"
        >
        <div class="modal-container">
            <div class="card-header">
                <h3 class="card-title">
                    <span v-if="method === 'post'">
                        {{ trans('global.terminalObjective.create') }}
                    </span>
                    <span v-if="method === 'patch'">
                        {{ trans('global.terminalObjective.edit') }}
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
                <div class="form-group">
                    <label for="description">
                        {{ trans('global.terminalObjective.fields.title') }}
                    </label>
                    <Editor
                        id="title"
                        name="title"
                        :placeholder="trans('global.terminalObjective.fields.title')"
                        class="form-control"
                        :init="tinyMCE"
                        :initial-value="form.title"
                    ></Editor>
                </div>

                <div class="form-group">
                    <label for="description">
                        {{ trans('global.map.fields.description') }}
                    </label>
                    <Editor
                        id="description"
                        name="description"
                        :placeholder="trans('global.terminalObjective.fields.description')"
                        class="form-control"
                        :init="tinyMCE"
                        :initial-value="form.description"
                    ></Editor>
                </div>

                <Select2
                    id="objective_type_id"
                    name="objective_type_id"
                    url="/objectiveTypes"
                    model="objectiveType"
                    :selected="this.form.objective_type_id"
                    @selectedValue="(id) => {
                        this.form.objective_type_id = id;
                    }"
                >
                </Select2>

                <v-swatches
                    :swatch-size="49"
                    :trigger-style="{}"
                    popover-to="right"
                    v-model="this.form.color"
                    show-fallback
                    fallback-input-type="color"

                    @input="(id) => {
                                    if(id.isInteger){
                                      this.form.color = id;
                                    }
                                }"
                    :max-height="300"
                ></v-swatches>
                <div class="form-group ">
                    <label for="time_approach">{{ trans('global.terminalObjective.fields.time_approach') }}</label>
                    <input
                        type="text" id="time_approach"
                        name="title"
                        class="form-control"
                        v-model="form.time_approach"
                    />
                    <p class="help-block" v-if="form.errors.time_approach" v-text="form.errors.time_approach[0]"></p>
                </div>
                <div class="form-group ">
                    <span class="custom-control custom-switch custom-switch-on-green">
                        <input  v-model="form.visibility"
                                type="checkbox"
                                class="custom-control-input pt-1 "
                                :id="'visibility_' + form.id">
                        <label class="custom-control-label text-muted"
                               :for="'visibility_' + form.id" >
                            {{ trans('global.visibility') }}
                        </label>
                    </span>
                </div>
            </div>

            <div class="card-footer">
                 <span class="pull-right">
                     <button
                         id="terminalObjective-cancel"
                         type="button"
                         class="btn btn-default"
                         @click="globalStore?.closeModal($options.name)">
                         {{ trans('global.cancel') }}
                     </button>
                     <button
                         id="terminalObjective-save"
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
import MediumModal from "../media/MediumModal.vue";
import axios from "axios";
import Editor from "@tinymce/tinymce-vue";
import Select2 from "../forms/Select2.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'terminal-objective-modal',
    components: {
        Editor,
        MediumModal,
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
            url: '/terminalObjectives',
            form: new Form({
                'id': '',
                'title': '',
                'description': '',
                'color': '#008000',
                'time_approach': '',
                'curriculum_id': '',
                'objective_type_id': 1,
                'visibility': true,
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink link curriculummedia table lists"
                ],
                {
                    'public': 1,
                    'referenceable_type': 'App\\\Curriculum',
                    'referenceable_id': this.form?.curriculum_id,
                    'eventHubCallbackFunction': 'insertContent',
                    'eventHubCallbackFunctionParams': this.component_id
                }),
        }
    },
    computed: {
        textColor: function() {
            return this.$textcolor(this.form.color, '#333333');
        }
    },
    methods: {
        submit(method) {
            this.form.title = tinyMCE.get('title').getContent();
            this.form.description = tinyMCE.get('description').getContent();
            if (method == 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            axios.post(this.url, this.form)
                .then(r => {
                    this.$eventHub.emit('terminalObjective-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            console.log('update');
            axios.patch(this.url + '/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('terminalObjective-updated', r.data);
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

