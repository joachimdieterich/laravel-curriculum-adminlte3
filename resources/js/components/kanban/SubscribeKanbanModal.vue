<template>
    <Modal
        model="kanban"
        modalName="subscribe-kanban-modal"
        title="global.kanban.enrol"
        :form="form"
        :allow-overflow="true"
        :disable-save-button="!form.kanban_id"
        :intercept-save="true"
        @save="submit()"
    >
        <template #general>
            <Select2
                id="kanban-subscription"
                url="/kanbans"
                model="kanban"
                @selectedValue="id => form.kanban_id = id[0]"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";

export default {
    name: 'subscribe-kanban-modal',
    components: {
        Modal,
        Select2,
    },
    props: {
        group_id: {
            type: Number,
            default: null,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                kanban_id: null,
            }),
        }
    },
    methods: {
        submit() {
            axios.post('/kanbanSubscriptions', {
                kanban_id: this.form.kanban_id,
                subscribable_type: 'App\\Group',
                subscribable_id: this.group_id,
            })
            .then(response => {
                this.$eventHub.emit('kanban-subscription-added', response.data);
                this.globalStore.closeModal(this.$options.name);
            })
            .catch(e => {
                console.log(e);
                this.toast.error(this.errorMessage(e));
            });
        },
    },
}
</script>