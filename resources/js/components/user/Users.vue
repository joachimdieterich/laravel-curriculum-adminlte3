<template >
    <div>
        <div class="row">
            <div id="user-content"
                 class="col-md-12 m-0">
                <div v-if="create_label_field != 'enrol'"
                     class="row">
                    <div class="col-md-12 m-0 pb-2">
                        <button
                            class="pull-right btn"
                            :class="classObject"
                            @click="setMode()">
                            {{ trans('global.select') }}
                        </button>
                    </div>
                </div>

                <IndexWidget
                    v-permission="'user_create'"
                    key="'userCreate'"
                    modelName="User"
                    url="/users"
                    :create=true
                    :createLabel="trans('global.user.' + create_label_field)">
                    <template v-slot:itemIcon>
                        <i v-if="create_label_field == 'enrol'"
                           class="fa fa-2x p-5 fa-link nav-item-text text-muted"></i>
                        <i v-else
                           class="fa fa-2x p-5 fa-plus nav-item-text text-muted"></i>
                    </template>
                </IndexWidget>
                <IndexWidget
                    v-for="user in users"
                    :key="'userIndex'+user.id"
                    :model="user"
                    modelName= "user"
                    storeTitle= "users"
                    url="/users">
                    <template v-slot:icon>
                        <i class="fas fa-user pt-2"></i>
                    </template>

                    <template
                        v-permission="'user_edit, user_delete'"
                        v-slot:dropdown>
                        <div class="dropdown-menu dropdown-menu-right"
                             style="z-index: 1050;"
                             x-placement="left-start">
                            <button
                                v-permission="'user_edit'"
                                :name="'edit-user-' + user.id"
                                class="dropdown-item text-secondary"
                                @click.prevent="editUser(user)">
                                <i class="fa fa-pencil-alt mr-2"></i>
                                {{ trans('global.user.edit') }}
                            </button>
                            <hr class="my-1">
                            <button
                                v-permission="'user_delete'"
                                :id="'delete-user-' + user.id"
                                type="submit"
                                class="dropdown-item py-1 text-red"
                                @click.prevent="confirmItemDelete(user)">
                                <span v-if="create_label_field == 'enrol'">
                                     <i class="fa fa-unlink mr-2"></i>
                                    {{ trans('global.user.expel') }}
                                </span>
                                <span v-else>
                                     <i class="fa fa-trash mr-2"></i>
                                    {{ trans('global.user.delete') }}
                                </span>
                            </button>
                        </div>
                    </template>
                    <template v-slot:content>
                    <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                   <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                       {{ user.firstname }} {{ user.lastname }}
                   </h1>
                   <p class="text-muted small"></p>
                </span>
                    </template>
                </IndexWidget>
            </div>
            <div id="user-datatable-wrapper"
                 class="w-100 dataTablesWrapper">
                <DataTable
                    id="user-datatable"
                    :columns="columns"
                    :options="options"
                    :ajax="url"
                    :search="search"
                    width="100%"
                    style="display:none; "
                ></DataTable>
            </div>

            <Teleport to="body">
                <SubscribeUserModal
                    v-if="subscribable"
                    :show="this.showUserModal"
                    @close="this.showUserModal = false"
                    :params="reference"
                >
                </SubscribeUserModal>
                <UserModal
                    v-if="!subscribable"
                    :show="this.showUserModal"
                    @close="this.showUserModal = false"
                    :params="currentUser"
                ></UserModal>
                <ConfirmModal
                    :showConfirm="this.showConfirm"
                    :title="trans('global.user.' + delete_label_field)"
                    :description="trans('global.user.' + delete_label_field +'_helper')"
                    css= 'danger'
                    :ok_label="trans('trans.global.ok')"
                    :cancel_label="trans('trans.global.cancel')"
                    @close="() => {
                    this.showConfirm = false;
                }"
                    @confirm="() => {
                    this.showConfirm = false;
                    this.destroy();
                }"
                ></ConfirmModal>
            </Teleport>


        </div>
        <div class="row mt-4">
            <user-options></user-options>
        </div>
    </div>
</template>

<script>
import SubscribeUserModal from "./SubscribeUserModal";
import UserModal from "../user/UserModal";
import IndexWidget from "../uiElements/IndexWidget";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal";
import { storeToRefs } from 'pinia';
import { useDatatableStore } from "../../store/datatables";
import UserOptions from "./UserOptions";
DataTable.use(DataTablesCore);



export default {
    props: {
        reference : Object,
        subscribable: {
            type: Boolean,
            default: false
        },
        create_label_field: {
            type: String,
            default: 'enrol'
        },
        delete_label_field: {
            type: String,
            default: 'delete'
        },
        subscribable_type: '',
        subscribable_id: '',
    },
    setup () { //https://pinia.vuejs.org/core-concepts/getters.html#passing-arguments-to-getters
        const store = useDatatableStore();
        return {
            store
        }
    },
    data() {
        return {
            component_id: this._uid,
            users: null,
            search: '',
            showUserModal: false,
            showConfirm: false,
            url: (this.subscribable_id) ? '/users/list?group_id=' + this.reference.id : '/users/list', // if subscibable == true get enrolled users
            errors: {},
            currentUser: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'firstname', data: 'firstname', searchable: true},
                { title: 'lastname', data: 'lastname', searchable: true},
                { title: 'medium_id', data: 'medium_id'},
            ],
            options : this.$dtOptions,
            modalMode: 'edit',
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('user-added', (user) => {
            this.showUserModal = false;
            this.users.push(user);
        });

        this.$eventHub.on('user-updated', (user) => {
            this.showUserModal = false;
            this.update(user);
        });
        this.$eventHub.on('createUser', () => {
            this.currentUser = {};
            this.showUserModal = true;
        });
    },
    methods: {
        setMode(){
            //console.log(this.store);
            console.log(this.store.getDatatable('users'));
            this.store.addToDatatables(
                {
                    'datatable': 'users',
                    'select': (this.store.getDatatable('users')?.select) ? false : true,
                    'selectedItems': []
                }
            )
            console.log(this.store.getDatatable('users')?.select);

        },
        editUser(user){
            this.currentUser = user;
            this.showUserModal = true;
        },
        loaderEvent(){
            const dt = $('#user-datatable').DataTable();
            dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.users = dt.rows({page: 'current'}).data().toArray();

                $('#user-content').insertBefore('#user-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                dt.search(filter).draw();
            });
        },
        confirmItemDelete(user){
            this.currentUser = user;
            this.showConfirm = true;
        },
        destroy() {
            if (this.subscribable){
                axios.delete('/groups/expel',{
                    data :{
                        'expel_list' : {
                            0: {
                                'group_id' : this.reference.id,
                                'user_id': {
                                    0 : this.currentUser.id
                                }
                            }
                        }
                    }
                } )
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
        update(user) {
            const index = this.users.findIndex(
                vc => vc.id === user.id
            );

            for (const [key, value] of Object.entries(user)) {
                this.users[index][key] = value;
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
        SubscribeUserModal
    },
}
</script>
