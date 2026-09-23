<template>
    <Modal
        model="content"
        modalName="content-modal"
        :form="form"
        :require-title="true"
        :disable-save-button="!form.content"
    >
        <template #general-extended>
            <div class="mt-3">
                <Editor
                    id="content"
                    name="content"
                    licenseKey="gpl"
                    :init="tinyMCE"
                    v-model="form.content"
                />
            </div>
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Editor from '@tinymce/tinymce-vue';

export default {
    name: 'content-modal',
    components: {
        Modal,
        Editor,
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                title: '',
                content: '',
                subscribable_id: null,
                subscribable_type: null,
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink", "link", "table", "lists", "code", "autoresize",
                ],
                {
                    callback: 'insertContent',
                    callbackId: this.component_id,
                    placeholder: window.trans.global.description + ' *',
                },
                "bold underline italic | alignleft aligncenter alignright alignjustify | bullist numlist | link mathjax code",
                ""
            ),
        }
    },
}
</script>