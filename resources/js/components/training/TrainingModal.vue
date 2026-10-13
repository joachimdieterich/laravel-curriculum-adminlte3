<template>
    <Modal
        ref="modal"
        model="training"
        modalName="training-modal"
        :form="form"
        :require-title="true"
        :intercept-save="true"
        @opened="preProcessing()"
        @save="postProcessing()"
    >
        <template #general-extended>
            <div class="my-3">
                <Editor
                    :id="'description' + component_id"
                    licenseKey="gpl"
                    :init="tinyMCE"
                    v-model="form.description"
                />
            </div>

            <VueDatePicker
                v-model="form.date"
                :teleport="true"
                format="dd.MM.yyy HH:mm"
                locale="de"
                :range="{ partialRange: false }"
                time-picker-inline
                :start-time="[{ hours: 0, minutes: 0 }, { hours: 23, minutes: 59 }]"
                :placeholder="trans('global.selectDateRange')"
                :select-text="trans('global.ok')"
                :cancel-text="trans('global.close')"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Editor from '@tinymce/tinymce-vue';
import VueDatePicker from "@vuepic/vue-datepicker";
import '@vuepic/vue-datepicker/dist/main.css';

export default {
    name: 'training-modal',
    components: {
        Modal,
        VueDatePicker,
        Editor,
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                title: '',
                description: '',
                date: null,
                begin: null,
                end: null,
                subscribable_id: null,
                subscribable_type: null,
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink", "link", "lists", "table", "code", "autoresize",
                ],
                {
                    callback: 'insertContent',
                    callbackId: this.component_id,
                },
                "bold underline italic | alignleft aligncenter alignright alignjustify | bullist numlist | link",
                "",
            ),
        }
    },
    methods: {
        preProcessing() {
            if (this.form.begin && this.form.end) {
                this.form.date = [
                    new Date(this.reformatDate(this.form.begin)),
                    new Date(this.reformatDate(this.form.end))
                ];
            }
        },
        postProcessing() {
            if (this.form.date) {
                this.form.begin = this.form.date[0].toLocaleString();
                this.form.end = this.form.date[1].toLocaleString();
            } else {
                this.form.begin = null;
                this.form.end = null;
            }

            if (this.form.id) this.$refs.modal.update();
            else this.$refs.modal.add();
        },
        /**
         * change date-format from 'Mo., DD.MM.YYYY HH:mm' to 'MM.DD.YYYY'
         * 
         * @param date
         */
        reformatDate(date) {
            let basicDate = date.split(', ')[1];
            const day = basicDate.slice(0, 3);
            const month = basicDate.slice(4, 6);

            return month + day + basicDate.slice(7);
        },
    },
}
</script>