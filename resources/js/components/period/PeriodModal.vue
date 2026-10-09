<template>
    <Modal
        ref="modal"
        model="period"
        modalName="period-modal"
        :form="form"
        :require-title="true"
        :disable-save-button="!form.date"
        :intercept-save="true"
        @opened="preProcessing($event)"
        @save="postProcessing()"
    >
        <template #general-extended>
            <VueDatePicker
                v-model="form.date"
                class="mt-3"
                :range="{ partialRange: false }"
                format="dd.MM.yyy"
                :teleport="true"
                locale="de"
                :placeholder="trans('global.selectDateRange') + ' *'"
                :select-text="trans('global.ok')"
                :cancel-text="trans('global.close')"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import VueDatePicker from "@vuepic/vue-datepicker";
import '@vuepic/vue-datepicker/dist/main.css';

export default {
    name: 'period-modal',
    components: {
        Modal,
        VueDatePicker,
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                title: '',
                date: null,
                begin: null,
                end: null,
            }),
        }
    },
    methods: {
        preProcessing(params) {
            if (params.begin !== null && params.end !== null) {
                this.form.date = [params.begin, params.end];
            }
        },
        postProcessing() {
            this.form.begin = this.form.date[0];
            this.form.end = this.form.date[1];

            if (this.form.id) this.$refs.modal.update();
            else this.$refs.modal.add();
        },
    },
}
</script>