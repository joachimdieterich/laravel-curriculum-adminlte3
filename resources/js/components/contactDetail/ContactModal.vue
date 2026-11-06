<template>
    <Modal
        model="contactDetail"
        modalName="contact-modal"
        :form="form"
        :disable-save-button="!form.email || !form.phone || !form.mobile"
    >
        <template #general>
            <div class="form-floating mb-4">
                <input
                    type="text"
                    id="contact-detail-email"
                    class="form-control"
                    maxlength="191"
                    v-model="form.email"
                    placeholder=""
                    required
                />
                <label for="contact-detail-email">E-Mail *</label>
            </div>
        
            <div class="form-floating mb-4">
                <input
                    type="text"
                    id="contact-detail-phone"
                    class="form-control"
                    maxlength="191"
                    v-model="form.phone"
                    placeholder=""
                    required
                />
                <label for="contact-detail-phone">{{ trans('global.contactDetail.fields.phone') }} *</label>
            </div>

            <div class="form-floating mb-3">
                <input
                    type="text"
                    id="contact-detail-mobile"
                    class="form-control"
                    maxlength="191"
                    v-model="form.mobile"
                    placeholder=""
                    required
                />
                <label for="contact-detail-mobile">{{ trans('global.contactDetail.fields.mobile') }} *</label>
            </div>

            <div>
                <label for="contact-detail-notes" class="form-label">
                    {{ trans('global.contactDetail.fields.notes') }}
                </label>
                <Editor
                    id="contact-detail-notes"
                    licenseKey="gpl"
                    :init="tinyMCE"
                    v-model="form.notes"
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
    name: 'contact-modal',
    components: {
        Modal,
        Editor,
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                email: '',
                phone: '',
                mobile: '',
                notes: '',
            }),
            tinyMCE: this.$initTinyMCE(
                null,
                {
                    'callback': 'insertContent',
                    'callbackId': this.component_id

                },
                "undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link",
                "",
                "span[id|class|style|name|reference_type|reference_id|min_value]",
            ),
        }
    },
}
</script>
