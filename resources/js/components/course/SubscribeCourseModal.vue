<template>
    <Modal
        model="course"
        modalName="subscribe-course-modal"
        title="global.course.enrol"
        :form="form"
        :allow-overflow="true"
        :disable-save-button="!form.model_id"
        :intercept-save="true"
        @save="submit()"
    >
        <template #general>
            <Select2
                id="course-subscription"
                url="/curricula"
                model="curriculum"
                @selectedValue="id => form.model_id = id[0]"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";

export default {
    name: 'subscribe-course-modal',
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
                model_id: null,
            }),
        }
    },
    methods: {
        submit() {
            axios.post('/curricula/enrol', {
                group_id: this.group_id,
                model_id: this.form.model_id,
            })
                .then(r => {
                    this.$eventHub.emit('course-added', r.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    console.log(e.response);
                    this.toast.error(this.errorMessage(e));
                });
        },
    },
}
</script>