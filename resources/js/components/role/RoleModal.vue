<template>
    <Modal
        model="role"
        modalName="role-modal"
        :form="form"
        :allow-overflow="true"
        :require-title="true"
        @opened="params => preProcessing(params)"
    >
        <template #general-extended>
            <Select2
                id="role-permissions"
                css="mt-3"
                url="/permissions"
                model="permission"
                :multiple="true"
                :selected="selectedPermissions"
                @selectedValue="permissions => form.permissions = permissions"
                @cleared="form.permissions = []"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";

export default {
    name: 'role-modal',
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
                permissions: [],
            }),
            selectedPermissions: []
        }
    },
    methods: {
        preProcessing(params) {
            this.selectedPermissions = params.permissions?.map(p => p.id);
        },
    },
}
</script>