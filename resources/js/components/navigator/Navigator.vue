<template>
    <div class="d-flex flex-column">
        <div
            id="navigatorView-content"
            class="px-3"
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
                                    <i class="fa fa-trash me-2"></i>
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
                    <template #icon>
                        <i class="fa fa-history pt-2"></i>
                    </template>

                    <template v-if="navigatorItem.referenceable?.archived" #badges>
                        <p class="text-muted small">
                            <span
                                class="btn btn-info btn-xs position-absolute select-all pull-right me-1"
                                style="bottom: 0; margin: 5px 40px 8px 0; width: max-content; right: 5px;"
                            >
                                <i class="fa fa-archive" aria-hidden="true"></i>
                                {{ trans('global.curriculum.fields.archived') }}
                            </span>
                        </p>
                    </template>

                    <template #dropdown
                        v-permission="'navigator_edit, navigator_delete'"
                    >
                        <div class="dropdown-menu dropdown-menu-end">
                            <button
                                v-permission="'navigator_edit'"
                                type="button"
                                class="dropdown-item"
                                @click="editNavigatorItem(navigatorItem)"
                            >
                                <i class="fa fa-pencil-alt"></i>
                                {{ trans('global.navigatorView.edit') }}
                            </button>

                            <hr class="my-1">

                            <button
                                v-permission="'navigator_delete'"
                                type="submit"
                                class="dropdown-item text-danger"
                                @click="confirmItemDelete(navigatorItem)"
                            >
                                <i class="fa fa-trash"></i>
                                {{ trans('global.navigatorItem.delete') }}
                            </button>
                        </div>
                    </template>
                </IndexWidget>
            </span>
        </div>

        <DataTable
            ref="datatable"
            id="navigator-item-datatable"
            :columns="columns"
            :options="$dtOptions"
            :ajax="'/navigatorViews/' + view.id + '/list'"
            class="d-none"
            @xhr="(e, settings, json) => navigatorItems = json.data"
        />

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
                                <i class="fa fa-pencil-alt me-2"></i>
                                {{ trans('global.navigatorView.edit') }}
                            </button>
                            <button
                                v-permission="'navigator_delete'"
                                :id="'delete-navigatorView-' + navigatorItem.id"
                                type="submit"
                                class="dropdown-item py-1 text-red"
                                @click.prevent="confirmItemDelete(navigatorItem)"
                            >
                                <i class="fa fa-trash me-2"></i>
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
                @close="showConfirm = false"
                @confirm="destroy()"
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
DataTable.use(DataTablesCore);

export default {
    components: {
        Content,
        ConfirmModal,
        DataTable,
        NavigatorItemModal,
        IndexWidget
    },
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
    data() {
        return {
            component_id: this.$.uid,
            navigatorItems: null,
            showConfirm: false,
            currentNavigatorItem: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
                { title: 'description', data: 'description', searchable: true },
                { title: 'referenceable_type', data: 'referenceable_type' },
                { title: 'referenceable_id', data: 'referenceable_id' },
                { title: 'position', data: 'position' },
                { title: 'css_class', data: 'css_class' },
                { title: 'visibility', data: 'visibility' },
                { title: 'url', data: 'url' },
            ],
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.dt = this.$refs.datatable.dt;

        this.$eventHub.on('navigatorItem-added', navigatorItem => {
            this.navigatorItems.push(navigatorItem);
        });

        this.$eventHub.on('navigatorItem-updated', updatedNavigatorItem => {
            let navigatorItem = this.navigatorItems.find(item => item.id === updatedNavigatorItem.id);

            Object.assign(navigatorItem, updatedNavigatorItem);
        });

        this.$eventHub.on('filter', filter => {
            dt.search(filter.searchString).draw();
        });
    },
    methods: {
        editNavigatorItem(navigatorItem) {
            this.globalStore.showModal('navigator-item-modal', navigatorItem);
        },
        confirmItemDelete(navigatorItem) {
            this.currentNavigatorItem = navigatorItem;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/navigatorItems/' + this.currentNavigatorItem.id)
                .then(res => {
                    this.showConfirm = false;
                    let index = this.navigatorItems.indexOf(this.currentNavigatorItem);
                    this.navigatorItems.splice(index, 1);
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