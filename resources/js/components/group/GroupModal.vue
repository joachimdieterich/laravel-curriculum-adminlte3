<template>
    <Modal
        model="group"
        modalName="group-modal"
        :form="form"
        :allow-overflow="true"
    >
        <template #general>
            <div v-if="checkPermission('is_admin')"
                class="mb-3"
            >
                <label for="group-common-name" class="form-label">{{ trans('global.common_name') }}</label>
                <input
                    id="group-common-name"
                    type="text"
                    class="form-control"
                    v-model="form.common_name"
                    disabled
                    readonly
                />
            </div>

            <div class="mb-3">
                <label for="group-title" class="form-label">{{ trans('global.group.fields.title') }} *</label>
                <input
                    id="group-title"
                    type="text"
                    class="form-control"
                    v-model="form.title"
                    :placeholder="trans('global.title')"
                    required
                />
            </div>

            <Select2
                id="group-grade-id"
                url="/grades"
                model="grade"
                css="mb-3"
                :label="trans('global.grade.title_singular') + ' *'"
                :selected="form.grade_id"
                @selectedValue="id => form.grade_id = id"
            />

            <Select2
                id="group-period-id"
                url="/periods"
                model="period"
                css="mb-3"
                :label="trans('global.period.title_singular') + ' *'"
                :selected="form.period_id"
                @selectedValue="id => form.period_id = id"
            />

            <Select2
                id="group-organization-id"
                url="/organizations"
                model="organization"
                css="mb-0"
                :label="trans('global.organization.title_singular') + ' *'"
                :selected="form.organization_id"
                @selectedValue="id => form.organization_id = id"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";

export default {
    name: 'group-modal',
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
                common_name: null,
                grade_id: '',
                period_id: '',
                organization_id: '',
            }),
        }
    },
}
</script>