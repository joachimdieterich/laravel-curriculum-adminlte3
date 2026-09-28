<template>
    <Modal
        model="planEntry"
        modalName="plan-entry-modal"
        url="/planEntries"
        :form="form"
        :require-title="true"
        :show-display-section="true"
        :show-medium-field="true"
        :show-icon-picker="true"
    >
        <template #general-extended>
            <div class="mt-3">
                <Editor
                    id="plan-entry-description"
                    licenseKey="gpl"
                    :init="tinyMCE"
                    v-model="form.description"
                />
            </div>
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Editor from "@tinymce/tinymce-vue";

export default {
    name: 'plan-entry-modal',
    components: {
        Modal,
        Editor,
    },
    props: {
        plan: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                title: '',
                description: '',
                plan_id: this.plan.id,
                color: '#27AF60',
                css_icon: 'fa fa-clipboard',
                order_id: 0,
                medium_id: null,
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink", "link", "lists", "code", "autoresize",
                ],
                {
                    callback: 'insertContent',
                    callbackId: this.component_id,
                    subscribable_type: 'App\\Plan',
                    subscribable_id: this.plan.id,
                },
                "bold underline italic | alignleft aligncenter alignright alignjustify",
                "bullist numlist outdent indent | curriculummedia link mathjax code",
            ),
        }
    },
}
</script>