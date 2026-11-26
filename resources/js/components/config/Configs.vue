<template>
    <div class="d-flex flex-column">
        <div
            id="config-content"
            class="px-3"
        >
            <IndexWidget
                v-permission="'config_create'"
                key="'ConfigCreate'"
                modelName="Config"
                url="/configs"
                :create=true
                :label="trans('global.config.create')">
            </IndexWidget>

            <IndexWidget v-for="config in configs"
                :id="config.id"
                :key="'configIndex' + config.id"
                :model="config"
                titleField="key"
                modelName="Config"
                url="/configs"
            >
                <template #icon>
                    <i class="fa fa-key"></i>
                </template>

                <template #dropdown-alt>
                    <DropdownButton v-if="checkPermission('is_admin')"
                        :entries="[
                            { icon: 'fa-pencil-alt', label: 'global.config.edit', callback: () => editConfig(config) },
                            { hr: true },
                            { icon: 'fa-trash', label: 'global.config.delete', callback: () => confirmItemDelete(config), class: 'text-danger' },
                        ]"
                    />
                </template>
            </IndexWidget>
        </div>

        <DataTable
            ref="datatable"
            id="config-datatable"
            :columns="columns"
            :options="$dtOptions"
            ajax="/configs/list"
            class="d-none"
            @xhr="(e, settings, json) => configs = json.data"
        />

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
                        <small>{{ trans('global.config.model_limiter_description')  }}</small>
                    </a>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <ConfigModal/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.config.delete')"
                :description="trans('global.config.delete_helper')"
                @close="showConfirm = false"
                @confirm="destroy()"
            />
        </Teleport>
    </div>
</template>
<script>
import IndexWidget from "../uiElements/IndexWidget.vue";
import DropdownButton from "../uiElements/DropdownButton.vue";
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfigModal from "./ConfigModal.vue";
DataTable.use(DataTablesCore);

export default {
    components: {
        IndexWidget,
        DropdownButton,
        DataTable,
        ConfirmModal,
        ConfigModal,
    },
    data() {
        return {
            configs: [],
            showConfirm: false,
            currentConfig: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'key', data: 'key', searchable: true },
                { title: 'value', data: 'value', searchable: true },
                { title: 'referenceable_type', data: 'referenceable_type', searchable: true },
                { title: 'referenceable_id', data: 'referenceable_id', searchable: true },
                { title: 'data_type', data: 'data_type', searchable: true },
            ],
            dt: null,
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.dt = this.$refs.datatable.dt;

        this.$eventHub.on('config-added', config => {
            this.configs.push(config);
        });

        this.$eventHub.on('config-updated', updatedConfig => {
            let config = this.configs.find(c => c.id === updatedConfig.id);
            Object.assign(config, updatedConfig);
        });

        this.$eventHub.on('filter', filter => {
            this.dt.search(filter.searchString).draw();
        });
    },
    methods: {
        editConfig(config) {
            this.globalStore.showModal('config-modal', config);
        },
        confirmItemDelete(config) {
            this.currentConfig = config;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/configs/' + this.currentConfig.id)
                .then(res => {
                    this.showConfirm = false;
                    let index = this.configs.indexOf(this.currentConfig);
                    this.configs.splice(index, 1);
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