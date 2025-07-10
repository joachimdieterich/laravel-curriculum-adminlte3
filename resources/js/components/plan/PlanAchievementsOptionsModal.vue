<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @mouseup.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">{{ trans('global.options') }}</h3>
                    <div class="card-tools">
                        <button
                            type="button"
                            class="btn btn-tool"
                            @click="globalStore.closeModal($options.name)"
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
                            <div class="form-group">
                                <label for="timespan">{{ trans('global.plan.options.timespan') }}</label>
                                <VueDatePicker
                                    id="timespan"
                                    name="timespan"
                                    format="dd.MM.yyyy"
                                    :range="{ partialRange: false }"
                                    :enable-time-picker="false"
                                    :start-time="[{ hours: 0, minutes: 0 }, { hours: 23, minutes: 59 }]"
                                    locale="de"
                                    v-model="options.timespan"
                                    :placeholder="trans('global.selectDateRange')"
                                    :select-text="trans('global.ok')"
                                    :cancel-text="trans('global.close')"
                                    @update:model-value="setTimespan()"
                                    @cleared="options.hideUnset = false"
                                />
                            </div>

                            <div class="custom-switch custom-switch-on-green mb-2">
                                <input type="checkbox" id="teacher_toggle" class="custom-control-input" v-model="options.showTeacher" disabled>
                                <label for="teacher_toggle" class="custom-control-label pointer" style="cursor: not-allowed">{{ trans('global.plan.options.toggle_teacher') }}</label>
                            </div>

                            <div class="custom-switch custom-switch-on-green mb-2">
                                <input type="checkbox" id="student_toggle" class="custom-control-input" v-model="options.showStudent" disabled>
                                <label for="student_toggle" class="custom-control-label pointer" style="cursor: not-allowed">{{ trans('global.plan.options.toggle_student') }}</label>
                            </div>

                            <div class="custom-switch custom-switch-on-green mb-2">
                                <input type="checkbox" id="unset_toggle" class="custom-control-input" v-model="options.hideUnset">
                                <label for="unset_toggle" class="custom-control-label pointer" @click="toggleUnset()">{{ trans('global.plan.options.toggle_unset') }}</label>
                            </div>

                            <div class="custom-switch custom-switch-on-green">
                                <input type="checkbox" id="objectives_toggle" class="custom-control-input" v-model="options.collapseObjectives">
                                <label for="objectives_toggle" class="custom-control-label pointer" @click="toggleObjectives()">{{ trans('global.plan.options.toggle_objectives') }}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="d-flex justify-content-between pull-right">
                        <button
                            class="btn btn-primary"
                            @click="globalStore.closeModal($options.name)"
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
import VueDatePicker from "@vuepic/vue-datepicker";
import '@vuepic/vue-datepicker/dist/main.css';
import {useGlobalStore} from "../../store/global";

export default {
    name: 'plan-achievements-options-modal',
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            options: {
                timespan: null,
                hideUnset: false,
                showTeacher: true,
                showStudent: false,
                collapseObjectives: false,
            },
        }
    },
    methods: {
        setTimespan() {
            this.$parent.filterByTimespan(this.options.timespan);
            // if timespan got set, wait for the calendar-overlay to disappear and turn-on the 'hide-unset-achievements' toggle
            if (this.options.timespan !== null) {
                setTimeout(() => {
                    this.options.hideUnset = true; // don't call the toggleUnset() function
                }, 200);
            }
        },
        toggleUnset() {
            // setTimeout is needed because of race condition
            setTimeout(() => {
                this.$parent.toggleUnset(this.options.hideUnset);
            }, 50);
        },
        toggleObjectives() {
            setTimeout(() => {
                this.$parent.toggleObjectives(this.options.collapseObjectives);
            }, 50);
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
    },
    components: {
        VueDatePicker,
    },
}
</script>