<template>
    <Modal
        model="certificate"
        modalName="certificate-modal"
        :form="form"
        :allow-overflow="true"
        :require-title="true"
        :disable-save-button="!form.body || !form.curriculum_id || !form.organization_id"
    >
        <template #general-extended>
            <div class="mt-3">
                <input
                    id="certificate-description"
                    type="text"
                    class="form-control"
                    v-model.trim="form.description"
                    :placeholder="trans('global.description')"
                />
            </div>

            <div class="mt-3">
                <Editor
                    id="certificate-body"
                    licenseKey="gpl"
                    :init="tinyMCE"
                    v-model="form.body"
                />
            </div>

            <Select2
                id="certificate-curriculum"
                url="/curricula"
                model="curriculum"
                :label="trans('global.curriculum.title_singular') + ' *'"
                :selected="form.curriculum_id"
                @selectedValue="id => form.curriculum_id = id[0]"
            />

            <Select2
                id="certificate-organization"
                :url="'/organizations'"
                model="organization"
                :label="trans('global.organization.title_singular') + ' *'"
                :selected="form.organization_id"
                @selectedValue="id => form.organization_id = id"
            />

            <Switch
                id="certificate-global"
                class="mt-3"
                label="global.global.title_singular"
                v-model="form.global"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Editor from '@tinymce/tinymce-vue';
import Select2 from "../forms/Select2.vue";
import Switch from "../forms/Switch.vue";

export default {
    name: 'certificate-modal',
    components: {
        Modal,
        Switch,
        Editor,
        Select2,
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                title: '',
                description: '',
                body: '',
                curriculum_id: null,
                organization_id: null,
                global: false,
            }),
            tinyMCE: this.$initTinyMCE(
                null,
                {
                    callback: 'insertContent',
                    callbackId: this.component_id,
                    placeholder: this.trans('global.certificate.fields.body') + ' *',
                },
                "undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link code",
                "span[id|class|style|name|reference_type|reference_id|min_value]",
            ),
        }
    },
}
</script>