<template>
    <Modal
        model="user"
        modalName="user-modal"
        :form="form"
        :disable-save-button="!form.username || !form.firstname || !form.lastname || !form.email || (!form.id && !form.password)"
    >
        <template #general>
            <div v-if="checkPermission('is_admin')"
                class="mb-4"
            >
                <label for="user-common-name" class="form-label">{{ trans('global.common_name') }}</label>
                <input
                    id="user-common-name"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model="form.common_name"
                    readonly
                    disabled
                />
            </div>

            <div class="form-floating mb-4">
                <input
                    id="username"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model="form.username"
                    placeholder=""
                    required
                />
                <label for="username">{{ trans('global.user.fields.username') }} *</label>
            </div>

            <div class="form-floating mb-4">
                <input
                    id="user-firstname"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model="form.firstname"
                    placeholder=""
                    required
                />
                <label for="user-firstname">{{ trans('global.user.fields.firstname') }} *</label>
            </div>

            <div class="form-floating mb-4">
                <input
                    id="user-lastname"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model="form.lastname"
                    placeholder=""
                    required
                />
                <label for="user-lastname">{{ trans('global.user.fields.lastname') }} *</label>
            </div>

            <div class="form-floating">
                <input
                    id="user-email"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model="form.email"
                    placeholder=""
                    required
                />
                <label for="user-email">{{ trans('global.user.fields.email') }} *</label>
            </div>

            <div v-if="!form.id"
                class="form-floating mt-4"
            >
                <input
                    id="user-password"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model="form.password"
                    placeholder=""
                    required
                />
                <label for="user-password">{{ trans('global.user.fields.password') }} *</label>
            </div>
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';

export default {
    name: 'user-modal',
    components: { Modal },
    props: {
        params: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                username: '',
                firstname: '',
                lastname: '',
                common_name: '',
                email: '',
                password: '',
            }),
        }
    },
}
</script>