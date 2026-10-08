<template>
    <Modal
        model="navigator"
        modalName="navigator-modal"
        :form="form"
        :require-title="true"
        :allow-overflow="true"
        :disable-save-button="!form.organization_id"
    >
        <template #general-extended>
            <Select2
                id="navigator-organization"
                css="mt-3"
                url="/organizations"
                model="organization"
                :selected="form.organization_id"
                @selectedValue="id => form.organization_id = id[0]"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";

export default {
    name: 'navigator-modal',
    components: {
        Modal,
        Select2,
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                title: '',
                organization_id: null,
            }),
        }
    },
}
</script>