<template>
    <div class="d-flex flex-column">
        <div
            id="user-content"
            class="px-3"
        >
            <div v-if="adminTools" class="d-flex">
                <Switch
                    id="user-toggle-view"
                    label="Tabellenansicht"
                    v-model="toggleView"
                />
                <button v-if="!toggleView"
                    type="button"
                    class="btn ms-auto"
                    :class="btnClass"
                    @click="setMode()"
                >
                    {{ trans('global.select') }}
                </button>
            </div>

            <div :class="toggleView && 'd-none'">
                <IndexWidget v-if="!subscribable"
                    v-permission="'user_create'"
                    key="userCreate"
                    modelName="User"
                    url="/users"
                    :create="true"
                    :subscribe="false"
                    :subscribable_id="subscribable_id"
                    :label="trans('global.user.' + createLabel)"
                />
                <IndexWidget v-else="subscribable"
                    v-permission="'group_enrolment'"
                    key="userSubscribe"
                    modelName="User"
                    url="/users"
                    :create="false"
                    :subscribe="true"
                    :subscribable_id="subscribable_id"
                    :label="trans('global.user.' + createLabel)"
                >
                    <template #itemIcon>
                        <i v-if="subscribable"
                            class="fa fa-2x fa-link text-muted"
                        ></i>
                    </template>
                </IndexWidget>
                <IndexWidget v-for="user in users"
                    :key="'userIndex' + user.id"
                    :model="user"
                    modelName="User"
                    storeTitle="users"
                    url="/users"
                    :showSubscribable="subscribable"
                >
                    <template #icon>
                        <i class="fas fa-user"></i>
                    </template>

                    <template #dropdown>
                        <div
                            class="dropdown-menu dropdown-menu-end"
                            style="z-index: 1050;"
                            x-placement="left-start"
                        >
                            <div v-if="!subscribable"
                                v-permission="'user_edit, user_delete'"
                            >
                                <button
                                    v-permission="'user_edit'"
                                    :name="'edit-user-' + user.id"
                                    class="dropdown-item text-secondary"
                                    @click.prevent="editUser(user)"
                                >
                                    <i class="fa fa-pencil-alt me-2"></i>
                                    {{ trans('global.user.edit') }}
                                </button>
                                <hr class="my-1"/>
                                <button
                                    v-permission="'user_delete'"
                                    :id="'delete-user-' + user.id"
                                    type="submit"
                                    class="dropdown-item py-1 text-red"
                                    @click.prevent="confirmItemDelete(user)"
                                >
                                    <span>
                                        <i class="fa fa-trash me-2"></i>
                                        {{ trans('global.user.delete') }}
                                    </span>
                                </button>
                            </div>

                            <div v-else
                                v-permission="'group_enrolment'"
                            >
                                <button
                                    :id="'delete-user-' + user.id"
                                    type="submit"
                                    class="dropdown-item py-1 text-red"
                                    @click.prevent="confirmItemDelete(user)"
                                >
                                    <span>
                                        <i class="fa fa-unlink me-2"></i>
                                        {{ trans('global.user.expel') }}
                                    </span>
                                </button>
                            </div>
                        </div>
                    </template>

                    <template #content>
                        <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                            <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                                {{ user.firstname }} {{ user.lastname }}
                            </h1>
                            <p v-if="adminTools" class="text-muted small">
                                {{ user.username }} </br>
                                {{ user.email }} </br>
                                {{ user.common_name }}
                            </p>
                        </span>
                    </template>
                </IndexWidget>
            </div>
        </div>

        <DataTable
            ref="datatable"
            id="user-datatable"
            :columns="columns"
            :options="options"
            :ajax="subscribable ? '/users/list?group_id=' + subscribable_id : '/users/list'"
            class="d-none"
            @xhr="(e, settings, json) => users = json.data"
        />

        <UserOptions v-if="!subscribable"/>

        <Teleport to="body">
            <UserModal v-if="!subscribable"/>
            <SubscribeUserModal v-if="subscribable"/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.user.' + deleteLabel)"
                :description="trans('global.user.' + deleteLabel + '_helper')"
                @close="showConfirm = false"
                @confirm="() => {
                    showConfirm = false;
                    destroy();
                }"
            />
        </Teleport>
    </div>
</template>
<script>
import SubscribeUserModal from "./SubscribeUserModal.vue";
import UserModal from "../user/UserModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import 'datatables.net-select-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import { useDatatableStore } from "../../store/datatables";
import UserOptions from "./UserOptions.vue";
import Switch from "../forms/Switch.vue";
DataTable.use(DataTablesCore);

export default {
    components: {
        UserOptions,
        ConfirmModal,
        DataTable,
        UserModal,
        IndexWidget,
        SubscribeUserModal,
        Switch,
    },
    props: {
        subscribable: {
            type: Boolean,
            default: false,
        },
        subscribable_type: {
            type: String,
            default: null,
        },
        subscribable_id: {
            type: Number,
            default: null,
        },
    },
    setup() {
        return { store: useDatatableStore() }
    },
    data() {
        return {
            component_id: this.$.uid,
            users: null,
            showConfirm: false,
            adminTools: false,
            toggleView: false,
            currentUser: {},
            columns: [
                { title: 'ID', data: 'id', searchable: false },
                { title: 'common_name', data: 'common_name', searchable: true },
                { title: 'username', name: 'username', data: 'username', searchable: true },
                { title: 'firstname', name: 'firstname', data: 'firstname', searchable: true },
                { title: 'lastname', name: 'lastname', data: 'lastname', searchable: true },
                { title: 'E-Mail', data: 'email', searchable: true },
            ],
            options: this.$dtOptions,
            dt: null,
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;
        this.adminTools = !this.subscribable && this.checkPermission('is_admin');

        this.dt = this.$refs.datatable.dt;
        this.dt.order([4, 'asc'], [3, 'asc']); // sort by lastname, firstname

        this.dt.on('select deselect', (e, dt) => {
            if (!this.toggleView) return; // stop event if not visible
            // add datatable to store if not exists
            if (!this.store.getDatatable('users')?.select) {
                this.store.addToDatatables({
                    datatable: 'users',
                    select: true,
                    selectedItems: [],
                });
            }

            this.store.addSelectItems('users', this.users[dt[0][0]]);
        });

        this.$eventHub.on('user-added', user => {
            this.users.push(user);
        });

        this.$eventHub.on('user-updated', updatedUser => {
            const user = this.users.find(u => u.id === updatedUser.id);
            Object.assign(user, updatedUser);
        });

        this.$eventHub.on('filter', filter => {
            this.dt.search(filter.searchString).draw();
        });
    },
    methods: {
        setMode() {
            this.dt.rows('.selected').deselect();

            // toggle selection on/off
            const toggle = !this.store.getDatatable('users')?.select;
            this.store.addToDatatables({
                datatable: 'users',
                select: toggle,
                selectedItems: [],
            });
        },
        editUser(user) {
            this.globalStore.showModal('user-modal', user);
        },
        confirmItemDelete(user) {
            this.currentUser = user;
            this.showConfirm = true;
        },
        destroy() {
            if (this.subscribable) {
                axios.delete('/groups/expel', {
                    data: {
                        expel_list: {
                            0: {
                                group_id: this.subscribable_id,
                                user_id: {
                                    // ID is normally the group_user_id, but if the user has been added with no reload, it is the user_id
                                    0: this.currentUser.user_id ?? this.currentUser.id,
                                },
                            },
                        },
                    },
                })
                    .then(res => {
                        let index = this.users.indexOf(this.currentUser);
                        this.users.splice(index, 1);
                    })
                    .catch(e => {
                        console.log(e);
                    });
            } else {
                axios.delete('/users/' + this.currentUser.id)
                    .then(res => {
                        let index = this.users.indexOf(this.currentUser);
                        this.users.splice(index, 1);
                    })
                    .catch(e => {
                        console.log(e);
                    });
            }
        },
    },
    computed: {
        btnClass() {
            return this.store.getDatatable('users')?.select === true
                ? 'btn-dark'
                : 'btn-outline-dark';
        },
        createLabel() {
            return this.subscribable ? 'enrol' : 'create';
        },
        deleteLabel() {
            return this.subscribable ? 'expel' : 'delete';
        },
    },
    watch: {
        toggleView() {
            this.dt.table().node().classList.toggle('d-none');
        },
    },
}
</script>