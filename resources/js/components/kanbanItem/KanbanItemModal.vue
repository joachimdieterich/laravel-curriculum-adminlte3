<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
        >
            <div class="modal-container">
                <div class="modal-header">
                    <span class="card-title">
                        {{ method == 'post' ? trans('global.kanbanItem.create') : trans('global.kanbanItem.edit') }}
                    </span>
                    <button
                        type="button"
                        class="btn btn-icon text-secondary"
                        :title="trans('global.close')"
                        @click="close()"
                    >
                        <i class="fa fa-times"></i>
                    </button>
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
                                licenseKey="gpl"
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
                            <span class="card-title">{{ trans('global.display') }}</span>
                        </div>
                        <div class="card-body d-flex align-items-center">
                            <v-swatches
                                class="d-flex"
                                style="height: 42px;"
                                :swatches="$swatches"
                                row-length="5"
                                popover-y="top"
                                v-model="form.color"
                                show-fallback
                                fallback-input-type="color"
                            />

                            <NewMediumForm
                                :subscribable_id="form.id"
                                :subscribable_type="'App\\KanbanItem'"
                                :allow_fallback_on_create="true"
                                :media_subscriptions="form.media_subscriptions"
                                :multiple="true"
                                @add="(subscription) => {
                                    this.form.media_subscriptions.push(...subscription);
                                }"
                            />
                        </div>
                    </div>

                    <div v-if="hasPermissionsAccess"
                        class="card"
                    >
                        <div
                            class="card-header border-bottom"
                            data-card-widget="collapse"
                        >
                            <span class="card-title">{{ trans('global.permissions') }}</span>
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
                                    time-picker-inline
                                    :start-time="{ hours: 23, minutes: 59 }"
                                    @cleared="form.due_date = ''"
                                    :select-text="trans('global.ok')"
                                    :cancel-text="trans('global.close')"
                                    :placeholder="trans('global.kanbanItem.fields.due_date')"
                                />
                            </div>

                            <span class="custom-control custom-switch custom-switch-on-green">
                                <input
                                    :id="'replace_links_' + form.id"
                                    class="custom-control-input pt-1"
                                    type="checkbox"
                                    v-model="form.replace_links"
                                />
                                <label
                                    class="custom-control-label font-weight-light pointer"
                                    :for="'replace_links_' + form.id"
                                >
                                    {{ trans('global.replace_links') }}
                                </label>
                            </span>
                            <span class="custom-control custom-switch custom-switch-on-green">
                                <input
                                    :id="'movable_' + form.id"
                                    class="custom-control-input pt-1"
                                    type="checkbox"
                                    v-model="form.movable"
                                />
                                <label
                                    class="custom-control-label font-weight-light pointer"
                                    :for="'movable_' + form.id"
                                >
                                    {{ trans('global.movable') }}
                                </label>
                            </span>
                            <span class="custom-control custom-switch custom-switch-on-green">
                                <input
                                    :id="'editable_' + form.id"
                                    class="custom-control-input pt-1"
                                    type="checkbox"
                                    v-model="form.editable"
                                />
                                <label
                                    class="custom-control-label font-weight-light pointer"
                                    :for="'editable_' + form.id"
                                >
                                    {{ trans('global.editable') }}
                                </label>
                            </span>
                            <span class="custom-control custom-switch custom-switch-on-green">
                                <input
                                    :id="'visibility_' + form.id"
                                    class="custom-control-input pt-1"
                                    type="checkbox"
                                    v-model="form.visibility"
                                />
                                <label
                                    class="custom-control-label font-weight-light pointer"
                                    :for="'visibility_' + form.id"
                                >
                                    {{ trans('global.visible') }}
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
                                time-picker-inline
                                :start-time="[{ hours: 0, minutes: 0 }, { hours: 23, minutes: 59 }]"
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
                            @click="close()"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="kanban-item-save"
                            class="btn btn-primary ml-3"
                            :disabled="!form.title || processing"
                            @click="submit()"
                        >
                            <span v-if="processing"><i class="fa fa-spinner fa-pulse fa-fw"></i></span>
                            <span v-else>{{ trans('global.save') }}</span>
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </Transition>
</template>
<script>
import NewMediumForm from '../media/NewMediumForm.vue';
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
            medium: null,
            processing: false,
            form: new Form({
                id: null,
                title: '',
                description: '',
                kanban_id: '',
                kanban_status_id: '',
                order_id: 0,
                owner_id: null,
                color: '#f4f4f4',
                media_subscriptions: [],
                due_date: '',
                movable: true, // replaces 'locked' to match shown translation
                locked: false, // actual value that gets sent to backend
                editable: true,
                replace_links: false,
                visibility: true,
                visible_date: null,
                visible_from: '',
                visible_until: '',
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink", "link", "lists", "table", "code", "autoresize",
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
            if (state.modals[this.$options.name].show && !state.modals[this.$options.name].lock) {
                this.globalStore.lockModal(this.$options.name);
                this.processing = false;
                this.form.reset();
                
                const params = state.modals[this.$options.name].params;
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params.item);
                    this.method = params.method;
                    this.form.movable = !this.form.locked;

                    if (this.form.media_subscriptions.length > 0) this.medium = this.form.media_subscriptions[0].medium;
                    else this.medium = null; // needs to be reset

                    if (this.form.visible_from == null && this.form.visible_until != null) {
                        this.form.visible_date = [this.form.visible_until, null]; // second date needs to be null
                    } else {
                        // unset dates need to be set to empty strings, becuase null will show 1970-01-01T00:00:00.000Z
                        this.form.visible_date = [this.form.visible_from ?? '', this.form.visible_until ?? ''];
                    }
                }
            }
        });
    },
    methods: {
        close() {
            this.globalStore.closeModal(this.$options.name);
        },
        submit() {
            this.form.locked = !this.form.movable;
            // parse dates to local time, so the server won't have to deal with timezones
            this.form.due_date = this.form.due_date?.toLocaleString() ?? null; // undefined will remove the field from the request
            if (this.form.visible_date[1] === null) {
                this.form.visible_until = this.form.visible_date[0].toLocaleString();
            } else {
                this.form.visible_from = this.form.visible_date[0].toLocaleString();
                this.form.visible_until = this.form.visible_date[1].toLocaleString();
            }

            this.processing = true;

            if (this.method == 'patch') {
                this.update();
            } else {
                this.form.media_subscriptions = this.form.media_subscriptions.map(m => m.medium_id);
                this.add();
            }
        },
        add() {
            axios.post('/kanbanItems', this.form)
                .then(r => {
                    this.$eventHub.emit('kanban-item-added-' + this.form.kanban_status_id, r.data);
                    this.close()
                })
                .catch(e => {
                    this.toast.error(this.errorMessage(e));
                    console.log(e);
                });
        },
        update() {
            axios.patch('/kanbanItems/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('kanban-item-updated-' + r.data.kanban_status_id, r.data);
                    this.close();
                })
                .catch(e => {
                    console.log(e);
                    this.toast.error(this.errorMessage(e));
                });
        },
    },
    computed: {
        hasPermissionsAccess() {
            return this.method == 'post'
                || this.form.owner_id == this.$userId
                || this.$parent.kanban.owner_id == this.$userId
                || this.checkPermission('is_admin');
        },
    },
    components: {
        VueDatePicker,
        Editor,
        NewMediumForm,
    },
}
</script>