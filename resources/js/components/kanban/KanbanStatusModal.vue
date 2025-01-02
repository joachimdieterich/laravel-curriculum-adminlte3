<template>
    <Transition name="modal">
        <div
            v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        <span v-if="method === 'post'">
                            {{ trans('global.kanbanStatus.create') }}
                        </span>
                        <span v-if="method === 'patch'">
                            {{ trans('global.kanbanStatus.edit') }}
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
                            type="text"
                            id="title"
                            name="title"
                            class="form-control"
                            v-model.trim="form.title"
                            :placeholder="trans('global.kanbanItem.fields.title')"
                            required
                        />
                        <p
                            v-if="form.errors.title"
                            class="help-block"
                            v-text="form.errors.title[0]"
                        ></p>
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
                            show-fallback
                            fallback-input-type="color"

                            @input="(id) => {
                                    if(id.isInteger){
                                      this.form.color = id;
                                    }
                                }"
                            :max-height="300"
                        ></v-swatches>
                    </div>

                    <div
                        class="card-header border-bottom"
                        data-card-widget="collapse"
                    >
                        <h5 class="card-title">
                            {{ trans('global.permissions') }}
                        </h5>
                    </div>
                    <div class="card-body pb-0">
                        <div
                            v-if=" $userId == form.owner_id
                                || $userId == kanban.owner_id
                                || method === 'post'"
                            class="form-group"
                        >
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
                            @click="close()"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="kanban-status-save"
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
import axios from "axios";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'kanban-status-modal',
    components: {},
    props: {
        kanban: Object,
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
            url: '/kanbanStatuses',
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
        close() {
            this.globalStore?.closeModal(this.$options.name);
        },
        submit() {
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
                    this.$eventHub.emit('kanban-status-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch(this.url + '/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('kanban-status-updated', r.data);
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
                    this.form.populate(params.status);
                    this.method = params.method;
                }
            }
        });
    },
}
</script>
