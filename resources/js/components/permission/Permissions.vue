
<template >
    <div class="row">
        <div
            id="permission-content"
            class="col-md-12 m-0"
        >
            <IndexWidget
                v-permission="'permission_create'"
                key="'permissionCreate'"
                modelName="Permission"
                url="/permissions"
                :create=true
                :label="trans('global.permission.create')"
            />
            <IndexWidget v-for="permission in permissions"
                :key="'permissionIndex'+permission.id"
                :model="permission"
                modelName="Permission"
                url="/permissions"
            >
                <template v-slot:icon>
                    <i class="fas fa-unlock-alt pt-2"></i>
                </template>

                <template v-slot:dropdown
                    v-permission="'permission_edit, permission_delete'"
                >
                    <div
                        class="dropdown-menu dropdown-menu-right"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            v-permission="'permission_edit'"
                            :name="'edit-permission-' + permission.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editPermission(permission)"
                        >
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.permission.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'permission_delete'"
                            :id="'delete-permission-' + permission.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(permission)"
                        >
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.permission.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div
            id="permission-datatable-wrapper"
            class="w-100 dataTablesWrapper"
        >
            <DataTable
                id="permission-datatable"
                :columns="columns"
                :options="options"
                ajax="permissions/list"
                :search="search"
                width="100%"
                style="display: none;"
            />
        </div>

        <Teleport to="body">
            <PermissionModal/>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.permission.delete')"
                :description="trans('global.permission.delete_helper')"
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
import PermissionModal from "../permission/PermissionModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import {useGlobalStore} from "../../store/global";
DataTable.use(DataTablesCore);

export default {
    props: {},
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            permissions: null,
            search: '',
            showConfirm: false,
            errors: {},
            currentPermission: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
            ],
            options : this.$dtOptions,
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('permission-added', (permission) => {
            this.globalStore?.closeModal('permission-modal');
            this.permissions.push(permission);
        });

        this.$eventHub.on('permission-updated', (permission) => {
            this.globalStore?.closeModal('permission-modal');
            this.update(permission);
        });
    },
    methods: {
        editPermission(permission) {
            this.globalStore?.showModal('permission-modal', permission);
        },
        loaderEvent() {
            const dt = $('#permission-datatable').DataTable();
            dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.permissions = dt.rows({page: 'current'}).data().toArray();

                $('#permission-content').insertBefore('#permission-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                dt.search(filter).draw();
            });
        },
        confirmItemDelete(permission) {
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
        update(updatedPermission) {
            let permission = this.permissions.find(p => p.id === updatedPermission.id);

            Object.assign(permission, updatedPermission);
        }
    },
    components: {
        ConfirmModal,
        DataTable,
        PermissionModal,
        IndexWidget,
    },
}
</script>