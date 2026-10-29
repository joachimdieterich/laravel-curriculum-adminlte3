<template>
    <div class="d-flex flex-column">
        <div
            id="metadataset-content"
            class="px-3"
        >
            <IndexWidget
                v-permission="'metadataset_create'"
                key="'metadatasetCreate'"
                modelName="Metadataset"
                url="/metadatasets"
                :create=true
                :label="trans('global.metadataset.create')">
            </IndexWidget>
            <IndexWidget v-for="metadataset in metadatasets"
                :key="'metadatasetIndex'+metadataset.id"
                :model="metadataset"
                modelName="Metadataset"
                titleField="version"
                url="/metadatasets"
            >
                <template #icon>
                    <i class="fa fa-metadataset-location-dot pt-2"></i>
                </template>

                <template #dropdown
                    v-permission="'metadataset_edit, metadataset_delete'"
                >
                    <div class="dropdown-menu dropdown-menu-end">
                        <button
                            v-permission="'metadataset_delete'"
                            type="submit"
                            class="dropdown-item text-danger"
                            @click="confirmItemDelete(metadataset)"
                        >
                            <i class="fa fa-trash"></i>
                            {{ trans('global.metadataset.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <DataTable
            ref="datatable"
            id="metadataset-datatable"
            :columns="columns"
            :options="$dtOptions"
            ajax="/metadatasets/list"
            class="d-none"
            @xhr="(e, settings, json) => metadatasets = json.data"
        />

        <Teleport to="body">
            <MetadatasetModal/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.metadataset.delete')"
                :description="trans('global.metadataset.delete_helper')"
                @close="showConfirm = false"
                @confirm="destroy()"
            />
        </Teleport>
    </div>
</template>
<script>
import MetadatasetModal from "../metadataset/MetadatasetModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
DataTable.use(DataTablesCore);

export default {
    components: {
        ConfirmModal,
        DataTable,
        MetadatasetModal,
        IndexWidget,
    },
    data() {
        return {
            component_id: this.$.uid,
            metadatasets: null,
            showConfirm: false,
            currentMetadataset: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'version', data: 'version', searchable: true },
            ],
            dt: null,
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.dt = this.$refs.datatable.dt;

        this.$eventHub.on('metadataset-added', metadataset => {
            this.metadatasets.push(metadataset);
        });
        this.$eventHub.on('metadataset-updated', updatedeMetadataset => {
            let metadataset = this.metadatasets.find(m => m.id === updatedeMetadataset.id);
            Object.assign(metadataset, updatedeMetadataset);
        });
        this.$eventHub.on('filter', filter => {
            this.dt.search(filter.searchString).draw();
        });
    },
    methods: {
        confirmItemDelete(metadataset){
            this.currentMetadataset = metadataset;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/metadatasets/' + this.currentMetadataset.id)
                .then(res => {
                    this.showConfirm = false;
                    let index = this.metadatasets.indexOf(this.currentMetadataset);
                    this.metadatasets.splice(index, 1);
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