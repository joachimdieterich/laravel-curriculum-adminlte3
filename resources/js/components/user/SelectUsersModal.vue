<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @mouseup.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">{{ trans(title) }}</h3>
                    <div class="card-tools">
                        <button
                            type="button"
                            class="btn btn-tool"
                            @click="globalStore.closeModal($options.name)"
                        >
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <table
                                id="user-table"
                                class="table m-0 border-top-0"
                            >
                                <thead>
                                    <tr class="border-top-0">
                                        <th style="width: 0px;"></th>
                                        <th>{{ trans('users') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user in users">
                                        <td class="text-center">
                                            <input
                                                :id="'user_' + user.id"
                                                name="user"
                                                class="pointer"
                                                :type="multiple ? 'checkbox' : 'radio'"
                                                :value="user"
                                                v-model="selectedUsers"
                                                :aria-describedby="'user-' + user.id"
                                            />
                                        </td>
                                        <td :id="'user-' + user.id">
                                            <label
                                                :for="'user_' + user.id"
                                                class="font-weight-normal m-0 pointer"
                                            >
                                                {{ user.firstname }} {{ user.lastname }}
                                            </label>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            type="button"
                            class="btn btn-default"
                            @click="globalStore.closeModal($options.name)"
                        >
                            {{ trans('global.close') }}
                        </button>
                        <button
                            class="btn btn-primary ml-3"
                            @click="submit()"
                            :disabled="selectedUsers.length === 0"
                        >
                            {{ trans(submitText) }}
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </Transition>
</template>
<script>
import {useGlobalStore} from "../../store/global";

export default {
    name: 'select-users-modal',
    props: {
        users: {
            type: Array,
            default: null,
        },
        multiple: {
            type: Boolean,
            default: false,
        },
        title: { // needs to be a translation-string
            type: String,
            default: 'global.select_users',
        },
        submitText: {
            type: String,
            default: 'global.save',
        }
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
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
    mounted() {
        this.globalStore.registerModal(this.$options.name);
    },
}
</script>