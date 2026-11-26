<template>
    <Modal
        model="tag"
        modalName="tag-modal"
        :form="form"
        :disable-save-button="!form.name"
        @opened="preProcessing($event)"
    >
        <template #general>
            <div class="form-floating">
                <input
                    id="tag-name"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model.trim="form.name"
                    placeholder=""
                    required
                />
                <label for="tag-name">Name *</label>
            </div>
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';

export default {
    name: 'tag-modal',
    components: { Modal },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                name: '',
            }),
        }
    },
    methods: {
        preProcessing(params) {
            this.form.name = params.translation;
        },
    },
}
</script>