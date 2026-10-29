<template>
    <div class="d-flex flex-column">
        <div
            id="navigator-content"
            class="px-3"
        >
            <IndexWidget
                v-permission="'navigator_create'"
                key="'navigatorCreate'"
                modelName="Navigator"
                url="/navigators"
                :create=true
                :label="trans('global.navigator.create')"
            />
            <IndexWidget v-for="navigator in navigators"
                :key="'navigatorIndex'+navigator.id"
                :model="navigator"
                modelName="Navigator"
                :urlOnly=true
                :url="navigator.url"
            >
                <template #icon>
                    <i class="fa fa-history"></i>
                </template>

                <template #dropdown
                    v-permission="'navigator_edit, navigator_delete'"
                >
                    <div class="dropdown-menu dropdown-menu-end">
                        <button
                            type="button"
                            class="dropdown-item"
                            @click="editNavigator(navigator)"
                        >
                            <i class="fa fa-pencil-alt"></i>
                            {{ trans('global.navigator.edit') }}
                        </button>

                        <hr class="my-1">

                        <button
                            type="submit"
                            class="dropdown-item text-danger"
                            @click="confirmItemDelete(navigator)"
                        >
                            <i class="fa fa-trash"></i>
                            {{ trans('global.navigator.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <DataTable
            ref="datatable"
            id="navigator-datatable"
            :columns="columns"
            :options="$dtOptions"
            ajax="/navigators/list"
            class="d-none"
            @xhr="(e, settings, json) => navigators = json.data"
        />

        <Teleport to="body">
            <NavigatorModal/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.navigator.delete')"
                :description="trans('global.navigator.delete_helper')"
                @close="showConfirm = false"
                @confirm="destroy()"
            />
        </Teleport>
    </div>
</template>
<script>
import NavigatorModal from "../navigator/NavigatorModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
DataTable.use(DataTablesCore);

export default {
    components: {
        ConfirmModal,
        DataTable,
        NavigatorModal,
        IndexWidget,
    },
    data() {
        return {
            component_id: this.$.uid,
            navigators: null,
            showConfirm: false,
            currentNavigator: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
                { title: 'organization_id', data: 'organization'},
                { title: 'organization', data: 'organization', searchable: true},
            ],
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.dt = this.$refs.datatable.dt;

        this.$eventHub.on('navigator-added', navigator => {
            this.navigators.push(navigator);
        });

        this.$eventHub.on('navigator-updated', updatedNavigator => {
            let navigator = this.navigators.find(n => n.id === updatedNavigator.id);

            Object.assign(navigator, updatedNavigator);
        });

        this.$eventHub.on('filter', filter => {
            dt.search(filter.searchString).draw();
        });
    },
    methods: {
        editNavigator(navigator) {
            this.globalStore.showModal('navigator-modal', navigator);
        },
        confirmItemDelete(navigator) {
            this.currentNavigator = navigator;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/navigators/' + this.currentNavigator.id)
                .then(res => {
                    this.showConfirm = false;
                    let index = this.navigators.indexOf(this.currentNavigator);
                    this.navigators.splice(index, 1);
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