<template>
    <Modal
        model="planAchievement"
        modalName="plan-achievements-options-modal"
        title="global.options"
        :allow-overflow="true"
        :show-cancel-button="false"
        :intercept-save="true"
        @save="globalStore.closeModal($options.name)"
    >
        <template #general>
            <div class="d-flex flex-column gap-2">
                <div>
                    <label for="achievements-timespan">{{ trans('global.plan.options.timespan') }}</label>
                    <VueDatePicker
                        id="achievements-timespan"
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
    
                <Switch
                    id="achievements-show-teacher"
                    label="global.plan.options.toggle_teacher"
                    v-model="options.showTeacher"
                    :disabled="true"
                />
                <Switch
                    id="achievements-show-student"
                    label="global.plan.options.toggle_student"
                    v-model="options.showStudent"
                    :disabled="true"
                />
                <Switch
                    id="achievements-hide-unset"
                    label="global.plan.options.toggle_unset"
                    v-model="options.hideUnset"
                    @update:model-value="toggleUnset()"
                />
                <Switch
                    id="achievements-collapse-objectives"
                    label="global.plan.options.toggle_objectives"
                    v-model="options.collapseObjectives"
                    @update:model-value="toggleObjectives()"
                />
            </div>
        </template>
    </Modal>
</template>
<script>
import Modal from "../uiElements/Modal.vue";
import VueDatePicker from "@vuepic/vue-datepicker";
import '@vuepic/vue-datepicker/dist/main.css';
import Switch from "../forms/Switch.vue";

export default {
    name: 'plan-achievements-options-modal',
    components: {
        Modal,
        VueDatePicker,
        Switch,
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
                    // don't call the toggleUnset() function, since unset achievements will automatically be hidden
                    this.options.hideUnset = true;
                }, 200);
            }
        },
        toggleUnset() {
            // setTimeout is needed because of a race condition
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
}
</script>