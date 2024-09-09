<template >
    <div class="row">
        <div id="permission-content"
             class="col-md-12 m-0">
            <IndexWidget
                v-permission="'permission_create'"
                key="'permissionCreate'"
                modelName="Permission"
                url="/permissions"
                :create=true
                :createLabel="trans('global.permission.create')">
            </IndexWidget>
            <IndexWidget
                v-for="permission in permissions"
                :key="'permissionIndex'+permission.id"
                :model="permission"
                modelName= "permission"
                url="/permissions">
                <template v-slot:icon>
                    <i class="fas fa-unlock-alt pt-2"></i>
                </template>

                <template
                    v-permission="'permission_edit, permission_delete'"
                    v-slot:dropdown>
                    <div class="dropdown-menu dropdown-menu-right"
                         style="z-index: 1050;"
                         x-placement="left-start">
                        <button
                            v-permission="'permission_edit'"
                            :name="'edit-permission-' + permission.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editPermission(permission)">
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.permission.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'permission_delete'"
                            :id="'delete-permission-' + permission.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(permission)">
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.permission.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div id="permission-datatable-wrapper"
             class="w-100 dataTablesWrapper">
            <DataTable
                id="permission-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display:none; "
            ></DataTable>
        </div>

        <Teleport to="body">
            <PermissionModal
                :show="this.showPermissionModal"
                @close="this.showPermissionModal = false"
                :params="currentPermission"
            ></PermissionModal>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.permission.delete')"
                :description="trans('global.permission.delete_helper')"
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
import PermissionModal from "../permission/PermissionModal";
import IndexWidget from "../uiElements/IndexWidget";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal";
DataTable.use(DataTablesCore);

export default {
    props: {

    },
    data() {
        return {
            component_id: this.$.uid,
            permissions: null,
            search: '',
            showPermissionModal: false,
            showConfirm: false,
            url: '/permissions/list',
            errors: {},
            currentPermission: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
            ],
            options : this.$dtOptions,
            modalMode: 'edit'
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('permission-added', (permission) => {
            this.showPermissionModal = false;
            this.permissions.push(permission);
        });

        this.$eventHub.on('permission-updated', (permission) => {
            this.showPermissionModal = false;
            this.update(permission);
        });
        this.$eventHub.on('createPermission', () => {
            this.currentPermission = {};
            this.showPermissionModal = true;
        });
    },
    methods: {
        editPermission(permission){
            this.currentPermission = permission;
            this.showPermissionModal = true;
        },
        loaderEvent(){
            const dt = $('#permission-datatable').DataTable();
            dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.permissions = dt.rows({page: 'current'}).data().toArray();

                $('#permission-content').insertBefore('#permission-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                dt.search(filter).draw();
            });
        },
        confirmItemDelete(permission){
            this.currentPermission = permission;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/permissions/' + this.currentPermission.id)
                .then(res => {
                    let index = this.permissions.indexOf(this.currentPermission);
                    this.permissions.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        update(permission) {
            const index = this.permissions.findIndex(
                vc => vc.id === permission.id
            );

            for (const [key, value] of Object.entries(permission)) {
                this.permissions[index][key] = value;
            }
        }
    },
    components: {
        ConfirmModal,
        DataTable,
        PermissionModal,
        IndexWidget
    },
}
</script>
