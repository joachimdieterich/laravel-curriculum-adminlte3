<template>
    <Modal
        model="logbook"
        modalName="logbook-modal"
        :form="form"
        :allow-overflow="true"
        :require-title="true"
        :show-description-field="true"
        :show-owner-field="form.id && checkPermission('is_teacher')"
        :show-display-section="true"
        :show-medium-field="true"
        :show-icon-picker="true"
    ></Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';

export default {
    name: 'logbook-modal',
    components: { Modal },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                title:  '',
                description: '',
                owner_id: null,
                medium_id: null,
                color: '#27AF60',
                css_icon: 'fa fa-book',
            }),
        }
    },
}
</script>