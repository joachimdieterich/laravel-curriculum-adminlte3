<template >
    <div class="row">
        <div
            id="role-content"
            class="col-md-12 m-0"
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
                <template v-slot:icon>
                    <i class="fas fa-user-tag"></i>
                </template>

                <template v-slot:dropdown
                    v-permission="'role_edit, role_delete'"
                >
                    <div
                        class="dropdown-menu dropdown-menu-right"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            v-permission="'role_edit'"
                            :name="'edit-role-' + role.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editRole(role)"
                        >
                            <i class="fa fa-pencil-alt mr-2"></i>
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
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.role.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div
            id="role-datatable-wrapper"
            class="w-100 dataTablesWrapper"
        >
            <DataTable
                id="role-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display: none;"
            />
        </div>

        <Teleport to="body">
            <RoleModal/>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.role.delete')"
                :description="trans('global.role.delete_helper')"
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
</template>
<script>
import RoleModal from "../role/RoleModal.vue";
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
            roles: null,
            search: '',
            showConfirm: false,
            url: '/roles/list',
            errors: {},
            currentRole: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
                { title: 'permissions', data: 'permissions' },
            ],
            options : this.$dtOptions,
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('role-added', (role) => {
            this.roles.push(role);
        });

        this.$eventHub.on('role-updated', (role) => {
            this.update(role);
        });
    },
    methods: {
        editRole(role) {
            this.globalStore?.showModal('role-modal', role);
        },
        loaderEvent() {
            const dt = $('#role-datatable').DataTable();
            dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.roles = dt.rows({page: 'current'}).data().toArray();

                $('#role-content').insertBefore('#role-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                dt.search(filter).draw();
            });
        },
        confirmItemDelete(role) {
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
        update(updatedRole) {
            const role = this.roles.find(r => r.id === updatedRole.id);

            Object.assign(role, updatedRole);
        }
    },
    components: {
        ConfirmModal,
        DataTable,
        RoleModal,
        IndexWidget,
    },
}
</script>