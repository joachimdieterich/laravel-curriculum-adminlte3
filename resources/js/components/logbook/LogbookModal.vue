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
                            {{ trans('global.logbook.create') }}
                        </span>
                        <span v-if="method === 'patch'">
                            {{ trans('global.logbook.edit') }}
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
                        <div class="card-body pb-0">
                            <div
                                class="form-group"
                                :class="form.errors.title ? 'has-error' : ''"
                            >
                                <input
                                    type="text"
                                    id="title"
                                    name="title"
                                    class="form-control"
                                    v-model.trim="form.title"
                                    :placeholder="trans('global.title') + ' *'"
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
                                    class="form-control"
                                    style="max-height: 50svh;"
                                    :placeholder="trans('global.description')"
                                    v-model="form.description"
                                ></textarea>
                                <p v-if="form.errors.description"
                                    class="help-block"
                                    v-text="form.errors.description[0]"
                                ></p>
                            </div>
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
                                    :swatch-size="49"
                                    :trigger-style="{}"
                                    style="height: 42px;"
                                    popover-to="right"
                                    v-model="this.form.color"
                                    show-fallback
                                    fallback-input-type="color"
                                    @input="(id) => {
                                        if (id.isInteger) {
                                            this.form.color = id;
                                        }
                                    }"
                                    :max-height="300"
                                />
                                <MediumForm v-if="form.id"
                                    :id="'medium_form' + component_id"
                                    :medium_id="form.medium_id"
                                    :subscribable_id="form.id"
                                    subscribable_type="App\Logbook"
                                    accept="image/*"
                                    @selectedValue="(id) => {
                                        // on removal of medium, directly update the resource
                                        if (this.form.medium_id !== null && id === null) {
                                            this.$eventHub.emit('logbook-updated', {
                                                id: this.form.id,
                                                medium_id: null,
                                            });
                                        }
                                        this.form.medium_id = id;
                                    }"
                                />
                                <div class="dropdown">
                                    <button
                                        class="btn btn-default"
                                        style="width: 42px; padding: 6px 0px;"
                                        type="button"
                                        data-toggle="dropdown"
                                        aria-expanded="false"
                                    >
                                        <i :class="form.css_icon + ' pt-2'"></i>
                                    </button>
                                    <font-awesome-picker
                                        class="dropdown-menu dropdown-menu-right"
                                        style="min-width: min(385px,90vw);"
                                        :searchbox="trans('global.select_icon')"
                                        v-on:selectIcon="setIcon"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="logbook-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="logbook-save"
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
import Select2 from "../forms/Select2.vue";
import {useGlobalStore} from "../../store/global";
import FontAwesomePicker from "../../../views/forms/input/FontAwesomePicker.vue";
import MediumForm from "../media/MediumForm.vue";

export default {
    name: 'logbook-modal',
    components: {
        MediumForm,
        FontAwesomePicker,
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
                title:  '',
                description:  '',
                medium_id: null,
                color:'#27AF60',
                css_icon: 'fa fa-book',
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
            axios.post('/logbooks', this.form)
                .then(r => {
                    this.$eventHub.emit('logbook-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch('/logbooks/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('logbook-updated', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        setIcon(selectedIcon) {
            this.form.css_icon = 'fa fa-' + selectedIcon.className;
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

                    if (this.form.id != '') {
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