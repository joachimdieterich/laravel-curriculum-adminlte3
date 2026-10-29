<template>
    <Modal
        model="organizationType"
        modalName="organizationtype-modal"
        :form="form"
        :allow-overflow="true"
        :require-title="true"
        :disable-save-button="!form.external_id"
    >
        <template #general-extended>
            <div class="mt-3">
                <input
                    id="organization-type-external-id"
                    type="number"
                    min="0"
                    class="form-control"
                    v-model="form.external_id"
                    :placeholder="trans('global.organizationType.fields.external_id') + ' *'"
                    required
                />
            </div>

            <Select2
                id="organization-type-country"
                css="mt-3"
                option_id="alpha2"
                option_label="lang_de"
                :label="trans('global.country.title_singular') + ' *'"
                url="/countries"
                model="country"
                :selected="form.country_id"
                @selectedValue="id => {
                    this.form.country_id = id[0];
                    this.form.state_id = '';
                }"
            />

            <Select2
                id="organization-type-state"
                css="mt-3"
                option_id="code"
                option_label="lang_de"
                :url="'/countries/' + form.country_id + '/states/'"
                model="state"
                :term="form.country_id"
                :selected="form.state_id"
                @selectedValue="id => form.state_id = id[0]"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";

export default {
    name: 'organizationtype-modal',
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
                external_id: null,
                country_id: 'DE',
                state_id: 'DE-RP',
            }),
        }
    },
}
</script>