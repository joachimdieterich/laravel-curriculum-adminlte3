<template>
    <Modal
        model="config"
        modalName="config-modal"
        :form="form"
        :disable-save-button="!form.key || !form.value || !form.data_type"
    >
        <template #general>
            <div class="mb-3">
                <label for="config-key" class="form-label">{{ trans('global.config.fields.key') }} *</label>
                <input
                    id="config-key"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model.trim="form.key"
                    placeholder="Title"
                    required
                />
            </div>

            <div class="mb-3">
                <label for="config-value" class="form-label">{{ trans('global.config.fields.value') }} *</label>
                <textarea
                    id="config-value"
                    class="form-control"
                    rows="3"
                    v-model.trim="form.value"
                ></textarea>
            </div>

            <div class="mb-3">
                <label for="config-referenceable-type" class="form-label">
                    {{ trans('global.config.fields.referenceable_type') }}
                </label>
                <input
                    id="config-referenceable-type"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model.trim="form.referenceable_type"
                    required
                />
            </div>

            <div class="mb-3">
                <label for="config-referenceable-id" class="form-label">
                    {{ trans('global.config.fields.referenceable_id') }}
                </label>
                <input
                    id="config-referenceable-id"
                    type="number"
                    min="1"
                    class="form-control"
                    v-model="form.referenceable_id"
                    required
                />
            </div>

            <div>
                <label for="config-data-type" class="form-label">{{ trans('global.config.fields.data_type') }} *</label>
                <input
                    id="config-data-type"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model.trim="form.data_type"
                    required
                />
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