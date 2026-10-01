<template>
    <Modal
        model="user"
        modalName="select-users-modal"
        title="global.select_users"
        :intercept-save="true"
        @save="submit()"
    >
        <template #general>
            <table
                id="user-table"
                class="table m-0 border-top-0"
            >
                <thead>
                    <tr class="border-top-0">
                        <th style="width: 0px;"></th>
                        <th>{{ trans('global.users') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users">
                        <td class="text-center">
                            <input
                                :id="'user-' + user.id"
                                class="pointer"
                                :type="multiple ? 'checkbox' : 'radio'"
                                :value="user"
                                v-model="selectedUsers"
                                :aria-describedby="'user-' + user.id"
                            />
                        </td>
                        <td>
                            <label
                                :for="'user-' + user.id"
                                class="font-weight-normal m-0 pointer"
                            >
                                {{ user.firstname }} {{ user.lastname }}
                            </label>
                        </td>
                    </tr>
                </tbody>
            </table>
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';

export default {
    name: 'select-users-modal',
    components: { Modal },
    props: {
        users: {
            type: Array,
            default: null,
        },
        multiple: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            selectedUsers: [],
        }
    },
    methods: {
        submit() {
            this.$eventHub.emit('users-selected', this.selectedUsers);
            this.globalStore.closeModal(this.$options.name);
        },
    },
    watch: {
        multiple() {
            // reset selection when mode changes
            this.selectedUsers = [];
        },
    },
}
</script>