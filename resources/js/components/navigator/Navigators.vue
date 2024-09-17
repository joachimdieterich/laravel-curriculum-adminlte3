<template >
    <div class="row">
        <div id="navigator-content"
             class="col-md-12 m-0">
            <IndexWidget
                v-permission="'navigator_create'"
                key="'navigatorCreate'"
                modelName="Navigator"
                url="/navigators"
                :create=true
                :createLabel="trans('global.navigator.create')">
            </IndexWidget>
            <IndexWidget
                v-for="navigator in navigators"
                :key="'navigatorIndex'+navigator.id"
                :model="navigator"
                modelName= "navigator"
                :urlOnly=true
                :url="navigator.url">
                <template v-slot:icon>
                    <i class="fa fa-history pt-2"></i>
                </template>

                <template
                    v-permission="'navigator_edit, navigator_delete'"
                    v-slot:dropdown>
                    <div class="dropdown-menu dropdown-menu-right"
                         style="z-index: 1050;"
                         x-placement="left-start">
                        <button
                            v-permission="'navigator_edit'"
                            :name="'edit-navigator-' + navigator.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editNavigator(navigator)">
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.navigator.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'navigator_delete'"
                            :id="'delete-navigator-' + navigator.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(navigator)">
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.navigator.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div id="navigator-datatable-wrapper"
             class="w-100 dataTablesWrapper">
            <DataTable
                id="navigator-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display:none; "
            ></DataTable>
        </div>

        <Teleport to="body">
            <NavigatorModal
                :show="this.showNavigatorModal"
                @close="this.showNavigatorModal = false"
                :params="currentNavigator"
            ></NavigatorModal>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.navigator.delete')"
                :description="trans('global.navigator.delete_helper')"
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
import NavigatorModal from "../navigator/NavigatorModal";
import IndexWidget from "../uiElements/IndexWidget";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal";
DataTable.use(DataTablesCore);

export default {
    props: {

    },
    data() {
        return {
            component_id: this.$.uid,
            navigators: null,
            search: '',
            showNavigatorModal: false,
            showConfirm: false,
            url: '/navigators/list',
            errors: {},
            currentNavigator: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
                { title: 'organization_id', data: 'organization'},
                { title: 'organization', data: 'organization', searchable: true},
            ],
            options : this.$dtOptions,
            modalMode: 'edit'
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('navigator-added', (navigator) => {
            this.showNavigatorModal = false;
            this.navigators.push(navigator);
        });

        this.$eventHub.on('navigator-updated', (navigator) => {
            this.showNavigatorModal = false;
            this.update(navigator);
        });
        this.$eventHub.on('createNavigator', () => {
            this.currentNavigator = {};
            this.showNavigatorModal = true;
        });
    },
    methods: {
        editNavigator(navigator){
            this.currentNavigator = navigator;
            this.showNavigatorModal = true;
        },
        loaderEvent(){
            const dt = $('#navigator-datatable').DataTable();
            dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.navigators = dt.rows({page: 'current'}).data().toArray();

                $('#navigator-content').insertBefore('#navigator-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                dt.search(filter).draw();
            });
        },
        confirmItemDelete(navigator){
            this.currentNavigator = navigator;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/navigators/' + this.currentNavigator.id)
                .then(res => {
                    let index = this.navigators.indexOf(this.currentNavigator);
                    this.navigators.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        update(navigator) {
            const index = this.navigators.findIndex(
                vc => vc.id === navigator.id
            );

            for (const [key, value] of Object.entries(navigator)) {
                this.navigators[index][key] = value;
            }
        }
    },
    components: {
        ConfirmModal,
        DataTable,
        NavigatorModal,
        IndexWidget
    },
}
</script>
