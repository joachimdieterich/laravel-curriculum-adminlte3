<template>
    <Modal
        model="contactDetail"
        modalName="contact-modal"
        :form="form"
        :disable-save-button="!form.email || !form.phone || !form.mobile"
    >
        <template #general>
            <div class="mb-3">
                <label for="contact-detail-email" class="form-label">
                    {{ trans('global.contactDetail.fields.email') }} *
                </label>
                <input
                    type="text"
                    id="contact-detail-email"
                    class="form-control"
                    maxlength="191"
                    v-model="form.email"
                    :placeholder="trans('global.contactDetail.fields.email')"
                    required
                />
            </div>
        
            <div class="mb-3">
                <label for="contact-detail-phone" class="form-label">
                    {{ trans('global.contactDetail.fields.phone') }} *
                </label>
                <input
                    type="text"
                    id="contact-detail-phone"
                    class="form-control"
                    maxlength="191"
                    v-model="form.phone"
                    :placeholder="trans('global.contactDetail.fields.phone')"
                    required
                />
            </div>

            <div class="mb-3">
                <label for="contact-detail-mobile" class="form-label">
                    {{ trans('global.contactDetail.fields.mobile') }} *
                </label>
                <input
                    type="text"
                    id="contact-detail-mobile"
                    class="form-control"
                    maxlength="191"
                    v-model="form.mobile"
                    :placeholder="trans('global.contactDetail.fields.mobile')"
                    required
                />
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
