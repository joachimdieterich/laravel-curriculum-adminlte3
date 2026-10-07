<template>
    <Modal
        model="user"
        modalName="subscribe-user-modal"
        title="global.user.enrol"
        :allow-overflow="true"
        :disable-save-button="!form.user_id"
        :intercept-save="true"
        @save="submit()"
    >
        <template #general>
            <Select2
                id="user-subscription"
                url="/users"
                model="user"
                @selectedValue="id => form.user_id = id[0]"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";

export default {
    name: 'subscribe-user-modal',
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
                user_id: null,
            }),
        }
    },
    methods: {
        submit() {
            axios.post('/groups/enrol', {
                group_id: this.group_id,
                user_id: this.form.user_id,
            })
                .then(r => {
                    this.globalStore.closeModal(this.$options.name);
                    this.$eventHub.emit('user-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                    this.toast.error(this.errorMessage(e));
                });
        },
    },
}
</script>