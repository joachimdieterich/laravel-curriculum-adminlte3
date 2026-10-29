<template>
    <Modal
        model="kanbanStatus"
        modalName="kanban-status-modal"
        url="/kanbanStatuses"
        :form="form"
        :require-title="true"
        :show-display-section="true"
        :allow-overflow="true"
        :show-permission-section="hasPermissionsAccess"
    >
        <template #permissions>
            <Switch
                id="kanban-status-movable"
                class="mb-2"
                label="global.movable"
                v-model="form.movable"
            />

            <Switch
                id="kanban-status-editable"
                class="mb-2"
                label="global.editable"
                v-model="form.editable"
            />

            <Switch
                id="kanban-status-visibility"
                label="global.visible"
                v-model="form.visibility"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Switch from '../forms/Switch.vue';
import Form from 'form-backend-validation';

export default {
    name: 'kanban-status-modal',
    components: {
        Modal,
        Switch,
    },
    props: {
        kanban: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: '',
                title: '',
                kanban_id: this.kanban.id,
                owner_id: null,
                movable: true, // replaces 'locked' to match shown translation
                locked: false, // actual value that gets sent to backend
                editable: true,
                visibility: true,
                visible_from: null,
                visible_until: null,
                color: '#f4f4f4',
            }),
        }
    },
    computed: {
        hasPermissionsAccess() {
            return !this.form.id
                || this.form.owner_id == this.$userId
                || this.kanban.owner_id == this.$userId
                || this.checkPermission('is_admin');
        },
    },
}
</script>