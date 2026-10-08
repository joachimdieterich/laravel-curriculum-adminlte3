<template>
    <Modal
        model="plan"
        modalName="subscribe-plan-modal"
        title="global.plan.enrol"
        :form="form"
        :allow-overflow="true"
        :disable-save-button="!form.plan_id"
        :intercept-save="true"
        @save="submit()"
    >
        <template #general>
            <Select2
                id="plan-subscription"
                url="/plans"
                model="plan"
                @selectedValue="id => form.plan_id = id[0]"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";

export default {
    name: 'subscribe-plan-modal',
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
                plan_id: null,
            }),
        }
    },
    methods: {
        submit() {
            axios.post('/planSubscriptions', {
                plan_id: this.form.plan_id,
                subscribable_type: 'App\\Group',
                subscribable_id: this.group_id
            })
            .then(r => {
                this.$eventHub.emit('plan-subscription-added', r.data);
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