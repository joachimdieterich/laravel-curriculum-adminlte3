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
                                    type="text"
                                    id="title"
                                    name="title"
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
        
                            <div class="form-group">
                                <textarea
                                    id="description"
                                    name="description"
                                    :placeholder="trans('global.kanbanItem.fields.description')"
                                    class="form-control description "
                                    style="max-height: 35svh;"
                                    v-model.trim="form.description"
                                ></textarea>
                                <p v-if="form.errors.description"
                                    class="help-block"
                                    v-text="form.errors.description[0]"
                                ></p>
                            </div>

                            <Select2 v-if="checkPermission('is_admin')"
                                id="user_id"
                                css="mb-0 mt-3"
                                :label="trans('global.change_owner')"
                                model="User"
                                url="/users"
                                :selected="form.owner_id"
                                @selectedValue="(id) => this.form.owner_id = id[0]"
                            />
                        </div>
                    </div>

                    <div class="card">
                        <div
                            class="card-header border-bottom"
                            data-card-widget="collapse"
                        >
                            <h5 class="card-title">{{ trans('global.display') }}</h5>
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
                                    @input="(id) => {
                                        if (id.isInteger) {
                                            this.form.color = id;
                                        }
                                    }"
                                />
        
                                <MediumForm v-if="form.id"
                                    :id="'medium_form' + component_id"
                                    :medium_id="form.medium_id"
                                    :subscribable_id="form.id"
                                    subscribable_type="App\Kanban"
                                    accept="image/*"
                                    @selectedValue="(id) => {
                                        // on removal of medium, directly update the resource
                                        if (this.form.medium_id !== null && id === null) {
                                            this.$eventHub.emit('kanban-updated', {
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
                            <h5 class="card-title">{{ trans('global.permissions') }}</h5>
                        </div>
    
                        <div class="card-body">
                            <span class="custom-control custom-switch custom-switch-on-green">
                                <input
                                    :id="'commentable_' + form.id"
                                    class="custom-control-input pt-1"
                                    type="checkbox"
                                    v-model="form.commentable"
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
                                    :id="'auto_refresh_' + form.id"
                                    class="custom-control-input pt-1"
                                    type="checkbox"
                                    v-model="form.auto_refresh"
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
                                    :id="'only_edit_owned_items_' + form.id"
                                    class="custom-control-input pt-1"
                                    type="checkbox"
                                    v-model="form.only_edit_owned_items"
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
                                    :id="'collapse_items_' + form.id"
                                    class="custom-control-input pt-1"
                                    type="checkbox"
                                    v-model="form.collapse_items"
                                />
                                <label
                                    class="custom-control-label text-muted"
                                    :for="'collapse_items_' + form.id"
                                >
                                    {{ trans('global.kanban.collapse_items') }}
                                </label>
                            </span>
                            <span class="custom-control custom-switch custom-switch-on-green">
                                <input
                                    :id="'allow_copy_' + form.id"
                                    class="custom-control-input pt-1"
                                    type="checkbox"
                                    v-model="form.allow_copy"
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
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="kanban-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="kanban-save"
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
import MediumForm from "../media/MediumForm.vue";
import axios from "axios";
import Select2 from "../forms/Select2.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'kanban-modal',
    components: {
        Select2,
        MediumForm,
    },
    props: {
        params: {
            type: Object,
            default: null,
        },
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
            form: new Form({
                id: '',
                title:  '',
                description:  '',
                owner_id: null,
                color:'#27AF60',
                medium_id: null,
                commentable: true,
                auto_refresh: false,
                only_edit_owned_items: false,
                collapse_items: false,
                allow_copy: true,
            }),
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
            axios.post('/kanbans', this.form)
                .then(r => {
                    this.$eventHub.emit('kanban-added', r.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.toast.error(this.trans(this.errorMessage(e)));
                    console.log(e.response);
                });
        },
        update() {
            axios.patch('/kanbans/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('kanban-updated', r.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.toast.error(this.trans(this.errorMessage(e)));
                    console.log(e.response);
                });
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show && !state.modals[this.$options.name].lock) {
                this.globalStore.lockModal(this.$options.name);
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