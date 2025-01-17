<template >
    <div class="row">
        <div id="variant-definition-content"
             class="col-md-12 m-0">
            <IndexWidget
                v-permission="'organization_type_create'"
                key="'variantDefinitionCreate'"
                modelName="VariantDefinition"
                url="/variantDefinitions"
                :create=true
                :label="trans('global.variantDefinition.create')">
            </IndexWidget>
            <IndexWidget
                v-for="variantDefinition in variantDefinitions"
                :key="'variantDefinitionIndex'+variantDefinition.id"
                :model="variantDefinition"
                modelName= "VariantDefinition"
                url="/variantDefinitions">
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
                            :name="'edit-variant-definition-' + variantDefinition.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editVariantDefinition(variantDefinition)">
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.variantDefinition.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'organization_type_delete'"
                            :id="'delete-variant-definition-' + variantDefinition.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(variantDefinition)">
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.variantDefinition.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div id="variant-definition-datatable-wrapper"
             class="w-100 dataTablesWrapper">
            <DataTable
                id="variant-definition-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display:none; "
            ></DataTable>
        </div>

        <Teleport to="body">
            <VariantDefinitionModal></VariantDefinitionModal>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.variantDefinition.delete')"
                :description="trans('global.variantDefinition.delete_helper')"
                @close="() => {
                    this.showConfirm = false;
                }"
                @confirm="() => {
                    this.showConfirm = false;
                    this.destroy();
                }"
            ></ConfirmModal>
        </Teleport>
    </div>
</template>


<script>
import VariantDefinitionModal from "../variantDefinition/VariantDefinitionModal.vue";
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
            variantDefinitions: null,
            search: '',
            url: '/variantDefinitions/list',
            errors: {},
            currentVariantDefinition: {},
            showConfirm: false,
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
                { title: 'description', data: 'description', searchable: true},
                { title: 'color', data: 'color', searchable: true},
                { title: 'css_icon', data: 'css_icon', searchable: true},
            ],
            options : this.$dtOptions,
            modalMode: 'edit'
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('variantDefinition-added', (variantDefinition) => {
            this.loaderEvent();
            this.globalStore?.closeModal('variant-definition-modal');
        });

        this.$eventHub.on('variantDefinition-updated', (variantDefinition) => {
            this.globalStore?.closeModal('variant-definition-modal');
        });
        this.$eventHub.on('createVariantDefinition', () => {
            this.globalStore?.showModal('variant-definition-modal', {});
        });
    },
    methods: {
        editVariantDefinition(variantDefinition){
            this.currentVariantDefinition = variantDefinition;
            this.globalStore?.showModal('variant-definition-modal', this.currentVariantDefinition);
        },
        loaderEvent(){
            const dt = $('#variant-definition-datatable').DataTable();
            dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.variantDefinitions = dt.rows({page: 'current'}).data().toArray();

                $('#variant-definition-content').insertBefore('#variant-definition-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                dt.search(filter).draw();
            });
        },
        confirmItemDelete(variantDefinition){
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
        update(variantDefinition) {
            const index = this.variantDefinitions.findIndex(
                vc => vc.id === variantDefinition.id
            );

            for (const [key, value] of Object.entries(variantDefinition)) {
                this.organizations[index][key] = value;
            }
        }
    },
    components: {
        ConfirmModal,
        DataTable,
        VariantDefinitionModal,
        IndexWidget
    },
}
</script>
