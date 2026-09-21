<template>
    <Modal
        model="terminalObjective"
        modalName="move-terminal-objective-modal"
        title="global.terminalObjective.move_to_curriculum"
        :form="form"
        :allow-overflow="true"
        :intercept-save="true"
        @save="submit()"
    >
        <template #general>
            <Select2
                id="curriculum_id"
                name="curriculum_id"
                url="/curricula?owner"
                model="curriculum"
                css="mb-1"
                :label="trans('global.curriculum.title_singular') + ' *'"
                :selected="form.curriculum_id"
                @selectedValue="id => form.curriculum_id = id[0]"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";

export default {
    name: 'move-terminal-objective-modal',
    components: {
        Modal,
        Select2,
    },
    data() {
        return {
            form: new Form({
                id: null,
                curriculum_id: null,
                objective_type_id: null, // only needed for check in backend
            }),
        }
    },
    methods: {
        submit() {
            axios.patch('/terminalObjectives/' + this.form.id, this.form)
                .then(response => {
                    this.$eventHub.emit('objective-deleted', response.data);
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