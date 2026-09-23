<template>
    <div class="d-flex flex-column">
        <div
            id="organization-content"
            class="px-3"
        >
            <IndexWidget
                v-permission="'organization_create'"
                key="'organizationCreate'"
                modelName="Organization"
                url="/organizations"
                :create=true
                :label="trans('global.organization.create')"
            />
            <IndexWidget v-for="organization in organizations"
                :key="'organizationIndex'+organization.id"
                :model="organization"
                modelName="Organization"
                url="/organizations"
            >
                <template #icon>
                    <i class="fa fa-university"></i>
                </template>
                <template #dropdown
                    v-permission="'organization_edit, organization_delete'"
                >
                    <div
                        class="dropdown-menu dropdown-menu-end"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            v-permission="'organization_edit'"
                            :name="'edit-organization-' + organization.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editOrganization(organization)"
                        >
                            <i class="fa fa-pencil-alt me-2"></i>
                            {{ trans('global.organization.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'organization_delete'"
                            :id="'delete-organization-' + organization.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(organization)"
                        >
                            <i class="fa fa-trash me-2"></i>
                            {{ trans('global.organization.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <DataTable
            ref="datatable"
            id="organization-datatable"
            :columns="columns"
            :options="$dtOptions"
            ajax="/organizations/list"
            class="d-none"
            @xhr="(e, settings, json) => organizations = json.data"
        />

        <Teleport to="body">
            <OrganizationModal/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.organization.delete')"
                :description="trans('global.organization.delete_helper')"
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
import OrganizationModal from "../organization/OrganizationModal.vue";
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
DataTable.use(DataTablesCore);

export default {
    components: {
        DataTable,
        OrganizationModal,
        IndexWidget,
        ConfirmModal,
    },
    data() {
        return {
            component_id: this.$.uid,
            organizations: null,
            showConfirm: false,
            currentOrganization: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
            ],
            dt: null,
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.dt = this.$refs.datatable.dt;

        this.$eventHub.on('organization-added', organization => {
            this.organizations.push(organization);
        });

        this.$eventHub.on('organization-updated', updatedOrganization => {
            let organization = this.organizations.find(o => o.id === updatedOrganization.id);
            Object.assign(organization, updatedOrganization);
        });

        this.$eventHub.on('filter', filter => {
            this.dt.search(filter.searchString).draw();
        });
    },
    methods: {
        editOrganization(organization) {
            this.globalStore.showModal('organization-modal', organization);
        },
        confirmItemDelete(organization) {
            this.showConfirm = true;
            this.currentOrganization = organization;
        },
        destroy() {
            axios.delete('/organizations/' + this.currentOrganization.id)
                .then(res => {
                    let index = this.organizations.indexOf(this.currentOrganization);
                    this.organizations.splice(index, 1);
                })
                .catch(e => {
                    console.log(e);
                });
        },
    },
}
</script>