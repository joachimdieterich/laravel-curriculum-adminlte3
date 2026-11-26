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

                <template #dropdown-alt>
                    <DropdownButton v-if="checkPermission('is_schooladmin')"
                        :entries="[
                            { icon: 'fa-pencil-alt', label: 'global.organization.edit', callback: () => editOrganization(organization) },
                            { hr: true, condition: isAdmin },
                            { icon: 'fa-trash', label: 'global.organization.delete', callback: () => confirmItemDelete(organization), condition: isAdmin, class: 'text-danger' },
                        ]"
                    />
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
                @confirm="destroy()"
            />
        </Teleport>
    </div>
</template>
<script>
import OrganizationModal from "../organization/OrganizationModal.vue";
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DropdownButton from "../uiElements/DropdownButton.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
DataTable.use(DataTablesCore);

export default {
    components: {
        DataTable,
        OrganizationModal,
        IndexWidget,
        DropdownButton,
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
                    this.showConfirm = false;
                    let index = this.organizations.indexOf(this.currentOrganization);
                    this.organizations.splice(index, 1);
                })
                .catch(e => {
                    console.log(e);
                    this.showConfirm = false;
                    this.toast.error(this.errorMessage(e));
                });
        },
    },
    computed: {
        isAdmin() {
            return this.checkPermission('is_admin');
        }
    },
}
</script>