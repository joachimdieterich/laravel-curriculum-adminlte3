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
                    <div
                        class="dropdown-menu dropdown-menu-end"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            v-permission="'organization_type_edit'"
                            :name="'edit-organization-type-' + organizationType.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editOrganizationType(organizationType)"
                        >
                            <i class="fa fa-pencil-alt me-2"></i>
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
                            <i class="fa fa-trash me-2"></i>
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
                @confirm="() => {
                    showConfirm = false;
                    destroy();
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

        this.$eventHub.on('organization-type-added', organizationType => {
            this.organizationTypes.push(organizationType);
        });

        this.$eventHub.on('organization-type-updated', updatedType => {
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
                    let index = this.organizationTypes.indexOf(this.currentOrganizationType);
                    this.organizationTypes.splice(index, 1);
                })
                .catch(e => {
                    console.log(e);
                });
        },
    },
}
</script>