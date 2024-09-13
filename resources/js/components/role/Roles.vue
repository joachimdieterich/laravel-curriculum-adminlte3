<template >
    <div class="row">
        <div id="role-content"
             class="col-md-12 m-0">
            <IndexWidget
                v-permission="'role_create'"
                key="'roleCreate'"
                modelName="Role"
                url="/roles"
                :create=true
                :createLabel="trans('global.role.create')">
            </IndexWidget>
            <IndexWidget
                v-for="role in roles"
                :key="'roleIndex'+role.id"
                :model="role"
                modelName= "role"
                url="/roles">
                <template v-slot:icon>
                    <i class="fas fa-user-tag pt-2"></i>
                </template>

                <template
                    v-permission="'role_edit, role_delete'"
                    v-slot:dropdown>
                    <div class="dropdown-menu dropdown-menu-right"
                         style="z-index: 1050;"
                         x-placement="left-start">
                        <button
                            v-permission="'role_edit'"
                            :name="'edit-role-' + role.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editRole(role)">
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.role.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'role_delete'"
                            :id="'delete-role-' + role.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(role)">
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.role.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div id="role-datatable-wrapper"
             class="w-100 dataTablesWrapper">
            <DataTable
                id="role-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display:none; "
            ></DataTable>
        </div>

        <Teleport to="body">
            <RoleModal
                :show="this.showRoleModal"
                @close="this.showRoleModal = false"
                :params="currentRole"
            ></RoleModal>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.role.delete')"
                :description="trans('global.role.delete_helper')"
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
</template>


<script>
import RoleModal from "../role/RoleModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
DataTable.use(DataTablesCore);

export default {
    props: {

    },
    data() {
        return {
            component_id: this.$.uid,
            roles: null,
            search: '',
            showRoleModal: false,
            showConfirm: false,
            url: '/roles/list',
            errors: {},
            currentRole: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
                { title: 'permissions', data: 'permissions'},
            ],
            options : this.$dtOptions,
            modalMode: 'edit'
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('role-added', (role) => {
            this.showRoleModal = false;
            this.roles.push(role);
        });

        this.$eventHub.on('role-updated', (role) => {
            this.showRoleModal = false;
            this.update(role);
        });
        this.$eventHub.on('createRole', () => {
            this.currentRole = {};
            this.showRoleModal = true;
        });
    },
    methods: {
        editRole(role){
            this.currentRole = role;
            this.showRoleModal = true;
        },
        loaderEvent(){
            const dt = $('#role-datatable').DataTable();
            dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.roles = dt.rows({page: 'current'}).data().toArray();

                $('#role-content').insertBefore('#role-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                dt.search(filter).draw();
            });
        },
        confirmItemDelete(role){
            this.currentRole = role;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/roles/' + this.currentRole.id)
                .then(res => {
                    let index = this.roles.indexOf(this.currentRole);
                    this.roles.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        update(role) {
            const index = this.roles.findIndex(
                vc => vc.id === role.id
            );

            for (const [key, value] of Object.entries(role)) {
                this.roles[index][key] = value;
            }
        }
    },
    components: {
        ConfirmModal,
        DataTable,
        RoleModal,
        IndexWidget
    },
}
</script>
