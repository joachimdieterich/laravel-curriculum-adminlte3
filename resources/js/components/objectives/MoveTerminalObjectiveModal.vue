<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @click.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        <span>{{ trans('global.terminalObjective.move') }}</span>
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
                            <Select2
                                id="curriculum_id"
                                name="curriculum_id"
                                url="/curricula"
                                model="curriculum"
                                css="mb-1"
                                :label="trans('global.curriculum.title_singular') + ' *'"
                                :selected="form.curriculum_id"
                                @selectedValue="(id) => {
                                    this.form.curriculum_id = id[0];
                                }"
                            />
    
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="enablingObjective-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="enablingObjective-save"
                            class="btn btn-primary ml-3"
                            :disabled="!form.curriculum_id || form.curriculum_id == currentCurriculum"
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
    name: 'move-terminal-objective-modal',
    components: {
        Select2,
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            currentCurriculum: null,
            form: new Form({
                id: null,
                curriculum_id: null,
                objective_type_id: null, // only needed for check in backend
            }),
        }
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params;
                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                    this.currentCurriculum = params.curriculum_id
                }
            }
        });
    },
    methods: {
        submit() {
            axios.patch('/terminalObjectives/' + this.form.id, this.form)
                .then(response => {
                    this.$eventHub.emit('objective-deleted', response.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(response => {
                    console.log(response);
                });
        },
    },
}
</script>