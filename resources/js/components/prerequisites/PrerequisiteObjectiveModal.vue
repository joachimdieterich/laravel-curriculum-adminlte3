<template>
    <Modal
        model="prerequisite"
        modalName="prerequisite-objective-modal"
        title="global.prerequisite.create"
        :form="form"
        :allow-overflow="true"
    >
        <template #general>
            <Select2 v-if="!form.id"
                id="select-prerequisite-curriculum"
                css="mb-3"
                url="/curricula"
                model="curriculum"
                @selectedValue="id => form.curriculum_id = id"
            />

            <Select2 v-if="form.curriculum_id"
                id="terminalObjectives_id"
                css="mb-3"
                :url="'/curricula/' + form.curriculum_id + '/terminalObjectives'"
                model="terminalObjective"
                @selectedValue="id => form.terminal_objective_id = id"
            />

            <Select2 v-if="form.terminal_objective_id"
                id="enablingObjectives_id"
                css="mb-3"
                :url="'/terminalObjectives/' + this.form.terminal_objective_id + '/enablingObjectives'"
                model="enablingObjective"
                @selectedValue="id => form.enabling_objective_id = id"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";

export default {
    name: 'prerequisite-objective-modal',
    components: {
        Modal,
        Select2,
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                successor_type: null,
                successor_id: null,
                curriculum_id: null,
                terminal_objective_id: null,
                enabling_objective_id: null,
            }),
        }
    },
}
</script>