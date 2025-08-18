<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @mouseup.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="modal-header">
                    <span class="card-title">
                        {{ method == 'post' ? trans('global.kanbanStatus.create') : trans('global.kanbanStatus.edit') }}
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

                <div
                    class="modal-body"
                    style="overflow-y: visible;"
                >
                    <div class="card">
                        <div class="card-body">
                            <div>
                                <input
                                    type="text"
                                    id="title"
                                    name="title"
                                    class="form-control"
                                    maxlength="191"
                                    v-model.trim="form.title"
                                    :placeholder="trans('global.kanbanItem.fields.title') + ' *'"
                                    required
                                />
                                <p
                                    v-if="form.errors.title"
                                    class="help-block"
                                    v-text="form.errors.title[0]"
                                ></p>
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
                            <v-swatches
                                class="d-flex"
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
                                :max-height="300"
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
                            <span class="custom-control custom-switch custom-switch-on-green">
                                <input
                                    :id="'editable_' + form.id"
                                    class="custom-control-input pt-1"
                                    type="checkbox"
                                    v-model="form.editable"
                                />
                                <label
                                    class="custom-control-label font-weight-light"
                                    :for="'editable_' + form.id"
                                >
                                    {{ trans('global.editable') }}
                                </label>
                            </span>
                            <span class="custom-control custom-switch custom-switch-on-green">
                                <input
                                    :id="'locked_' + form.id"
                                    class="custom-control-input pt-1"
                                    type="checkbox"
                                    v-model="form.locked"
                                />
                                <label
                                    class="custom-control-label font-weight-light"
                                    :for="'locked_' + form.id"
                                >
                                    {{ trans('global.locked') }}
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
                                    class="custom-control-label font-weight-light"
                                    :for="'visibility_' + form.id"
                                >
                                    {{ trans('global.visibility') }}
                                </label>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="kanban-status-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="kanban-status-save"
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
import {useGlobalStore} from "../../store/global";
import {useToast} from "vue-toastification";

export default {
    name: 'kanban-status-modal',
    props: {
        kanban: Object,
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
                kanban_id: this.kanban.id,
                owner_id: null,
                locked: false,
                editable: true,
                visibility: true,
                visible_from: null,
                visible_until: null,
                color: '#f4f4f4',
            }),
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
            axios.post('/kanbanStatuses', this.form)
                .then(r => {
                    this.$eventHub.emit('kanban-status-added', r.data);
                    this.globalStore?.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.toast.error(this.errorMessage(e));
                    console.log(e);
                });
        },
        update() {
            axios.patch('/kanbanStatuses/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('kanban-status-updated', r.data);
                    this.globalStore?.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.toast.error(this.errorMessage(e));
                    console.log(e);
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
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {

            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params;
                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params.status);
                    this.method = params.method;
                }
            }
        });
    },
}
</script>