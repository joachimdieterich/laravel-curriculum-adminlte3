<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        <span v-if="method === 'post'">
                            {{ trans('global.kanbanItem.create') }}
                        </span>
                        <span v-if="method === 'patch'">
                            {{ trans('global.kanbanItem.edit') }}
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

                <div
                    class="card-body"
                    style="max-height: 80vh; overflow-y: auto;"
                >
                    <div class="form-group">
                        <input
                            id="title"
                            name="title"
                            type="text"
                            class="form-control"
                            v-model.trim="form.title"
                            :placeholder="trans('global.kanbanItem.fields.title') + ' *'"
                            required
                        />
                        <p v-if="form.errors.title"
                            class="help-block"
                            v-text="form.errors.title[0]"
                        ></p>
                    </div>
                    <div class="form-group">
                        <Editor
                            :id="'description_' + component_id"
                            :name="'description_' + component_id"
                            class="form-control"
                            :init="tinyMCE"
                            v-model="form.description"
                        />
                    </div>
                    <div
                        class="card-header border-bottom"
                        data-card-widget="collapse"
                    >
                        <h5 class="card-title">
                            Darstellung
                        </h5>
                    </div>
                    <!-- /.card-header -->
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
                    </div>
                    <!-- PERMISSIONS -->
                    <div
                        class="card-header border-bottom"
                        data-card-widget="collapse"
                    >
                        <h5 class="card-title">
                            {{ trans('global.permissions') }}
                        </h5>
                    </div>
                    <div class="form-group">
                        <VueDatePicker
                            id="due_date"
                            name="due_date"
                            class="my-2"
                            v-model="form.due_date"
                            format="dd.MM.yyyy"
                            :teleport="true"
                            locale="de"
                            @cleared="form.due_date = ''"
                            :select-text="trans('global.ok')"
                            :cancel-text="trans('global.close')"
                            :placeholder="trans('global.kanbanItem.fields.due_date')"
                        ></VueDatePicker>

                        <span class="custom-control custom-switch custom-switch-on-green">
                            <input
                                :id="'locked_'+ form.id"
                                class="custom-control-input pt-1"
                                type="checkbox"
                                v-model="form.locked"
                            />
                            <label
                                class="custom-control-label font-weight-light pointer"
                                :for="'locked_'+ form.id"
                            >
                                {{ trans('global.locked') }}
                            </label>
                        </span>
                        <span class="custom-control custom-switch custom-switch-on-green">
                            <input
                                :id="'editable_'+ form.id"
                                class="custom-control-input pt-1"
                                type="checkbox"
                                v-model="form.editable"
                            />
                            <label
                                class="custom-control-label font-weight-light pointer"
                                :for="'editable_'+ form.id"
                            >
                                {{ trans('global.editable') }}
                            </label>
                        </span>
                        <span class="custom-control custom-switch custom-switch-on-green">
                            <input
                                :id="'replace_links_'+ form.id"
                                class="custom-control-input pt-1"
                                type="checkbox"
                                v-model="form.replace_links"
                            />
                            <label 
                                class="custom-control-label font-weight-light pointer"
                                :for="'replace_links_'+ form.id"
                            >
                                {{ trans('global.replace_links') }}
                            </label>
                        </span>
                        <span class="custom-control custom-switch custom-switch-on-green">
                            <input
                                :id="'visibility_'+ form.id"
                                class="custom-control-input pt-1"
                                type="checkbox"
                                v-model="form.visibility"
                            />
                            <label
                                class="custom-control-label font-weight-light pointer"
                                :for="'visibility_'+ form.id"
                            >
                                {{ trans('global.visibility') }}
                            </label>
                        </span>
                        <VueDatePicker
                            v-if="form.visibility"
                            id="visible_date"
                            name="visible_date"
                            class="my-2"
                            v-model="form.visible_date"
                            :range="{ partialRange: false }"
                            format="dd.MM.yyyy HH:mm"
                            :teleport="true"
                            locale="de"
                            @cleared="form.visible_date = ['', '']"
                            :select-text="trans('global.ok')"
                            :cancel-text="trans('global.close')"
                            :placeholder="trans('global.kanbanItem.fields.visible_from_to')"
                        ></VueDatePicker>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="kanban-item-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="close()"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="kanban-item-save"
                            class="btn btn-primary"
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
import VueDatePicker from "@vuepic/vue-datepicker";
import Editor from '@tinymce/tinymce-vue';
import axios from "axios";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'kanban-item-modal',
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            url: '/kanbanItems',
            form: new Form({
                id: '',
                title: '',
                description: '',
                kanban_id: '',
                kanban_status_id: '',
                order_id: 0,
                color: '#f4f4f4',
                due_date: '',
                locked: false,
                editable: true,
                replace_links: false,
                visibility: true,
                visible_date: null,
                visible_from: '',
                visible_until: '',
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink link lists table code"
                ],
                {
                    'eventHubCallbackFunction': 'insertContent',
                    'eventHubCallbackFunctionParams': this.component_id,
                },
                "bold underline italic | alignleft aligncenter alignright | table",
                "bullist numlist outdent indent | mathjax link code",
            ),
        }
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {

            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params;
                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params.item);
                    this.form.visible_date = [this.form.visible_from ?? '', this.form.visible_until ?? ''];
                    this.method = params.method;
                }
            }
        });
    },
    methods: {
        close() {
            this.globalStore?.closeModal(this.$options.name);
        },
        submit() {
            this.form.visible_from = this.form.visible_date[0];
            this.form.visible_until = this.form.visible_date[1];
            
            if (this.method == 'patch') {
                this.update();
            } else {
                this.add();
            }
            this.close();
        },
        add() {
            axios.post(this.url, this.form)
                .then(r => {
                    this.$eventHub.emit('kanban-item-added', r.data);
                })
                .catch(e => {
                    console.log(e);
                });
        },
        update() {
            axios.patch(this.url + '/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('kanban-item-updated', r.data);
                })
                .catch(e => {
                    console.log(e);
                });
        },
    },
    components: {
        VueDatePicker,
        Editor,
    },
}
</script>