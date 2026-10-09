<template>
    <div class="d-flex flex-column">
        <div
            id="organization-type-content"
            class="px-3"
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
                <template #icon>
                    <i class="fa fa-university"></i>
                </template>

                <template #dropdown
                    v-permission="'organization_type_edit, organization_type_delete'"
                >
                    <div class="dropdown-menu dropdown-menu-end">
                        <button
                            v-permission="'organization_type_edit'"
                            type="button"
                            class="dropdown-item"
                            @click="editOrganizationType(organizationType)"
                        >
                            <i class="fa fa-pencil-alt"></i>
                            {{ trans('global.organization.edit') }}
                        </button>

                        <hr class="my-1">

                        <button
                            v-permission="'organization_type_delete'"
                            type="submit"
                            class="dropdown-item text-danger"
                            @click="confirmItemDelete(organizationType)"
                        >
                            <i class="fa fa-trash"></i>
                            {{ trans('global.organizationType.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <DataTable
            ref="datatable"
            id="organization-type-datatable"
            :columns="columns"
            :options="$dtOptions"
            ajax="/organizationTypes/list"
            class="d-none"
            @xhr="(e, settings, json) => organizationTypes = json.data"
        />

        <Teleport to="body">
            <OrganizationTypeModal/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.organizationType.delete')"
                :description="trans('global.organizationType.delete_helper')"
                @close="showConfirm = false"
                @confirm="destroy()"
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
DataTable.use(DataTablesCore);

export default {
    components: {
        ConfirmModal,
        DataTable,
        OrganizationTypeModal,
        IndexWidget,
    },
    data() {
        return {
            component_id: this.$.uid,
            organizationTypes: null,
            showConfirm: false,
            currentOrganizationType: {},
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

        this.$eventHub.on('organizationType-added', organizationType => {
            this.organizationTypes.push(organizationType);
        });

        this.$eventHub.on('organizationType-updated', updatedType => {
            let type = this.organizationTypes.find(t => t.id === updatedType.id);
            Object.assign(type, updatedType);
        });

        this.$eventHub.on('filter', filter => {
            this.dt.search(filter.searchString).draw();
        });
    },
    methods: {
        confirmItemDelete(organizationType) {
            this.currentOrganizationType = organizationType;
            this.showConfirm = true;
        },
        editOrganizationType(organizationType) {
            this.globalStore.showModal('organizationtype-modal', organizationType);
        },
        destroy() {
            axios.delete('/organizationTypes/' + this.currentOrganizationType.id)
                .then(res => {
                    this.showConfirm = false;
                    let index = this.organizationTypes.indexOf(this.currentOrganizationType);
                    this.organizationTypes.splice(index, 1);
                })
                .catch(e => {
                    console.log(e);
                    this.showConfirm = false;
                    this.toast.error(this.errorMessage(e));
                });
        },
    },
}
</script>