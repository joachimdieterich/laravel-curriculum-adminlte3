<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @mouseup.self="close()"
        >
            <div class="modal-container">
                <div class="modal-header">
                    <span class="card-title">{{ trans('global.referenceable_types.objective') }}</span>
                    <div class="card-tools d-flex">
                        <button v-if="method !== 'post'"
                            v-permission="'objective_delete'"
                            type="button"
                            class="btn btn-icon mr-2"
                            :title="trans('global.delete')"
                            @click="destroy()"
                        >
                            <i class="fa fa-trash text-danger"></i>
                        </button>
                        <button
                            type="button"
                            class="btn btn-icon text-secondary"
                            :title="trans('global.close')"
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
                                <Select2
                                    id="curriculum_id"
                                    name="curriculum_id"
                                    url="/curricula"
                                    model="curriculum"
                                    option_id="id"
                                    option_label="title"
                                    :selected="form.curriculum_id"
                                    @selectedValue="(id) => this.form.curriculum_id = id"
                                />
                            </div>
                            <div v-if="this.form.curriculum_id"
                                class="form-group"
                            >
                                <Select2
                                    id="terminalObjectives_id"
                                    name="terminalObjectives_id"
                                    :url="'/curricula/' + form.curriculum_id + '/terminalObjectives'"
                                    model="terminalObjective"
                                    option_id="id"
                                    option_label="title"
                                    :selected="null"
                                    @selectedValue="(id) => this.form.terminal_objective_id = id"
                                />
                            </div>
                            <div v-if="form.terminal_objective_id"
                                class="form-group"
                            >
                                <Select2
                                    id="enablingObjectives_id"
                                    name="enablingObjectives_id"
                                    :url="'/terminalObjectives/' + form.terminal_objective_id + '/enablingObjectives'"
                                    model="enablingObjective"
                                    option_id="id"
                                    option_label="title"
                                    selected="null"
                                    @selectedValue="(id) => this.form.enabling_objective_id = id"
                                />
                            </div>
                            <div class="form-group">
                                <label for="description">{{ trans('global.description') }}</label>
                                <textarea
                                    id="description"
                                    name="description"
                                    class="form-control description my-editor"
                                    v-model="form.description"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="grade-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="close()"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="grade-save"
                            class="btn btn-primary ml-3"
                            @click="submit(method)"
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
    name: 'reference-objective-modal',
    components: {
        Select2,
    },
    props: {
        params: {
            type: Object
        },  //{ 'modelId': curriculum.id, 'modelUrl': 'curriculum' , 'shareWithToken': true, 'canEditCheckbox': false}
    },
    setup() { //use database store
        const globalStore = useGlobalStore();
        return {
            globalStore
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            url: '',
            form: new Form({
                id: null,
                subscribable_type: null,
                subscribable_id: null,
                curriculum_id: null,
                terminal_objective_id: null,
                enabling_objective_id: null,
                description: '',
            }),
        }
    },
    methods: {
        close() {
            this.globalStore.closeModal(this.$options.name);
        },
        submit(method) {
            if (method == 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            axios.post(this.url, this.form)
                .then(r => {
                    this.$eventHub.emit('reference-added', r.data);
                    this.close();
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch(this.url + '/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('reference-updated', r.data);
                    this.close();
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        destroy() {
            axios.delete(this.url + '/' + this.form.id)
                .then(r => {
                    this.$eventHub.emit('reference-deleted', r.data);
                    this.close();
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
                this.form.reset();

                const params = state.modals[this.$options.name].params;
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                    this.url = params.url;
                    if (this.form.id != null) {
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