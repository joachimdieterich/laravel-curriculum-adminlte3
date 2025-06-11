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

                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <input
                                    id="title"
                                    name="title"
                                    type="text"
                                    class="form-control"
                                    maxlength="191"
                                    v-model.trim="form.title"
                                    :placeholder="trans('global.kanbanItem.fields.title') + ' *'"
                                    required
                                />
                                <p v-if="form.errors.title"
                                    class="help-block"
                                    v-text="form.errors.title[0]"
                                ></p>
                            </div>

                            <Editor
                                :id="'description_' + component_id"
                                :name="'description_' + component_id"
                                class="form-control"
                                :init="tinyMCE"
                                v-model="form.description"
                            />
                        </div>
                    </div>

                    <div class="card">
                        <div
                            class="card-header border-bottom"
                            data-card-widget="collapse"
                        >
                            <div class="card-title">{{ trans('global.display') }}</div>
                        </div>
                        <div class="card-body">
                            <v-swatches
                                class="d-flex"
                                style="height: 42px;"
                                :swatches="$swatches"
                                row-length="5"
                                popover-y="top"
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

                    <div v-if="ownerOrAdmin"
                        class="card"
                    >
                        <div
                            class="card-header border-bottom"
                            data-card-widget="collapse"
                        >
                            <div class="card-title">{{ trans('global.permissions') }}</div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <VueDatePicker
                                    id="due_date"
                                    name="due_date"
                                    v-model="form.due_date"
                                    format="dd.MM.yyyy HH:mm"
                                    :teleport="true"
                                    locale="de"
                                    @cleared="form.due_date = ''"
                                    :select-text="trans('global.ok')"
                                    :cancel-text="trans('global.close')"
                                    :placeholder="trans('global.kanbanItem.fields.due_date')"
                                />
                            </div>

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

                            <VueDatePicker v-if="form.visibility"
                                id="visible_date"
                                name="visible_date"
                                class="mt-2"
                                v-model="form.visible_date"
                                range
                                format="dd.MM.yyyy HH:mm"
                                :teleport="true"
                                locale="de"
                                @cleared="form.visible_date = ['', '']"
                                :select-text="trans('global.ok')"
                                :cancel-text="trans('global.close')"
                                :placeholder="trans('global.visible_until_or_from_to')"
                            />
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="kanban-item-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name);"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="kanban-item-save"
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
import VueDatePicker from "@vuepic/vue-datepicker";
import Editor from '@tinymce/tinymce-vue';
import axios from "axios";
import {useGlobalStore} from "../../store/global";
import {useToast} from "vue-toastification";

export default {
    name: 'kanban-item-modal',
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            form: new Form({
                id: '',
                title: '',
                description: '',
                kanban_id: '',
                kanban_status_id: '',
                order_id: 0,
                owner_id: null,
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
                    "autolink link lists code curriculummedia autoresize table"
                ],
                {
                    'callback': 'insertContent',
                    'callbackId': this.component_id
                },
                "bold underline italic | alignleft aligncenter alignright alignjustify | table",
                "bullist numlist outdent indent | mathjax link code"
            ),
        }
    },
    setup() {
        const globalStore = useGlobalStore();
        const toast = useToast();
        return {
            globalStore,
            toast,
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
        submit() {
            // parse dates to local time, so the server won't have to deal with timezones
            this.form.due_date = this.form.due_date?.toLocaleString() ?? null; // undefined will remove the field from the request
            if (this.form.visible_date[1] === null) {
                this.form.visible_until = this.form.visible_date[0].toLocaleString();
            } else {
                this.form.visible_from = this.form.visible_date[0].toLocaleString();
                this.form.visible_until = this.form.visible_date[1].toLocaleString();
            }

            if (this.method == 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            axios.post('/kanbanItems', this.form)
                .then(r => {
                    this.$eventHub.emit('kanban-item-added', r.data);
                    this.globalStore?.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.toast.error(this.trans('global.error'));
                    console.log(e);
                });
        },
        update() {
            axios.patch('/kanbanItems/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('kanban-item-updated', r.data);
                    this.globalStore?.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.toast.error(this.trans('global.error'));
                    console.log(e);
                });
        },
    },
    computed: {
        ownerOrAdmin() {
            return this.form.owner_id == this.$userId
                || this.$parent.kanban.owner_id == this.$userId
                || this.checkPermission('is_admin');
        },
    },
    components: {
        VueDatePicker,
        Editor,
    },
}
</script>