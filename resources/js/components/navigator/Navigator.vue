<template >
    <div class="row">
        <div
            id="navigatorView-content"
            class="col-md-12 m-0"
        >
            <div v-for="navigatorItem in navigatorItems">
                <div v-if="navigatorItem.position == 'header'">
                    <Content v-if="navigatorItem.referenceable_type == 'App\\Content'"
                        :content="navigatorItem"
                    />
                    <div v-if="navigatorItem.referenceable_type != 'App\\Content'"
                        class="col-12"
                    >
                        <div class="card">
                            <div class="card-body">
                                {{ navigatorItem.title }}
                                <button
                                    v-permission="'navigator_delete'"
                                    :id="'delete-navigatorView-' + navigatorItem.id"
                                    type="submit"
                                    class="dropdown-item py-1 text-red"
                                    @click.prevent="confirmItemDelete(navigatorItem)"
                                >
                                    <i class="fa fa-trash mr-2"></i>
                                    {{ trans('global.navigatorItem.delete') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <IndexWidget
                v-permission="'navigator_create'"
                key="'navigatorItemCreate'"
                modelName="Navigator-Item"
                :url="'/navigators/' + navigator.id"
                :create=true
                :label="trans('global.NavigatorItem.create')"
            />
            <span v-for="navigatorItem in navigatorItems">
                <IndexWidget v-if="navigatorItem.position == 'content'"
                    :key="'navigatorItemIndex' + navigatorItem.id"
                    :model="navigatorItem"
                    modelName="NavigatorItem"
                    :url="navigatorItem.url"
                >
                    <template v-slot:icon>
                        <i class="fa fa-history pt-2"></i>
                    </template>

                    <template v-slot:dropdown
                        v-permission="'navigator_edit, navigator_delete'"
                    >
                        <div
                            class="dropdown-menu dropdown-menu-right"
                            style="z-index: 1050;"
                            x-placement="left-start"
                        >
                            <button
                                v-permission="'navigator_edit'"
                                :name="'edit-navigatorItem-' + navigatorItem.id"
                                class="dropdown-item text-secondary"
                                @click.prevent="editNavigatorItem(navigatorItem)"
                            >
                                <i class="fa fa-pencil-alt mr-2"></i>
                                {{ trans('global.navigatorView.edit') }}
                            </button>
                            <hr class="my-1">
                            <button
                                v-permission="'navigator_delete'"
                                :id="'delete-navigatorView-' + navigatorItem.id"
                                type="submit"
                                class="dropdown-item py-1 text-red"
                                @click.prevent="confirmItemDelete(navigatorItem)"
                            >
                                <i class="fa fa-trash mr-2"></i>
                                {{ trans('global.navigatorItem.delete') }}
                            </button>
                        </div>
                    </template>
                </IndexWidget>
            </span>
        </div>
        <div
            id="navigatorView-datatable-wrapper"
            class="w-100 dataTablesWrapper"
        >
            <DataTable
                id="navigatorItem-datatable"
                :columns="columns"
                :options="options"
                :ajax="'/navigatorViews/' + view.id + '/list'"
                :search="search"
                width="100%"
                style="display: none;"
            />
        </div>

        <div v-for="navigatorItem in navigatorItems">
            <div v-if="navigatorItem.position == 'footer'">
                <Content v-if="navigatorItem.referenceable_type == 'App\\Content'"
                    :content="navigatorItem"
                />
                <div v-if="navigatorItem.referenceable_type != 'App\\Content'"
                    class="col-12"
                >
                    <div class="card">
                        <div class="card-body">
                            {{ navigatorItem.title }}
                            <button
                                v-permission="'navigator_edit'"
                                :name="'edit-navigatorItem-' + navigatorItem.id"
                                class="dropdown-item text-secondary"
                                @click.prevent="editNavigatorItem(navigatorItem)"
                            >
                                <i class="fa fa-pencil-alt mr-2"></i>
                                {{ trans('global.navigatorView.edit') }}
                            </button>
                            <button
                                v-permission="'navigator_delete'"
                                :id="'delete-navigatorView-' + navigatorItem.id"
                                type="submit"
                                class="dropdown-item py-1 text-red"
                                @click.prevent="confirmItemDelete(navigatorItem)"
                            >
                                <i class="fa fa-trash mr-2"></i>
                                {{ trans('global.navigatorItem.delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <NavigatorItemModal
                :navigator="navigator"
                :view="view"
            />
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.navigatorItem.delete')"
                :description="trans('global.navigatorItem.delete_helper')"
                @close="() => {
                    this.showConfirm = false;
                }"
                @confirm="() => {
                    this.showConfirm = false;
                    this.destroy();
                }"
            />
        </Teleport>
    </div>
</template>
<script>
import NavigatorItemModal from "../navigator/NavigatorItemModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import Content from "../content/Content.vue";
import {useGlobalStore} from "../../store/global.js";
DataTable.use(DataTablesCore);

export default {
    props: {
        navigator: {
            type: Object,
            default: null,
        },
        view: {
            type: Object,
            default: null,
        },
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            navigatorItems: null,
            search: '',
            showConfirm: false,
            errors: {},
            currentNavigatorItem: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
                { title: 'description', data: 'description', searchable: true},
                { title: 'referenceable_type', data: 'referenceable_type'},
                { title: 'referenceable_id', data: 'referenceable_id'},
                { title: 'position', data: 'position'},
                { title: 'css_class', data: 'css_class'},
                { title: 'visibility', data: 'visibility'},
                { title: 'url', data: 'url'},
            ],
            options : this.$dtOptions,
            modalMode: 'edit'
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('navigatorItem-added', (navigatorItem) => {
            this.globalStore?.closeModal('navigator-item-modal');
            this.navigatorItems.push(navigatorItem);
        });

        this.$eventHub.on('navigatorItem-updated', (navigatorView) => {
            this.globalStore?.closeModal('navigator-item-modal');
            this.update(navigatorView);
        });
        this.$eventHub.on('createNavigatorItem', () => {
            this.globalStore?.showModal('navigator-item-modal', {});
        });
    },
    methods: {
        editNavigatorItem(navigatorItem) {
            this.globalStore?.showModal('navigator-item-modal', navigatorItem);
        },
        loaderEvent() {
            const dt = $('#navigatorItem-datatable').DataTable();
            dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.navigatorItems = dt.rows({page: 'current'}).data().toArray();

                $('#navigatorItem-content').insertBefore('#navigatorItem-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                dt.search(filter).draw();
            });
        },
        confirmItemDelete(navigatorItem) {
            this.currentNavigatorItem = navigatorItem;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/navigatorItems/' + this.currentNavigatorItem.id)
                .then(res => {
                    let index = this.navigatorItems.indexOf(this.currentNavigatorItem);
                    this.navigatorItems.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        update(navigatorItem) {
            const index = this.navigatorItems.findIndex(
                vc => vc.id === navigatorItem.id
            );

            for (const [key, value] of Object.entries(navigatorItem)) {
                this.navigatorItems[index][key] = value;
            }
        }
    },
    components: {
        Content,
        ConfirmModal,
        DataTable,
        NavigatorItemModal,
        IndexWidget
    },
}
</script>