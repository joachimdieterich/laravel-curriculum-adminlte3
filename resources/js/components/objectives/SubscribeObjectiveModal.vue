<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @click.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ trans('global.referenceable_types.link') }}
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool draggable">
                            <i class="fa fa-arrows-alt"></i>
                        </button>
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
                    style="overflow: visible;"
                >
                    <div class="card">
                        <div class="card-body">
                            <Select2
                                id="curriculum_id"
                                name="curriculum_id"
                                url="/curricula"
                                model="curriculum"
                                option_id="id"
                                option_label="title"
                                @selectedValue="(id) => {
                                    this.form.curriculum_id = id[0];
                                }"
                            />

                            <Select2 v-if="form.curriculum_id"
                                id="terminalObjectives_id"
                                name="terminalObjectives_id"
                                :url="'/curricula/' + form.curriculum_id + '/terminalObjectives'"
                                model="terminalObjective"
                                :multiple="true"
                                option_id="id"
                                option_label="title"
                                @selectedValue="(id) => {
                                    this.form.terminal_objective_id = id;
                                    this.form.enabling_objective_id = [];
                                }"
                            />

                            <Select2 v-if="form.terminal_objective_id.length === 1"
                                id="enablingObjectives_id"
                                name="enablingObjectives_id"
                                :url="'/terminalObjectives/' + form.terminal_objective_id[0] + '/enablingObjectives'"
                                model="enablingObjective"
                                :multiple="true"
                                option_id="id"
                                option_label="title"
                                selected="null"
                                @selectedValue="(id) => {
                                    this.form.enabling_objective_id = id;
                                }"
                            />
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="grade-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="grade-save"
                            class="btn btn-primary ml-3"
                            :disabled="form.terminal_objective_id.length === 0"
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
import axios from 'axios';

export default {
    name: 'subscribe-objective-modal',
    components: {
        Select2,
    },
    props: {},
    setup() { //use database store
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                subscribable_type: null,
                subscribable_id: null,
                curriculum_id: null,
                terminal_objective_id: [],
                enabling_objective_id: [],
            }),
        }
    },
    methods: {
        submit() {
            const type = this.form.enabling_objective_id.length === 0 ? 'terminal' : 'enabling';

            axios.post('/' + type + 'ObjectiveSubscriptions', {
                terminal_objective_id:    this.form.terminal_objective_id,
                enabling_objective_id:    this.form.enabling_objective_id, // will be discarded in terminal-controller
                subscribable_type:        this.form.subscribable_type,
                subscribable_id:          this.form.subscribable_id
            })
            .then(response => {
                this.$eventHub.emit('subscriptions-added', {
                    terminal_objectives: response.data[0],
                    id: this.form.subscribable_id,
                });
                this.globalStore.closeModal(this.$options.name);
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
                }
            }
        });
    },
}
</script>
<style>
.select2-selection__rendered { white-space: normal !important; }
</style>