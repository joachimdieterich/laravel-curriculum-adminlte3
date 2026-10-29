<template>
    <Modal
        model="grade"
        modalName="grade-modal"
        :form="form"
        :require-title="true"
        :allow-overflow="true"
        :disable-save-button="!form.external_begin || !form.external_end"
    >
        <template #general-extended>
            <div class="mt-3">
                <label for="grade-begin" class="form-label">{{ trans('global.grade.fields.external_begin') }} *</label>
                <input
                    id="grade-begin"
                    type="number"
                    min="1"
                    class="form-control"
                    v-model="form.external_begin"
                    placeholder="external_begin"
                    required
                />
            </div>

            <div class="mt-3">
                <label for="grade-end">{{ trans('global.grade.fields.external_end') }} *</label>
                <input
                    id="grade-end"
                    type="number"
                    min="1"
                    class="form-control"
                    v-model="form.external_end"
                    placeholder="external_end"
                    required
                />
            </div>

            <Select2
                id="grade-organization-type"
                css="mt-3"
                url="/organizationTypes"
                model="organizationType"
                :selected="form.organization_type_id"
                @selectedValue="id => form.organization_type_id = id[0]"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";

export default {
    name: 'grade-modal',
    components: {
        Modal,
        Select2,
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                title: '',
                external_begin: null,
                external_end: null,
                organization_type_id: 1,
            }),
        }
    },
}
</script>