<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
             class="modal-mask"
        >
            <div class="modal-container ">
                <div class="card-header ">
                    <h3 class="card-title">
                        <span v-if="method === 'post'">
                            {{ trans('global.kanban.create') }}
                        </span>
                        <span v-if="method === 'patch'">
                            {{ trans('global.kanban.edit') }}
                        </span>
                    </h3>
                    <div class="card-tools">
                        <button
                            type="button"
                            class="btn btn-tool"
                            @click="globalStore?.closeModal($options.name)">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <input
                            type="text"
                            id="title"
                            name="title"
                            class="form-control"
                            v-model.trim="form.title"
                            :placeholder="trans('global.kanbanItem.fields.title')"
                            required
                        />
                        <p class="help-block"
                        v-if="form.errors.title"
                        v-text="form.errors.title[0]"></p>
                    </div>

                    <div class="form-group">
                        <textarea
                            id="description"
                            name="description"
                            :placeholder="trans('global.kanbanItem.fields.description')"
                            class="form-control description "
                            v-model.trim="form.description"
                        ></textarea>
                        <p class="help-block"
                        v-if="form.errors.description"
                        v-text="form.errors.description[0]"></p>
                    </div>

                    <div
                        class="card-header border-bottom"
                        data-card-widget="collapse"
                    >
                        <h5 class="card-title">
                            Darstellung
                        </h5>
                    </div>

                    <div class="card-body pb-0">
                        <v-swatches
                            :swatch-size="49"
                            :trigger-style="{}"
                            popover-to="right"
                            v-model="this.form.color"
                            show-fallback
                            fallback-input-type="color"
                            @input="(id) => {
                                if(id.isInteger) {
                                    this.form.color = id;
                                }
                            }"
                            :max-height="300"
                        ></v-swatches>

                        <MediumForm
                            class="pull-right"
                            id="medium_id"
                            :form="form"
                            :medium_id="form.medium_id"
                            :subscribable_type="'App\\Kanban'"
                            :subscribable_id="form.id"
                            accept="image/*"
                            :selected="this.form.medium_id"
                            @selectedValue="(id) => {
                                this.form.medium_id = id;
                            }"
                        />
                    </div>

                    <div
                        class="card-header border-bottom"
                        data-card-widget="collapse"
                    >
                        <h5 class="card-title">
                            Berechtigung
                        </h5>
                    </div>

                    <div class="card-body pb-0">
                        <span class="custom-control custom-switch custom-switch-on-green">
                            <input
                                v-model="form.commentable"
                                type="checkbox"
                                class="custom-control-input pt-1"
                                :id="'commentable_' + form.id"
                            />
                            <label
                                class="custom-control-label text-muted"
                                :for="'commentable_' + form.id"
                            >
                                {{ trans('global.commentable') }}
                            </label>
                        </span>
                        <span class="custom-control custom-switch custom-switch-on-green">
                            <input
                                v-model="form.auto_refresh"
                                type="checkbox"
                                class="custom-control-input pt-1"
                                :id="'auto_refresh_' + form.id"
                            />
                            <label
                                class="custom-control-label text-muted"
                                :for="'auto_refresh_' + form.id"
                            >
                                {{ trans('global.auto_refresh') }}
                            </label>
                        </span>
                        <span class=" custom-control custom-switch custom-switch-on-green">
                            <input
                                v-model="form.only_edit_owned_items"
                                type="checkbox"
                                class="custom-control-input pt-1"
                                :id="'only_edit_owned_items_' + form.id"
                            />
                            <label
                                class="custom-control-label text-muted"
                                :for="'only_edit_owned_items_' + form.id"
                            >
                                {{ trans('global.kanban.only_edit_owned_items') }}
                            </label>
                        </span>
                        <span class="custom-control custom-switch custom-switch-on-green">
                            <input
                                v-model="form.allow_copy"
                                type="checkbox"
                                class="custom-control-input pt-1"
                                :id="'allow_copy_' + form.id"
                            />
                            <label
                                class="custom-control-label text-muted"
                                :for="'allow_copy_' + form.id"
                            >
                                {{ trans('global.kanban.allow_copy') }}
                            </label>
                        </span>
                    </div>
                </div>

                <div class="card-footer">
                     <span class="pull-right">
                         <button
                             id="kanban-cancel"
                             type="button"
                             class="btn btn-default"
                             @click="globalStore?.closeModal($options.name)">
                             {{ trans('global.cancel') }}
                         </button>
                         <button
                             id="kanban-save"
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
import MediumForm from "../media/MediumForm.vue";
import axios from "axios";
import Editor from "@tinymce/tinymce-vue";
import Select2 from "../forms/Select2.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'kanban-modal',
    components: {
        Editor,
        MediumModal,
        Select2,
        MediumForm
    },
    props: {
        params: {
            type: Object
        },  //{ 'modelId': curriculum.id, 'modelUrl': 'curriculum' , 'shareWithToken': true, 'canEditCheckbox': false}
    },
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
            url: '/kanbans',
            form: new Form({
                'id': '',
                'title':  '',
                'description':  '',
                'color':'#27AF60',
                'medium_id': null,
                'commentable': true,
                'auto_refresh': false,
                'only_edit_owned_items': false,
                'allow_copy': true,
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink link curriculummedia table lists autoresize"
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
            if (method == 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            axios.post(this.url, this.form)
                .then(r => {
                    this.$eventHub.emit('kanban-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            console.log('update');
            axios.patch(this.url + '/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('kanban-updated', r.data);
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
