<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @mouseup.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        <span v-if="method === 'post'">
                            {{ trans('global.role.create') }}
                        </span>
                        <span v-if="method === 'patch'">
                            {{ trans('global.role.edit') }}
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
                    class="modal-body"
                >
                    <div class="card">
                        <div class="card-body">
                            <div
                                class="form-group"
                                :class="form.errors.title ? 'has-error' : ''"
                            >
                                <input
                                    id="title"
                                    name="title"
                                    type="text"
                                    class="form-control"
                                    v-model="form.title"
                                    :readonly="this.method == 'patch'"
                                    :placeholder="trans('global.title') + ' *'"
                                    required
                                />
                                <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                            </div>
                            <Select2
                                v-permission="'tag_access'"
                                id="tags"
                                name="tags"
                                url="/tags"
                                model="tags"
                                :additional_query_param="{'type': 'App\\Role'}"
                                :label="trans('global.tag.title')"
                                :multiple="true"
                                :selected="getSelectedTags()"
                                @selectedValue="(id) => {
                                    this.form.tags = id;
                                }"
                                @cleared="() => {
                                    this.form.tags = [];
                                }"
                            />
                            <Select2
                                id="permissions"
                                name="permissions"
                                url="/permissions"
                                model="permission"
                                :multiple="true"
                                :selected="getSelectedPermissions()"
                                @selectedValue="(id) => {
                                    this.form.permissions = id;
                                }"
                                @cleared="() => {
                                    this.form.permissions = [];
                                }"
                            />
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="role-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="role-save"
                            class="btn btn-primary ml-3"
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
import Select2 from "../forms/Select2.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'role-modal',
    components: {
        Select2,
    },
    props: {
        params: {
            type: Object,
        },
    },
    setup() { //use database store
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
                id:'',
                title: '',
                permissions: '',
                tags: [],
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

            this.globalStore.closeModal(this.$options.name);
        },
        add() {
            axios.post('/roles', this.form)
                .then(r => {
                    this.$eventHub.emit('role-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch('/roles/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('role-updated', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        getSelectedPermissions(permissions) {
            let base = permissions ?? this.form.permissions;

            if (base[0]?.title){
                return base.map(p => p.id);
            }

            return base;
        },
        getSelectedTags(tags) {
            let base = tags ?? this.form.tags;

            if (base[0]?.name){
                return base.map(t => t.id);
            }

            return base;
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params;
                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    params.permissions = this.getSelectedPermissions(params.permissions);
                    params.tags = this.getSelectedTags(params.tags);
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