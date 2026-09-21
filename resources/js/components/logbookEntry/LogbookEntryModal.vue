<template>
    <Modal
        ref="modal"
        model="logbookEntry"
        modalName="logbook-entry-modal"
        url="/logbookEntries"
        :form="form"
        :require-title="true"
        :intercept-save="true"
        @opened="preProcessing()"
        @save="postProcessing()"
    >
        <template #general-extended>
            <div class="mt-3">
                <Editor
                    id="description"
                    name="description"
                    licenseKey="gpl"
                    :init="tinyMCE"
                    v-model="form.description"
                />
            </div>

            <VueDatePicker
                class="mt-3"
                v-model="form.date"
                :range="{ partialRange: false }"
                format="dd.MM.yyyy HH:mm"
                time-picker-inline
                :teleport="true"
                locale="de"
                :placeholder="trans('global.selectDateRange')"
                :select-text="trans('global.ok')"
                :cancel-text="trans('global.close')"
                @cleared="form.date = ['', '']"
            />      
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Editor from "@tinymce/tinymce-vue";
import VueDatePicker from "@vuepic/vue-datepicker";
import '@vuepic/vue-datepicker/dist/main.css';

export default {
    name: 'logbook-entry-modal',
    components: {
        Modal,
        Editor,
        VueDatePicker,
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                logbook_id: '',
                title: '',
                description: '',
                date: null,
                begin: '',
                end: '',
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink", "link", "lists", "autoresize",
                ],
                {
                    'callback': 'insertContent',
                    'callbackId': this.component_id
                },
                "bold underline italic | alignleft aligncenter alignright alignjustify | bullist numlist | link",
                "",
            ),
        }
    },
    methods: {
        preProcessing() {
            this.form.date = [this.form.begin ?? '', this.form.end ?? ''];
        },
        postProcessing() {
            this.form.begin = this.form.date[0].toLocaleString();
            this.form.end = this.form.date[1].toLocaleString();

            if (this.form.id) this.$refs.modal.update();
            else this.$refs.modal.add();
        },
    },
}
</script>