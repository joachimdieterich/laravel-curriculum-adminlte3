<template >
    <div class="row">
        <div
            id="organization-content"
            class="col-md-12 m-0"
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
                <template v-slot:icon>
                    <i class="fa fa-university"></i>
                </template>
                <template v-slot:dropdown
                    v-permission="'organization_edit, organization_delete'"
                >
                    <div
                        class="dropdown-menu dropdown-menu-right"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            v-permission="'organization_edit'"
                            :name="'edit-organization-' + organization.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editOrganization(organization)"
                        >
                            <i class="fa fa-pencil-alt mr-2"></i>
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
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.organization.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div
            id="organization-datatable-wrapper"
            class="w-100 dataTablesWrapper"
        >
            <DataTable
                id="organization-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display: none;"
            />
        </div>

        <Teleport to="body">
            <OrganizationModal/>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.organization.delete')"
                :description="trans('global.organization.delete_helper')"
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
import OrganizationModal from "../organization/OrganizationModal.vue";
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
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
            organizations: null,
            search: '',
            showConfirm: false,
            url: '/organizations/list',
            errors: {},
            currentOrganization: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
                { title: 'postcode', data: 'postcode', searchable: true },
                { title: 'city', data: 'city', searchable: true },
                { title: 'status', data: 'status', searchable: true },
            ],
            options : this.$dtOptions,
            dt: null,
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('organization-added', (organization) => {
            this.organizations.push(organization);
        });

        this.$eventHub.on('organization-updated', (updatedOrganization) => {
            let organization = this.organizations.find(o => o.id === updatedOrganization.id);

            Object.assign(organization, updatedOrganization);
        });

        this.$eventHub.on('filter', (filter) => {
            this.dt.search(filter).draw();
        });
    },
    methods: {
        editOrganization(organization) {
            this.globalStore?.showModal('organization-modal', organization);
        },
        loaderEvent() {
            this.dt = $('#organization-datatable').DataTable();
            this.dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.organizations = this.dt.rows({page: 'current'}).data().toArray();

                $('#organization-content').insertBefore('#organization-datatable-wrapper');
            });
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
                .catch(err => {
                    console.log(err.response);
                });
        },
    },
    components: {
        DataTable,
        OrganizationModal,
        IndexWidget,
        ConfirmModal,
    },
}
</script>