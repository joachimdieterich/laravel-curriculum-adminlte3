<template >
    <div class="row">
        <div id="organization-content"
             class="col-md-12 m-0">
                <IndexWidget
                    v-for="organization in organizations"
                    :key="'organizationIndex'+organization.id"
                    :model="organization"
                    modelName= "Organization"
                    url="/organizations">
                <template v-slot:icon>
                    <i class="fa fa-university pt-2"></i>
                </template>

                <template
                    v-permission="'organization-edit, organization-delete'"
                    v-slot:dropdown>
                    <div class="dropdown-menu dropdown-menu-right"
                         style="z-index: 1050;"
                         x-placement="left-start">
                        <button
                            v-permission="'organization-edit'"
                            :name="'edit-organization-' + organization.id"
                                class="dropdown-item text-secondary"
                                @click.prevent="editOrganization(organization)">
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.organization.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'organization-delete'"
                            :id="'delete-organization-' + organization.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(organization)">
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.organization.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
            <IndexWidget
                v-permission="'organization-create'"
                key="'organizationCreate'"
                modelName="Organization"
                url="/organizations"
                :create=true
                :createLabel="trans('global.organization.create')">
            </IndexWidget>
        </div>
        <div id="organization-datatable-wrapper"
        class="w-100 dataTablesWrapper">
            <DataTable
                id="organization-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display:none; "
            ></DataTable>
        </div>

        <Teleport to="body">
            <OrganizationModal
                :show="this.showOrganizationModal"
                @close="this.showOrganizationModal = false"
                :params="currentOrganization"
                ></OrganizationModal>
        </Teleport>
    </div>
</template>


<script>
import OrganizationModal from "../organization/OrganizationModal";
import IndexWidget from "../uiElements/IndexWidget";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
DataTable.use(DataTablesCore);

export default {
    props: {

    },
    data() {
        return {
            organizations: null,
            search: '',
            showOrganizationModal: false,
            url: '/organizations/list',
            errors: {},
            currentOrganization: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
                { title: 'postcode', data: 'postcode', searchable: true},
                { title: 'city', data: 'city', searchable: true},
                { title: 'status', data: 'status', searchable: true},
               /* { title: 'action', data: 'action'},*/
            ],
            options : {
                dom: 'tilpr',
                pageLength: 10,
                language: {
                    url: '/datatables/i18n/German.json',
                    paginate: {
                        "first":      '<i class="fa fa-angle-double-left"></id>',
                        "last":       '<i class="fa fa-angle-double-right"></id>',
                        "next":       '<i class="fa fa-angle-right"></id>',
                        "previous":   '<i class="fa fa-angle-left"></id>',
                    },
                },
            },
            modalMode: 'edit'
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('organization-added', (organization) => {
            this.loaderEvent();
        });

        this.$eventHub.on('organization-updated', (organization) => {

        });
        this.$eventHub.on('createOrganization', () => {
            this.currentOrganization = {};
            this.showOrganizationModal = true;
        });
    },
    methods: {
        editOrganization(organization){
            this.currentOrganization = organization;
            this.showOrganizationModal = true;
        },
        loaderEvent(){
            const dt = $('#organization-datatable').DataTable();
            dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.organizations = dt.rows({page: 'current'}).data().toArray();

                $('#curriculum-content').insertBefore('#organization-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                dt.search(filter).draw();
            });
        },
        destroy() {
            axios.delete('/organization/' + this.currentOrganization.id)
                .then(res => {
                    let index = this.organizations.indexOf(this.currentOrganization);
                    this.organizations.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        update(organization) {
            const index = this.organizations.findIndex(
                vc => vc.id === organization.id
            );

            for (const [key, value] of Object.entries(organization)) {
                this.organizations[index][key] = value;
            }
        }
    },
    components: {
        DataTable,
        OrganizationModal,
        IndexWidget
    },
}
</script>
