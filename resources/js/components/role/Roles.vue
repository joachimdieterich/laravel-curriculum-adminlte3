<template>
    <div class="d-flex flex-column">
        <div
            id="role-content"
            class="px-3"
        >
            <IndexWidget
                v-permission="'role_create'"
                key="'roleCreate'"
                modelName="Role"
                url="/roles"
                :create=true
                :label="trans('global.role.create')"
            />
            <IndexWidget v-for="role in roles"
                :key="'roleIndex' + role.id"
                :model="role"
                modelName="Role"
                url="/roles"
            >
                <template #icon>
                    <i class="fa fa-user-tag"></i>
                </template>

                <template #dropdown-alt>
                    <DropdownButton v-if="checkPermission('is_admin')"
                        :entries="[
                            { icon: 'fa-pencil-alt', label: 'global.role.edit', callback: () => editRole(role) },
                            { hr: true },
                            { icon: 'fa-trash', label: 'global.role.delete', callback: () => confirmItemDelete(role), class: 'text-danger' },
                        ]"
                    />
                </template>
            </IndexWidget>
        </div>

        <DataTable
            ref="datatable"
            id="role-datatable"
            :columns="columns"
            :options="$dtOptions"
            ajax="/roles/list"
            class="d-none"
            @xhr="(e, settings, json) => roles = json.data"
        />

        <Teleport to="body">
            <RoleModal/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.role.delete')"
                :description="trans('global.role.delete_helper')"
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
import RoleModal from "../role/RoleModal.vue";
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
        RoleModal,
        IndexWidget,
        DropdownButton,
    },
    data() {
        return {
            component_id: this.$.uid,
            roles: null,
            showConfirm: false,
            currentRole: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
            ],
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.dt = this.$refs.datatable.dt;

        this.$eventHub.on('role-added', role => {
            this.roles.push(role);
        });

        this.$eventHub.on('role-updated', role => {
            this.update(role);
        });

        this.$eventHub.on('filter', filter => {
            this.dt.search(filter.searchString).draw();
        });
    },
    methods: {
        editRole(role) {
            this.globalStore.showModal('role-modal', role);
        },
        confirmItemDelete(role) {
            this.currentRole = role;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/roles/' + this.currentRole.id)
                .then(() => {
                    let index = this.roles.indexOf(this.currentRole);
                    this.roles.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        update(updatedRole) {
            const role = this.roles.find(r => r.id === updatedRole.id);

            Object.assign(role, updatedRole);
        }
    },
}
</script>