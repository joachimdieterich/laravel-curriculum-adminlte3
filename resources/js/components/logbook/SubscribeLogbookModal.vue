<template>
    <Modal
        model="logbook"
        modalName="subscribe-logbook-modal"
        title="global.logbook.enrol"
        :form="form"
        :allow-overflow="true"
        :disable-save-button="!form.logbook_id"
        :intercept-save="true"
        @save="submit()"
    >
        <template #general>
            <Select2
                id="logbook-subscription"
                url="/logbooks"
                model="logbook"
                @selectedValue="id => form.logbook_id = id[0]"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";

export default {
    name: 'subscribe-logbook-modal',
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
                logbook_id: null,
            }),
        }
    },
    methods: {
        submit() {
            axios.post('/logbookSubscriptions', {
                logbook_id: this.form.logbook_id,
                subscribable_type: 'App\\Group',
                subscribable_id: this.group_id,
            })
            .then(response => {
                this.$eventHub.emit('logbook-subscription-added', response.data);
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