<template>
    <Modal
        model="videoconference"
        modalName="subscribe-videoconference-modal"
        title="global.videoconference.enrol"
        :form="form"
        :allow-overflow="true"
        :disable-save-button="!form.videoconference_id"
        :intercept-save="true"
        @save="submit()"
    >
        <template #general>
            <Select2
                id="videoconference-subscription"
                url="/videoconferences"
                model="videoconference"
                @selectedValue="id => form.videoconference_id = id[0]"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";

export default {
    name: 'subscribe-videoconference-modal',
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
                videoconference_id: null,
            }),
        }
    },
    methods: {
        submit() {
            axios.post('/videoconferenceSubscriptions', {
                videoconference_id: this.form.videoconference_id,
                subscribable_type: 'App\\Group',
                subscribable_id: this.group_id,
            })
            .then(r => {
                this.$eventHub.emit('videoconference-subscription-added', r.data);
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