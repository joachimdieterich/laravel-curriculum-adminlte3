<template>
    <Modal
        model="organization"
        modalName="organization-modal"
        :form="form"
        :require-title="true"
    >
        <template #general-extended>
            <div v-if="checkPermission('is_admin')"
                class="mt-3"
            >
                <label for="organization-common-name" class="form-label">
                    {{ trans('global.organization.fields.common_name') }}
                </label>
                <input
                    id="organization-common-name"
                    type="text"
                    class="form-control"
                    v-model="form.common_name"
                    readonly
                    disabled
                />
            </div>

            <div class="mt-3">
                <Editor
                    id="organization-description"
                    licenseKey="gpl"
                    :init="tinyMCE"
                    v-model="form.description"
                />
            </div>

            <div class="mt-3">
                <label for="organization-street" class="form-label">{{ trans('global.organization.fields.street') }}</label>
                <input
                    id="organization-street"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model.trim="form.street"
                    :placeholder="trans('global.organization.fields.street')"
                />
            </div>

            <div class="mt-3">
                <label for="organization-postcode" class="form-label">{{ trans('global.organization.fields.postcode') }}</label>
                <input
                    id="organization-postcode"
                    type="text"
                    class="form-control"
                    v-model.trim="form.postcode"
                    :placeholder="trans('global.organization.fields.postcode')"
                />
            </div>

            <div class="mt-3">
                <label for="organization-city" class="form-label">{{ trans('global.organization.fields.city') }}</label>
                <input
                    id="organization-city"
                    type="text"
                    class="form-control"
                    v-model.trim="form.city"
                    :placeholder="trans('global.organization.fields.city')"
                />
            </div>

            <div class="mt-3">
                <label for="organization-lms" class="form-label">{{ trans('global.organization.fields.lms_url') }}</label>
                <input
                    id="organization-lms"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model.trim="form.lms_url"
                    placeholder="https:\\[your_lms_url]"
                />
            </div>

            <div class="mt-3">
                <label for="organization-phone" class="form-label">{{ trans('global.organization.fields.phone') }}</label>
                <input
                    id="organization-phone"
                    type="text"
                    class="form-control"
                    v-model.trim="form.phone"
                    :placeholder="trans('global.organization.fields.phone')"
                />
            </div>

            <div class="mt-3">
                <label for="organization-email" class="form-label">{{ trans('global.organization.fields.email') }}</label>
                <input
                    id="organization-email"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model.trim="form.email"
                    :placeholder="trans('global.organization.fields.email')"
                />
            </div>

            <Select2
                id="organization-country"
                css="mt-3"
                url="/countries"
                model="country"
                option_id="alpha2"
                option_label="lang_de"
                :selected="form.country_id"
                @selectedValue="id => {
                    form.country_id = id[0];
                    form.state_id = null;
                }"
            />

            <Select2
                id="organization-state"
                css="mt-3"
                :url="'/countries/' + form.country_id + '/states'"
                model="state"
                :term="form.country_id"
                option_id="code"
                option_label="lang_de"
                :selected="form.state_id"
                :readOnly="form.country_id == null"
                @selectedValue="id => form.state_id = id[0]"
            />

            <Select2 v-if="checkPermission('is_admin')"
                id="organization-type"
                css="mt-3"
                url="/organizationTypes"
                model="organizationType"
                :selected="form.organization_type_id"
                @selectedValue="id => form.organization_type_id = id[0]"
            />

            <Select2 v-if="checkPermission('is_admin')"
                id="organization-status-definition"
                css="mt-3"
                url="/statusdefinitions"
                model="status"
                option_id="status_definition_id"
                option_label="lang_de"
                :selected="form.status_id"
                @selectedValue="id => form.status_id = id[0]"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Editor from '@tinymce/tinymce-vue';
import Select2 from "../forms/Select2.vue";

export default {
    name: 'organization-modal',
    components: {
        Modal,
        Editor,
        Select2,
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                common_name: '',
                title: '',
                description: '',
                street: '',
                postcode: '',
                city: '',
                state_id: 'DE-RP',
                country_id: 'DE',
                organization_type_id: 1,
                phone: null,
                email: null,
                status_id: 1,
                lms_url: '',
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink", "link", "lists", "autoresize", "code"
                ],
                {
                    'callback': 'insertContent',
                    'callbackId': this.component_id,
                },
                "bold underline italic | alignleft aligncenter alignright alignjustify | bullist numlist | link code",
                ""
            ),
        }
    },
}
</script>