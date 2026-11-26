
<template>
    <div class="d-flex flex-column">
        <div
            id="permission-content"
            class="px-3"
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
                <template #icon>
                    <i class="fa fa-unlock-alt"></i>
                </template>

                <template #dropdown-alt>
                    <DropdownButton v-if="checkPermission('is_admin')"
                        :entries="[
                            { icon: 'fa-pencil-alt', label: 'global.permission.edit', callback: () => editPermission(permission) },
                            { hr: true },
                            { icon: 'fa-trash', label: 'global.permission.delete', callback: () => confirmItemDelete(permission), class: 'text-danger' },
                        ]"
                    />
                </template>
            </IndexWidget>
        </div>

        <DataTable
            ref="datatable"
            id="permission-datatable"
            :columns="columns"
            :options="$dtOptions"
            ajax="permissions/list"
            class="d-none"
            @xhr="(e, settings, json) => permissions = json.data"
        />

        <Teleport to="body">
            <PermissionModal/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.permission.delete')"
                :description="trans('global.permission.delete_helper')"
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
import PermissionModal from "../permission/PermissionModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DropdownButton from "../uiElements/DropdownButton.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
DataTable.use(DataTablesCore);

export default {
    components: {
        ConfirmModal,
        DataTable,
        PermissionModal,
        IndexWidget,
        DropdownButton,
    },
    data() {
        return {
            component_id: this.$.uid,
            permissions: null,
            showConfirm: false,
            currentPermission: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
            ],
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.dt = this.$refs.datatable.dt;

        this.$eventHub.on('permission-added', permission => {
            this.permissions.push(permission);
        });

        this.$eventHub.on('permission-updated', permission => {
            this.update(permission);
        });

        this.$eventHub.on('filter', filter => {
            this.dt.search(filter.searchString).draw();
        });
    },
    methods: {
        editPermission(permission) {
            this.globalStore.showModal('permission-modal', permission);
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
}
</script>