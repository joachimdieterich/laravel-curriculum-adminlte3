<template>
    <Modal
        model="logbookEntrySubject"
        modalName="logbook-entry-subject-modal"
        title="global.logbookEntry.subject"
        :form="form"
        :allow-overflow="true"
        :intercept-save="true"
        @save="submit()"
    >
        <template #general>
            <Select2
                id="logbook-entry-subject"
                url="/subjects"
                model="subject"
                :selected="form.subject_id"
                @selectedValue="id => form.subject_id = id"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";

export default {
    name: 'logbook-entry-subject-modal',
    components: {
        Modal,
        Select2,
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                subject_id: null,
            }),
        }
    },
    methods: {
        submit() {
            axios.patch('/logbookEntries/' + this.form.id + '/setSubject', this.form)
                .then(response => {
                    this.globalStore.closeModal(this.$options.name);
                    this.$eventHub.emit('update-subject-badge', {
                        entry_id: this.form.id,
                        subject: response.data,
                    });
                })
                .catch(e => {
                    console.log(e);
                    this.processing = false;
                    console.log(e.response);
                });
        },
    },
}
</script>