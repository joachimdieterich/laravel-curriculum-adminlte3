<template>
    <Modal
        model="plan"
        modalName="plan-modal"
        :form="form"
        :show-display-section="true"
        :show-permission-section="true"
        :disable-save-button="!form.title"
    >
        <template #general>
            <Select2 v-if="false"
                id="plan-type"
                css="mb-3"
                model="PlanType"
                url="/planTypes"
                :label="trans('global.plan.fields.type') + ' *'"
                :readOnly="true || (method == 'patch')"
                :selected="form.type_id"
                @selectedValue="id => form.type_id = id"
            />

            <input
                id="plan-title"
                type="text"
                class="form-control mb-3"
                maxlength="191"
                v-model.trim="form.title"
                :placeholder="trans('global.title') + ' *'"
                required
            />

            <Editor
                id="plan-description"
                licenseKey="gpl"
                :init="tinyMCE"
                v-model="form.description"
            />

            <Select2 v-if="form.id && checkPermission('is_admin')"
                id="plan-owner"
                css="mt-3"
                model="User"
                url="/users"
                :label="trans('global.change_owner')"
                :selected="form.owner_id"
                @selectedValue="id => form.owner_id = id[0]"
            />
        </template>
        <template #permissions>
            <Switch
                id="plan-allow-copy"
                label="global.plan.allow_copy"
                v-model="form.allow_copy"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Editor from '@tinymce/tinymce-vue';
import Select2 from '../forms/Select2.vue';
import Switch from '../forms/Switch.vue';

export default {
    name: 'plan-modal',
    components: {
        Modal,
        Editor,
        Select2,
        Switch,
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                type_id: 4, // currently only one type exists
                title:  '',
                description:  '',
                owner_id: null,
                // these fields exist in the DB but aren't currently in use
                // date: null,
                // begin: '',
                // end: '',
                // duration: '',
                color: '#27AF60',
                medium_id: null,
                allow_copy: true,
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink", "link", "lists", "code", "autoresize",
                ],
                {
                    'callback': 'insertContent',
                    'callbackId': this.component_id
                },
                "bold underline italic | alignleft aligncenter alignright alignjustify | bullist numlist | link code",
                ""
            ),
        }
    },
}
</script>