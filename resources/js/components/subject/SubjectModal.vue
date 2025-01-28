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
                            {{ trans('global.subject.create') }}
                        </span>
                        <span v-if="method === 'patch'">
                            {{ trans('global.subject.edit') }}
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
                    style="overflow-y: visible;"
                >
                    <div class="card">
                        <div class="card-body">
                            <div
                                class="form-group"
                                :class="form.errors.title ? 'has-error' : ''"
                            >
                                <input
                                    type="text"
                                    id="title"
                                    name="title"
                                    class="form-control"
                                    v-model="form.title"
                                    :placeholder="trans('global.subject.fields.title') + ' *'"
                                    required
                                />
                                <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                            </div>

                            <div
                                class="form-group"
                                :class="form.errors.title_short ? 'has-error' : ''"
                            >
                                <input
                                    type="text"
                                    id="title_short"
                                    name="title_short"
                                    class="form-control"
                                    v-model="form.title_short"
                                    :placeholder="trans('global.subject.fields.title_short')"
                                    required
                                />
                                <p class="help-block"
                                v-if="form.errors.title_short"
                                v-text="form.errors.title_short[0]"
                                ></p>
                            </div>

                            <Select2
                                id="organization_type_id"
                                name="organization_type_id"
                                url="/organizationTypes"
                                model="organizationType"
                                :label="trans('global.organizationType.title_singular') + ' *'"
                                option_id="id"
                                option_label="title"
                                :selected="this.form.organization_type_id"
                                @selectedValue="(id) => {
                                    this.form.organization_type_id = id;
                                }"
                            />
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="subject-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="subject-save"
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
import {useGlobalStore} from "../../store/global";
import Select2 from "../forms/Select2.vue";

export default {
    name: 'subject-modal',
    components: {
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
                title_short: '',
                organization_type_id: 1,
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
            axios.post('/subjects', this.form)
                .then(r => {
                    this.$eventHub.emit('subject-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch('/subjects/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('subject-updated', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        getSelected() {
            if (this.form.permissions[0]?.title){
                return this.form.permissions.map(p => p.id);
            } else {
                return this.form.permissions;
            }
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