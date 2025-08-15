<template >
    <div class="row">
        <div id="metadataset-content"
             class="col-md-12 m-0">
            <IndexWidget
                v-permission="'metadataset_create'"
                key="'metadatasetCreate'"
                modelName="Metadataset"
                url="/metadatasets"
                :create=true
                :label="trans('global.metadataset.create')">
            </IndexWidget>
            <IndexWidget
                v-for="metadataset in metadatasets"
                :key="'metadatasetIndex'+metadataset.id"
                :model="metadataset"
                modelName="Metadataset"
                titleField="version"
                url="/metadatasets">
                <template v-slot:icon>
                    <i class="fa fa-metadataset-location-dot pt-2"></i>
                </template>

                <template
                    v-permission="'metadataset_edit, metadataset_delete'"
                    v-slot:dropdown>
                    <div class="dropdown-menu dropdown-menu-right"
                         style="z-index: 1050;"
                         x-placement="left-start">
                        <button
                            v-permission="'metadataset_delete'"
                            :id="'delete-metadataset-' + metadataset.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(metadataset)">
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.metadataset.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div id="metadataset-datatable-wrapper"
             class="w-100 dataTablesWrapper">
            <DataTable
                id="metadataset-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display:none; "
            ></DataTable>
        </div>

        <Teleport to="body">
            <MetadatasetModal></MetadatasetModal>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.metadataset.delete')"
                :description="trans('global.metadataset.delete_helper')"
                @close="() => {
                    this.showConfirm = false;
                }"
                @confirm="() => {
                    this.showConfirm = false;
                    this.destroy();
                }"
            ></ConfirmModal>
            <ConfirmModal
                :showConfirm="this.showCopy"
                :title="trans('global.metadataset.copy')"
                :description="trans('global.metadataset.copy_helper')"
                css= 'primary'
                @close="() => {
                    this.showCopy = false;
                }"
                @confirm="() => {
                    this.showCopy = false;
                    this.copy();
                }"
            ></ConfirmModal>
        </Teleport>
    </div>
</template>

<script>
import MetadatasetModal from "../metadataset/MetadatasetModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import {useGlobalStore} from "../../store/global";
DataTable.use(DataTablesCore);

export default {
    props: {
        subscribable_type: '',
        subscribable_id: '',
    },
    setup () {
        const globalStore = useGlobalStore();
        return {
            globalStore
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            metadatasets: null,
            search: '',
            showConfirm: false,
            showCopy: false,
            url: '/metadatasets/list',
            errors: {},
            currentMetadataset: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'version', data: 'version', searchable: true},
            ],
            options : this.$dtOptions,
            dt: null
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('metadataset-added', (metadataset) => {
            this.globalStore?.closeModal('metadataset-modal');
            this.metadatasets.push(metadataset);
        });
        this.$eventHub.on('metadataset-updated', (metadataset) => {
            this.globalStore?.closeModal('metadataset-modal');
            this.update(metadataset);
        });
        this.$eventHub.on('createMetadataset', () => {
            this.globalStore?.showModal('metadataset-modal', {});
        });
    },
    methods: {
        loaderEvent(){
            this.dt = $('#metadataset-datatable').DataTable();

            this.dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.metadatasets = this.dt.rows({page: 'current'}).data().toArray();

                $('#metadataset-content').insertBefore('#metadataset-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                this.dt.search(filter).draw();
            });
        },
        confirmItemDelete(metadataset){
            this.currentMetadataset = metadataset;
            this.showConfirm = true;
        },
        update(logbook) {
            const index = this.metadatasets.findIndex(
                c => c.id === logbook.id
            );

            for (const [key, value] of Object.entries(logbook)) {
                this.metadatasets[index][key] = value;
            }
        },
        destroy() {
            axios.delete('/metadatasets/' + this.currentMetadataset.id)
                .then(res => {
                    let index = this.metadatasets.indexOf(this.currentMetadataset);
                    this.metadatasets.splice(index, 1);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
    },
    components: {
        ConfirmModal,
        DataTable,
        MetadatasetModal,
        IndexWidget
    },
}
</script>
