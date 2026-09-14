<template>
    <Modal
        ref="modal"
        model="kanbanStatus"
        modalName="kanban-status-modal"
        :method="method"
        :processing="processing"
        :require-title="true"
        :show-display-section="true"
        :allow-overflow="true"
        :show-permission-section="hasPermissionsAccess"
        @save="form => submit(form)"
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
            method: 'post',
            processing: false,
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
    methods: {
        submit(formData) {
            this.form.populate(formData);
            this.form.locked = !this.form.movable;
            this.processing = true;

            if (this.method == 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            axios.post('/kanbanStatuses', this.form)
                .then(r => {
                    this.$eventHub.emit('kanban-status-created', r.data);
                    this.globalStore?.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.toast.error(this.errorMessage(e));
                    console.log(e);
                });
        },
        update() {
            axios.patch('/kanbanStatuses/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('kanban-status-updated', r.data);
                    this.globalStore?.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.toast.error(this.errorMessage(e));
                });
        },
    },
    computed: {
        hasPermissionsAccess() {
            return this.method == 'post'
                || this.form.owner_id == this.$userId
                || this.kanban.owner_id == this.$userId
                || this.checkPermission('is_admin');
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                this.processing = false;
                this.form.reset();

                const params = state.modals[this.$options.name].params;
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params.status);
                    this.method = params.method;
                    this.form.movable = !this.form.locked;
                }

                this.$refs.modal.resetForm(this.form);
            }
        });
    },
}
</script>