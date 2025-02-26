<template >
    <div>
        <div class="row">
            <div
                id="user-content"
                class="col-md-12 m-0"
            >
                <div v-if="create_label_field != 'enrol'"
                    class="row"
                >
                    <div class="col-md-12 m-0 pb-2">
                        <button
                            class="pull-right btn"
                            :class="classObject"
                            @click="setMode()"
                        >
                            {{ trans('global.select') }}
                        </button>
                    </div>
                </div>

                <IndexWidget v-if="!subscribable"
                    v-permission="'user_create'"
                    key="userCreate"
                    modelName="User"
                    url="/users"
                    :create="true"
                    :subscribe="false"
                    :subscribable_id="subscribable_id"
                    :label="trans('global.user.' + create_label_field)"
                />
                <IndexWidget v-else="subscribable"
                    v-permission="'group_enrolment'"
                    key="userSubscribe"
                    modelName="User"
                    url="/users"
                    :create="false"
                    :subscribe="true"
                    :subscribable_id="subscribable_id"
                    :label="trans('global.user.' + create_label_field)"
                >
                    <template v-slot:itemIcon>
                        <i v-if="create_label_field == 'enrol'"
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
                    <template v-slot:icon>
                        <i class="fas fa-user pt-2"></i>
                    </template>

                    <template v-slot:dropdown>
                        <div
                            class="dropdown-menu dropdown-menu-right"
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
                                    <i class="fa fa-pencil-alt mr-2"></i>
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
                                        <i class="fa fa-trash mr-2"></i>
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
                                        <i class="fa fa-unlink mr-2"></i>
                                        {{ trans('global.user.expel') }}
                                    </span>
                                </button>
                            </div>
                        </div>
                    </template>

                    <template v-slot:content>
                        <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                            <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                                {{ user.firstname }} {{ user.lastname }}
                            </h1>
                        </span>
                    </template>
                </IndexWidget>
            </div>
            <div
                id="user-datatable-wrapper"
                class="w-100 dataTablesWrapper"
            >
                <DataTable
                    id="user-datatable"
                    :columns="columns"
                    :options="options"
                    :ajax="url"
                    :search="search"
                    width="100%"
                    style="display: none;"
                />
            </div>

            <Teleport to="body">
                <UserModal v-if="!subscribable"/>
                <SubscribeUserModal v-if="subscribable"/>
                <ConfirmModal
                    :showConfirm="showConfirm"
                    :title="trans('global.user.' + delete_label_field)"
                    :description="trans('global.user.' + delete_label_field +'_helper')"
                    @close="() => {
                        this.showConfirm = false;
                    }"
                    @confirm="() => {
                        this.showConfirm = false;
                        this.destroy();
                    }"
                />
            </Teleport>
        </div>
        <div v-if="!subscribable"
            class="row mt-4"
        >
            <UserOptions/>
        </div>
    </div>
</template>
<script>
import SubscribeUserModal from "./SubscribeUserModal.vue";
import UserModal from "../user/UserModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import { useDatatableStore } from "../../store/datatables";
import UserOptions from "./UserOptions.vue";
import {useGlobalStore} from "../../store/global";
DataTable.use(DataTablesCore);

export default {
    props: {
        reference : Object,
        subscribable: {
            type: Boolean,
            default: false,
        },
        create_label_field: {
            type: String,
            default: 'enrol',
        },
        delete_label_field: {
            type: String,
            default: 'delete',
        },
        subscribable_type: '',
        subscribable_id: '',
    },
    setup() {
        const store = useDatatableStore();
        const globalStore = useGlobalStore();
        return {
            store,
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            users: null,
            search: '',
            showConfirm: false,
            url: (this.subscribable_id) ? '/users/list?group_id=' + this.reference.id : '/users/list', // if subscibable == true get enrolled users
            errors: {},
            currentUser: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'firstname', data: 'firstname', searchable: true },
                { title: 'lastname', data: 'lastname', searchable: true },
                { title: 'medium_id', data: 'medium_id' },
            ],
            options : this.$dtOptions,
            dt: null,
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('user-added', (user) => {
            this.users.push(user);
        });

        this.$eventHub.on('user-updated', (updatedUser) => {
            const user = this.users.find(u => u.id === updatedUser.id);

            Object.assign(user, updatedUser);
        });

        this.$eventHub.on('filter', (filter) => {
            this.dt.search(filter).draw();
        });
    },
    methods: {
        setMode() {
            console.log(this.store.getDatatable('users'));
            this.store.addToDatatables(
                {
                    datatable: 'users',
                    select: (this.store.getDatatable('users')?.select) ? false : true,
                    selectedItems: [],
                }
            )
            console.log(this.store.getDatatable('users')?.select);

        },
        editUser(user) {
            this.globalStore?.showModal('user-modal', user);
        },
        loaderEvent() {
            this.dt = $('#user-datatable').DataTable();
            this.dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.users = this.dt.rows({page: 'current'}).data().toArray();

                $('#user-content').insertBefore('#user-datatable-wrapper');
            });
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
                                group_id: this.reference.id,
                                user_id: {
                                    0: this.currentUser.id,
                                },
                            },
                        },
                    },
                })
                    .then(res => {
                        let index = this.users.indexOf(this.currentUser);
                        this.users.splice(index, 1);
                    })
                    .catch(err => {
                        console.log(err.response);
                    });
            } else {
                axios.delete('/users/' + this.currentUser.id)
                    .then(res => {
                        let index = this.users.indexOf(this.currentUser);
                        this.users.splice(index, 1);
                    })
                    .catch(err => {
                        console.log(err.response);
                    });
            }
        },
    },
    computed: {
        classObject() {
            if (this.store.getDatatable('users')?.select === true) {
                return 'btn-dark'
            } else {
                return 'btn-light'
            }
        }
    },
    components: {
        UserOptions,
        ConfirmModal,
        DataTable,
        UserModal,
        IndexWidget,
        SubscribeUserModal,
    },
}
</script>