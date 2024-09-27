<template >
    <div class="row">
        <div id="config-content"
             class="col-md-12 m-0">
            <IndexWidget
                v-permission="'config_create'"
                key="'ConfigCreate'"
                modelName="Config"
                url="/configs"
                :create=true
                :createLabel="trans('global.config.create')">
            </IndexWidget>

            <IndexWidget
                v-for="config in configs"
                :id="config.id"
                :key="'configIndex'+config.id"
                :model="config"
                titleField="key"
                modelName= "config"
                url="/configs">
                <template v-slot:icon>
                    <i v-if="config.type_id === 1"
                       class="fas fa-globe pt-2"></i>
                    <i v-else-if="config.type_id === 2"
                       class="fas fa-university pt-2"></i>
                    <i v-else-if="config.type_id === 3"
                       class="fa fa-users pt-2"></i>
                    <i v-else
                       class="fa fa-user pt-2"></i>
                </template>

                <template
                    v-permission="'config_edit, config_delete'"
                    v-slot:dropdown>
                    <div class="dropdown-menu dropdown-menu-right"
                         style="z-index: 1050;"
                         x-placement="left-start">
                        <button :name="'config-edit_' + config.id"
                                class="dropdown-item text-secondary"
                                @click.prevent="editConfig(config)">
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.config.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            :id="'delete-config-' + config.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(config)">
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.config.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <div id="config-datatable-wrapper"
             class="w-100 dataTablesWrapper">
            <DataTable
                id="config-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                width="100%"
                style="display:none; "
            ></DataTable>
        </div>


            <div class="col-sm-6 col-12 pt-3">
                <div class="info-box">

                <span class="info-box-icon bg-info">
                   <a href="/configs/models" class="link-muted">
                       <i class="fa fa-tachometer-alt"></i>
                   </a>
                </span>
                    <div class="info-box-content">
                        <a href="/configs/models" class="link-muted">
                            <span class="info-box-text">{{ trans('global.config.model_limiter_title') }}</span>
                            <small>
                                <span class="">{{ trans('global.config.model_limiter_description')  }}</span>
                            </small>
                        </a>
                    </div>

                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>


        <Teleport to="body">
            <ConfigModal></ConfigModal>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.config.delete')"
                :description="trans('global.config.delete_helper')"
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
import IndexWidget from "../uiElements/IndexWidget.vue";
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
DataTable.use(DataTablesCore);
import ConfigModal from "./ConfigModal.vue";
import {useGlobalStore} from "../../store/global";

export default {
    props: {
        subscribable_type: '',
        subscribable_id: '',
    },
    setup () {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            configs: [],
            subscriptions: {},
            search: '',
            showConfigModal: false,
            showConfirm: false,
            showSubscribeModal: false,
            showSubscribeParams: {},
            url: '/configs/list',
            errors: {},
            currentConfig: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'key', data: 'key', searchable: true},
                { title: 'value', data: 'value', searchable: true},
                { title: 'referenceable_type', data: 'referenceable_type', searchable: true},
                { title: 'referenceable_id', data: 'referenceable_id', searchable: true},
                { title: 'data_type', data: 'data_type', searchable: true},
            ],
            options : this.$dtOptions,
        }
    },
    methods: {
        confirmItemDelete(config){
            this.currentConfig = config;
            this.showConfirm = true;
        },
        editConfig(config){
            this.globalStore?.showModal('config-modal', config);
        },
        setOwner(config){
            window.location = "/configs/" + config.id + "/editOwner";
        },
        shareConfig(config){
            this.showSubscribeParams = { 'modelId': config.id, 'modelUrl': 'config' , 'shareWithToken': true, 'canEditCheckbox': false};
            this.showSubscribeModal = true;
        },
        loaderEvent(){
            this.url = '/configs/list';

            const dt = $('#config-datatable').DataTable();
            dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the config-data
                this.configs = dt.rows({page: 'current'}).data().toArray();
                $('#config-content').insertBefore('#config-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                dt.search(filter).draw();
            });
        },
        destroy() {
            axios.delete('/configs/' + this.currentConfig.id)
                .then(res => {
                    let index = this.configs.indexOf(this.currentConfig);
                    this.configs.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        update(config) {
            const index = this.configs.findIndex(
                c => c.id === config.id
            );

            for (const [key, value] of Object.entries(config)) {
                this.configs[index][key] = value;
            }
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('config-added', (config) => {
            this.globalStore?.closeModal('config-modal');
            this.configs.push(config); //todo -> use global widget to get add working
        });

        this.$eventHub.on('config-updated', (config) => {
            this.globalStore?.closeModal('config-modal');
            this.loaderEvent();
            this.update(config); //todo -> use global widget to get update working
        });

        this.$eventHub.on('createConfig', () => {
            this.globalStore?.showModal('config-modal', {});
        });
    },

    components: {
        IndexWidget,
        DataTable,
        ConfirmModal,
        ConfigModal
    },
}
</script>
