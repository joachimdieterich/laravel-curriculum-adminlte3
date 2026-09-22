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
                    <i class="fas fa-user-tag"></i>
                </template>

                <template #dropdown
                    v-permission="'role_edit, role_delete'"
                >
                    <div
                        class="dropdown-menu dropdown-menu-end"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            v-permission="'role_edit'"
                            :name="'edit-role-' + role.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editRole(role)"
                        >
                            <i class="fa fa-pencil-alt me-2"></i>
                            {{ trans('global.role.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'role_delete'"
                            :id="'delete-role-' + role.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(role)"
                        >
                            <i class="fa fa-trash me-2"></i>
                            {{ trans('global.role.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <DataTable
            ref="datatable"
            id="role-datatable"
            :columns="columns"
            :options="dtOptions('/roles/list')"
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
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import useTaggableDataTable from "../tag/useTaggableDataTable.js";
DataTable.use(DataTablesCore);

export default {
    components: {
        ConfirmModal,
        DataTable,
        RoleModal,
        IndexWidget,
    },
    setup() {
        const {selectedTags, selectedNegativeTags, dtOptions} = useTaggableDataTable();

        return { selectedTags, selectedNegativeTags, dtOptions }
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
        this.globalStore['searchTagModelContext'] = 'App\\Role';

        this.dt = this.$refs.datatable.dt;

        this.$eventHub.on('role-added', role => {
            this.roles.push(role);
        });

        this.$eventHub.on('role-updated', role => {
            this.update(role);
        });

        this.$eventHub.on('filter', filter => {
            this.selectedTags = filter.tags;
            this.selectedNegativeTags = filter.negativeTags;
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