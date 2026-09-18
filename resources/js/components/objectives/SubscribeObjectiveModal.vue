<template>
    <Modal
        model="subscribe-objective"
        modalName="subscribe-objective-modal"
        title="global.referenceable_types.link"
        :processing="processing"
        :allow-overflow="true"
        :disable-save-button="form.terminal_objective_id.length === 0"
        @save="submit()"
    >
        <template #general>
            <Select2
                id="curriculum_id"
                css="mb-3"
                url="/curricula"
                model="curriculum"
                @selectedValue="id => {
                    form.curriculum_id = id[0];
                    $refs.terminal.deselectAll();
                }"
            />

            <Select2 v-if="form.curriculum_id"
                ref="terminal"
                id="terminalObjectives_id"
                css="mb-3"
                :url="'/curricula/' + form.curriculum_id + '/terminalObjectives'"
                model="terminalObjective"
                :multiple="true"
                @cleared="() => {
                    form.terminal_objective_id = [];
                    $refs.enabling.deselectAll();
                }"
                @selectedValue="id => {
                    form.terminal_objective_id = id;
                    $refs.enabling.deselectAll();
                }"
            />

            <Select2 v-if="form.terminal_objective_id.length === 1"
                ref="enabling"
                id="enablingObjectives_id"
                :url="'/terminalObjectives/' + form.terminal_objective_id[0] + '/enablingObjectives'"
                model="enablingObjective"
                :multiple="true"
                @cleared="form.enabling_objective_id = []"
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
    name: 'subscribe-objective-modal',
    components: {
        Modal,
        Select2,
    },
    props: {
        users: {
            type: Array,
            default: null,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            processing: false,
            form: new Form({
                id: null,
                subscribable_type: null,
                subscribable_id: null,
                curriculum_id: null,
                terminal_objective_id: [],
                enabling_objective_id: [],
            }),
        }
    },
    methods: {
        submit() {
            this.processing = true;
            const type = this.form.enabling_objective_id.length === 0 ? 'terminal' : 'enabling';

            axios.post('/' + type + 'ObjectiveSubscriptions', {
                terminal_objective_id:    this.form.terminal_objective_id,
                enabling_objective_id:    this.form.enabling_objective_id, // will be discarded in terminal-controller
                subscribable_type:        this.form.subscribable_type,
                subscribable_id:          this.form.subscribable_id,
                users:                    this.users?.map(user => user.id) ?? [this.$userId], // needed to return their previous set achievements
            })
            .then(response => {
                this.$eventHub.emit('subscriptions-added', {
                    terminal_objectives: response.data[0],
                    id: this.form.subscribable_id,
                });
                this.globalStore.closeModal(this.$options.name);
            })
            .catch(e => {
                console.log(e);
                this.processing = false;
                this.toast.error(this.errorMessage(e));
            });
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                this.processing = false,
                this.form.reset();

                const params = state.modals[this.$options.name].params;
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                }
            }
        });
    },
}
</script>