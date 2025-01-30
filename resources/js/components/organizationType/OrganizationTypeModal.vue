<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        <span v-if="method === 'post'">
                            {{ trans('global.organizationType.create') }}
                        </span>
                        <span v-if="method === 'patch'">
                            {{ trans('global.organizationType.edit') }}
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
                                    id="title"
                                    type="text"
                                    name="title"
                                    class="form-control"
                                    v-model="form.title"
                                    :placeholder="trans('global.title') + ' *'"
                                    required
                                    />
                                <p class="help-block"
                                    v-if="form.errors.title"
                                    v-text="form.errors.title[0]"
                                ></p>
                            </div>

                            <div class="form-group">
                                <input
                                    id="external_id"
                                    type="text"
                                    name="external_id"
                                    class="form-control"
                                    v-model="form.external_id"
                                    :placeholder="trans('global.organizationType.fields.external_id') + ' *'"
                                    required
                                />
                                <p class="help-block"
                                    v-if="form.errors.external_id"
                                    v-text="form.errors.external_id[0]"
                                ></p>
                            </div>

                            <Select2
                                id="country_id"
                                name="country_id"
                                option_id="alpha2"
                                option_label="lang_de"
                                :label="trans('global.country.title_singular') + ' *'"
                                url="/countries"
                                model="country"
                                :selected="this.form.country_id"
                                @selectedValue="(id) => {
                                    this.form.country_id = id;
                                    this.form.state_id = '';
                                }"
                            />

                            <Select2
                                id="state_id"
                                name="state_id"
                                option_id="code"
                                option_label="lang_de"
                                css="mb-0"
                                :url="'/countries/' + this.form.country_id + '/states/'"
                                model="state"
                                :term="this.form.country_id"
                                :selected="this.form.state_id"
                            />
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="organization-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="organization-save"
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
    name: 'organizationtype-modal',
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
                external_id: '',
                country_id: 'DE',
                state_id: 'DE-RP',
            }),
        }
    },
    methods: {
        submit() {
            if (this.method === 'patch') {
                this.update();
            } else {
                this.add();
            }

            this.globalStore.closeModal(this.$options.name);
        },
        add() {
            axios.post('/organizationTypes', this.form)
                .then(r => {
                    this.$eventHub.emit('organization-type-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch('/organizationTypes/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('organization-type-updated', r.data);
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