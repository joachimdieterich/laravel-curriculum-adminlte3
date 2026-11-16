<template>
    <Modal
        model="permission"
        modalName="permission-modal"
        :form="form"
        :require-title="true"
    />
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';

export default {
    name: 'permission-modal',
    components: { Modal },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                title: '',
            }),
        }
    },
}
</script>