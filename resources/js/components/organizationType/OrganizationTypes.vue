<template >
    <div class="row">
        <div id="organization-type-content"
             class="col-md-12 m-0">
            <IndexWidget
                v-permission="'organization_type_create'"
                key="'organizationTypeCreate'"
                modelName="OrganizationType"
                url="/organizationTypes"
                :create=true
                :createLabel="trans('global.organizationType.create')">
            </IndexWidget>
            <IndexWidget
                v-for="organizationType in organizationTypes"
                :key="'organizationTypeIndex'+organizationType.id"
                :model="organizationType"
                modelName= "OrganizationType"
                url="/organizationTypes">
                <template v-slot:icon>
                    <i class="fa fa-university pt-2"></i>
                </template>

                <template
                    v-permission="'organization_type_edit, organization_type_delete'"
                    v-slot:dropdown>
                    <div class="dropdown-menu dropdown-menu-right"
                         style="z-index: 1050;"
                         x-placement="left-start">
                        <button
                            v-permission="'organization_type_edit'"
                            :name="'edit-organization-type-' + organizationType.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editOrganizationType(organizationType)">
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.organization.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'organization_type_delete'"
                            :id="'delete-organization-type-' + organizationType.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(organizationType)">
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.organizationType.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div id="organization-type-datatable-wrapper"
             class="w-100 dataTablesWrapper">
            <DataTable
                id="organization-type-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display:none; "
            ></DataTable>
        </div>

        <Teleport to="body">
            <OrganizationTypeModal></OrganizationTypeModal>
        </Teleport>
    </div>
</template>


<script>
import OrganizationTypeModal from "../organizationType/OrganizationTypeModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import {useGlobalStore} from "../../store/global";
DataTable.use(DataTablesCore);

export default {
    props: {

    },
    setup () {
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
            url: '/organizationTypes/list',
            errors: {},
            currentOrganizationType: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
                { title: 'external_id', data: 'external_id'},
                { title: 'country_id', data: 'country_id', searchable: true},
                { title: 'state_id', data: 'state_id', searchable: true},
            ],
            options : this.$dtOptions,
            modalMode: 'edit'
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('organizationType-added', (organizationType) => {
            this.loaderEvent();
            this.globalStore?.closeModal('organization-type-modal');
        });

        this.$eventHub.on('organizationType-updated', (organizationType) => {
            this.globalStore?.closeModal('organization-type-modal');
        });
        this.$eventHub.on('createOrganizationType', () => {
            this.globalStore?.showModal('organization-type-modal', {});
        });
    },
    methods: {
        editOrganizationType(organizationType){
            this.currentOrganizationType = organizationType;
            this.globalStore?.showModal('organization-type-modal', this.currentOrganizationType);
        },
        loaderEvent(){
            const dt = $('#organization-type-datatable').DataTable();
            dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.organizationTypes = dt.rows({page: 'current'}).data().toArray();

                $('#organization-type-content').insertBefore('#organization-type-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                dt.search(filter).draw();
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
        update(organizationType) {
            const index = this.organizationTypes.findIndex(
                vc => vc.id === organizationType.id
            );

            for (const [key, value] of Object.entries(organizationType)) {
                this.organizations[index][key] = value;
            }
        }
    },
    components: {
        ConfirmModal,
        DataTable,
        OrganizationTypeModal,
        IndexWidget
    },
}
</script>
