<template>
    <Modal
        model="metadataset"
        modalName="metadataset-modal"
        :form="form"
        :disable-save-button="!form.version"
    >
        <template #general>
            <input
                id="metadataset-version"
                type="number"
                min="1"
                max="32767"
                class="form-control"
                maxlength="191"
                v-model.trim="form.version"
                :placeholder="trans('global.metadataset.fields.version')"
                required
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';

export default {
    name: 'metadataset-modal',
    components: { Modal },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                version: '',
            }),
        }
    },
}
</script>