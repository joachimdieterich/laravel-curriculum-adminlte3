<template>
    <div>
        <div class="px-3">
            <IndexWidget
                v-permission="'organization_type_create'"
                key="'variantDefinitionCreate'"
                modelName="VariantDefinition"
                url="/variantDefinitions"
                :create=true
                :label="trans('global.variantDefinition.create')"
            />
            <IndexWidget v-for="variantDefinition in variantDefinitions"
                :key="'variantDefinitionIndex'+variantDefinition.id"
                :model="variantDefinition"
                modelName="VariantDefinition"
                url="/variantDefinitions"
            >
                <template #icon>
                    <i class="fa fa-university pt-2"></i>
                </template>

                <template #dropdown
                    v-permission="'organization_type_edit, organization_type_delete'"
                >
                    <div class="dropdown-menu dropdown-menu-end">
                        <button
                            v-permission="'organization_type_edit'"
                            type="button"
                            class="dropdown-item"
                            @click="editVariantDefinition(variantDefinition)"
                        >
                            <i class="fa fa-pencil-alt"></i>
                            {{ trans('global.variantDefinition.edit') }}
                        </button>

                        <hr class="my-1">

                        <button
                            v-permission="'organization_type_delete'"
                            type="submit"
                            class="dropdown-item text-danger"
                            @click="confirmItemDelete(variantDefinition)"
                        >
                            <i class="fa fa-trash"></i>
                            {{ trans('global.variantDefinition.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <DataTable
            ref="datatable"
            id="variant-definition-datatable"
            :columns="columns"
            :options="$dtOptions"
            ajax="/variantDefinitions/list"
            class="d-none"
            @xhr="(e, settings, json) => variantDefinitions = json.data"
        />

        <Teleport to="body">
            <VariantDefinitionModal/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.variantDefinition.delete')"
                :description="trans('global.variantDefinition.delete_helper')"
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
import VariantDefinitionModal from "../variantDefinition/VariantDefinitionModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
DataTable.use(DataTablesCore);

export default {
    components: {
        ConfirmModal,
        DataTable,
        VariantDefinitionModal,
        IndexWidget
    },
    data() {
        return {
            component_id: this.$.uid,
            variantDefinitions: null,
            currentVariantDefinition: {},
            showConfirm: false,
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
                { title: 'description', data: 'description', searchable: true },
                { title: 'color', data: 'color', searchable: true },
                { title: 'css_icon', data: 'css_icon', searchable: true },
            ],
            dt: null,
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.dt = this.$refs.datatable.dt;

        this.$eventHub.on('variantDefinition-added', variantDefinition => {
            this.variantDefinitions.push(variantDefinition);
            this.globalStore?.closeModal('variant-definition-modal');
        });

        this.$eventHub.on('variantDefinition-updated', variantDefinition => {
            this.globalStore?.closeModal('variant-definition-modal');
        });

        this.$eventHub.on('filter', filter => this.dt.search(filter).draw());
    },
    methods: {
        editVariantDefinition(variantDefinition) {
            this.currentVariantDefinition = variantDefinition;
            this.globalStore.showModal('variant-definition-modal', this.currentVariantDefinition);
        },
        confirmItemDelete(variantDefinition) {
            this.showConfirm = true;
            this.currentVariantDefinition = variantDefinition;
        },
        destroy() {
            axios.delete('/variantDefinitions/' + this.currentVariantDefinition.id)
                .then(res => {
                    let index = this.variantDefinitions.indexOf(this.currentVariantDefinition);
                    this.variantDefinitions.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
    },
}
</script>