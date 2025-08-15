<template >
    <div class="row">
        <div
            id="organization-type-content"
            class="col-md-12 m-0"
        >
            <IndexWidget
                v-permission="'organization_type_create'"
                key="'organizationTypeCreate'"
                modelName="OrganizationType"
                url="/organizationTypes"
                :create=true
                :label="trans('global.organizationType.create')"
            />
            <IndexWidget v-for="organizationType in organizationTypes"
                :key="'organizationTypeIndex' + organizationType.id"
                :model="organizationType"
                modelName="OrganizationType"
                url="/organizationTypes"
            >
                <template v-slot:icon>
                    <i class="fa fa-university"></i>
                </template>

                <template v-slot:dropdown
                    v-permission="'organization_type_edit, organization_type_delete'"
                >
                    <div
                        class="dropdown-menu dropdown-menu-right"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            v-permission="'organization_type_edit'"
                            :name="'edit-organization-type-' + organizationType.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editOrganizationType(organizationType)"
                        >
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.organization.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'organization_type_delete'"
                            :id="'delete-organization-type-' + organizationType.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(organizationType)"
                        >
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.organizationType.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div
            id="organization-type-datatable-wrapper"
            class="w-100 dataTablesWrapper"
        >
            <DataTable
                id="organization-type-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display: none;"
            />
        </div>

        <Teleport to="body">
            <OrganizationTypeModal/>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.organizationType.delete')"
                :description="trans('global.organizationType.delete_helper')"
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
import OrganizationTypeModal from "./OrganizationTypeModal.vue";
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
            organizationTypes: null,
            search: '',
            showConfirm: false,
            url: '/organizationTypes/list',
            errors: {},
            currentOrganizationType: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
                { title: 'external_id', data: 'external_id' },
                { title: 'country_id', data: 'country_id', searchable: true} ,
                { title: 'state_id', data: 'state_id', searchable: true },
            ],
            options : this.$dtOptions,
            dt: null,
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('organization-type-added', (organizationType) => {
            this.organizationTypes.push(organizationType);
        });

        this.$eventHub.on('organization-type-updated', (updatedType) => {
            let type = this.organizationTypes.find(t => t.id === updatedType.id);

            Object.assign(type, updatedType);
        });
        
        this.$eventHub.on('filter', (filter) => {
            this.dt.search(filter).draw();
        });
    },
    methods: {
        confirmItemDelete(organizationType) {
            this.currentOrganizationType = organizationType;
            this.showConfirm = true;
        },
        editOrganizationType(organizationType) {
            this.globalStore?.showModal('organizationtype-modal', organizationType);
        },
        loaderEvent() {
            this.dt = $('#organization-type-datatable').DataTable();
            this.dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.organizationTypes = this.dt.rows({page: 'current'}).data().toArray();

                $('#organization-type-content').insertBefore('#organization-type-datatable-wrapper');
            });
        },
        destroy() {
            axios.delete('/organizationTypes/' + this.currentOrganizationType.id)
                .then(res => {
                    let index = this.organizationTypes.indexOf(this.currentOrganizationType);
                    this.organizationTypes.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
    },
    components: {
        ConfirmModal,
        DataTable,
        OrganizationTypeModal,
        IndexWidget,
    },
}
</script>