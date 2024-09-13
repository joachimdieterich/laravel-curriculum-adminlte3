<template>
    <modal
        id="select-users-modal"
        name="select-users-modal"
        height="60%"
        width="20%"
        :minWidth="300"
        :minHeight="420"
        :adaptive=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen"
        @opened="opened()"
        @before-close="beforeClose()"
        style="z-index: 1100"
    >
        <div class="card"
            style="margin-bottom: 0px !important; height: 100%;"
        >
            <div class="card-header">
                <h3 class="card-title">{{ trans(title) }}</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool draggable">
                        <i class="fa fa-arrows-alt"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-widget="remove" @click="close()">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="card-body overflow-auto">
                <table
                    id="user-table"
                    class="table m-0 border-top-0"
                    v-permission="'achievement_access'"
                >
                    <thead>
                        <tr class="border-top-0">
                            <th style="width: 0px;"></th>
                            <th>{{ trans('users') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users">
                            <td>
                                <input v-if="multiple === false"
                                    type="radio"
                                    name="user"
                                    :value="user"
                                    v-model="selectedUsers"
                                    :aria-describedby="'user-' + user.id"
                                />
                                <input v-else
                                    type="checkbox"
                                    :value="user"
                                    v-model="selectedUsers"
                                    :aria-describedby="'user-' + user.id"
                                />
                            </td>
                            <td :id="'user-' + user.id">{{ user.firstname }} {{ user.lastname }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                <span class="d-flex justify-content-between">
                    <button type="button" class="btn btn-default" data-widget="remove" @click="close()">{{ trans('global.close') }}</button>
                    <button class="btn btn-primary" @click="submit()" :disabled="selectedUsers.length === 0">{{ trans(submitText) }}</button>
                </span>
            </div>
        </div>
    </modal>
</template>
<script>
export default {
    props: {
        users: [],
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
    data() {
        return {
            selectedUsers: [],
        }
    },
    methods: {
        close() {
            this.$modal.hide('select-users-modal');
        },
        submit() {
            this.$parent.handleUserModalClose(this.selectedUsers);
            this.close();
        },
        closed() {},
        beforeClose() {},
        opened() {},
        beforeOpen() {},
    }
}
</script>