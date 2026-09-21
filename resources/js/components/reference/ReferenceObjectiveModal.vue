<template>
    <Modal
        model="reference"
        modalName="reference-objective-modal"
        url="/referenceSubscriptions"
        title="global.referenceable_types.objective"
        :allow-overflow="true"
        :disable-save-button="!form.terminal_objective_id"
    >
        <template #general>
            <Select2
                id="select-reference-curriculum"
                css="mb-3"
                url="/curricula"
                model="curriculum"
                @selectedValue="id => {
                    form.curriculum_id = id;
                    form.terminal_objective_id = null;
                }"
            />

            <Select2 v-if="form.curriculum_id"
                id="select-reference-terminal"
                css="mb-3"
                :url="'/curricula/' + form.curriculum_id + '/terminalObjectives'"
                model="terminalObjective"
                @selectedValue="id => {
                    form.terminal_objective_id = id;
                    form.enabling_objective_id = null;
                }"
            />

            <Select2 v-if="form.terminal_objective_id"
                id="select-reference-enabling"
                css="mb-3"
                :url="'/terminalObjectives/' + form.terminal_objective_id + '/enablingObjectives'"
                model="enablingObjective"
                @selectedValue="id => form.enabling_objective_id = id"
            />

            <textarea
                id="reference-description"
                class="form-control"
                style="max-height: 35svh;"
                rows="4"
                :placeholder="trans('global.description')"
                v-model.trim="form.description"
            ></textarea>
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";

export default {
    name: 'reference-objective-modal',
    components: {
        Modal,
        Select2,
    },
    props: {
        params: {
            type: Object,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                subscribable_type: null,
                subscribable_id: null,
                curriculum_id: null,
                terminal_objective_id: null,
                enabling_objective_id: null,
                description: '',
            }),
        }
    },
}
</script>