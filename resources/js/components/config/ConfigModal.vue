<template>
    <Modal
        model="config"
        modalName="config-modal"
        :form="form"
        :disable-save-button="!form.key || !form.value || !form.data_type"
    >
        <template #general>
            <div class="form-floating mb-4">
                <input
                    id="config-key"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model.trim="form.key"
                    placeholder=""
                    required
                />
                <label for="config-key">{{ trans('global.config.fields.key') }} *</label>
            </div>

            <div class="form-floating mb-4">
                <textarea
                    id="config-value"
                    class="form-control"
                    rows="3"
                    v-model.trim="form.value"
                    placeholder=""
                ></textarea>
                <label for="config-value">{{ trans('global.config.fields.value') }} *</label>
            </div>

            <div class="form-floating mb-4">
                <input
                    id="config-referenceable-type"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model.trim="form.referenceable_type"
                    placeholder=""
                    required
                />
                <label for="config-referenceable-type">{{ trans('global.config.fields.referenceable_type') }}</label>
            </div>

            <div class="form-floating mb-4">
                <input
                    id="config-referenceable-id"
                    type="number"
                    min="1"
                    class="form-control"
                    v-model="form.referenceable_id"
                    placeholder=""
                    required
                />
                <label for="config-referenceable-id">{{ trans('global.config.fields.referenceable_id') }}</label>
            </div>

            <div class="form-floating">
                <input
                    id="config-data-type"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model.trim="form.data_type"
                    placeholder=""
                    required
                />
                <label for="config-data-type">{{ trans('global.config.fields.data_type') }} *</label>
            </div>
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';

export default {
    name: 'config-modal',
    components: { Modal },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                key: '',
                value: '',
                referenceable_type: '',
                referenceable_id: '',
                data_type: '',
            }),
        }
    },
}
</script>