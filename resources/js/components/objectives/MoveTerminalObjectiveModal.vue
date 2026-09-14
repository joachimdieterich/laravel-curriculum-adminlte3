<template>
    <Modal
        model="terminalObjective"
        modalName="move-terminal-objective-modal"
        title="global.terminalObjective.move_to_curriculum"
        :processing="processing"
        :allow-overflow="true"
        @save="submit"
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
            processing: false,
            form: new Form({
                id: null,
                curriculum_id: null,
                objective_type_id: null, // only needed for check in backend
            }),
        }
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                this.processing = false;
                this.form.reset();

                const params = state.modals[this.$options.name].params;
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                }
            }
        });
    },
    methods: {
        submit() {
            this.processing = true;

            axios.patch('/terminalObjectives/' + this.form.id, this.form)
                .then(response => {
                    this.$eventHub.emit('objective-deleted', response.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    console.log(e);
                    this.processing = false;
                    this.toast.error(this.errorMessage(e));
                });
        },
    },
}
</script>