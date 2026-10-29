<template>
    <Modal
        model="user"
        modalName="user-modal"
        :form="form"
        :disable-save-button="!form.username || !form.firstname || !form.lastname || !form.email || (!form.id && !form.password)"
    >
        <template #general>
            <div v-if="checkPermission('is_admin')"
                class="mb-3"
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

            <div class="mb-3">
                <label for="username" class="form-label">{{ trans('global.user.fields.username') }} *</label>
                <input
                    id="username"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model="form.username"
                    :placeholder="trans('global.user.fields.username')"
                    required
                />
            </div>

            <div class="mb-3">
                <label for="user-firstname" class="form-label">{{ trans('global.user.fields.firstname') }} *</label>
                <input
                    id="user-firstname"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model="form.firstname"
                    :placeholder="trans('global.user.fields.firstname')"
                    required
                />
            </div>

            <div class="mb-3">
                <label for="user-lastname" class="form-label">{{ trans('global.user.fields.lastname') }} *</label>
                <input
                    id="user-lastname"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model="form.lastname"
                    :placeholder="trans('global.user.fields.lastname')"
                    required
                />
            </div>

            <div>
                <label for="user-email" class="form-label">{{ trans('global.user.fields.email') }} *</label>
                <input
                    id="user-email"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model="form.email"
                    :placeholder="trans('global.user.fields.email')"
                    required
                />
            </div>

            <div v-if="!form.id"
                class="mt-3"
            >
                <label for="user-password" class="form-label">{{ trans('global.user.fields.password') }} *</label>
                <input
                    id="user-password"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model="form.password"
                    :placeholder="trans('global.user.fields.password')"
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