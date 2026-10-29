<template>
    <Modal
        model="subject"
        modalName="subject-modal"
        :form="form"
        :require-title="true"
        :allow-overflow="true"
        :disable-save-button="!form.organization_type_id"
    >
        <template #general-extended>
            <div class="mt-3">
                <input
                    type="text"
                    id="subject-title-short"
                    class="form-control"
                    maxlength="191"
                    v-model.trim="form.title_short"
                    :placeholder="trans('global.subject.fields.title_short')"
                    required
                />
            </div>

            <Select2
                id="subject-organization-type"
                css="mt-3"
                url="/organizationTypes"
                model="organizationType"
                :label="trans('global.organizationType.title_singular') + ' *'"
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
    name: 'subject-modal',
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
                title_short: '',
                organization_type_id: 1,
            }),
        }
    },
}
</script>