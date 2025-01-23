<template >
    <div class="row">
        <div class="col-md-12 ">
            <ul
                class="nav nav-pills py-2"
                role="tablist"
            >
                <li class="nav-item pointer">
                    <a
                        id="curriculum-filter-all"
                        class="nav-link"
                        :class="filter === 'all' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('all')"
                    >
                        <i class="fas fa-map-location-dot pr-2"></i>
                        {{ trans('global.all') }} {{ trans('global.map.title') }}
                    </a>
                </li>
                <li class="nav-item pointer">
                    <a
                        id="custom-filter-by-organization"
                        class="nav-link"
                        :class="filter === 'by_organization' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('by_organization')"
                    >
                        <i class="fas fa-university pr-2"></i>
                        {{ trans('global.my') }} {{ trans('global.organization.title_singular') }}
                    </a>
                </li>
                <li
                    v-permission="'map_create'"
                    class="nav-item pointer"
                >
                    <a
                        id="custom-filter-owner"
                        class="nav-link"
                        :class="filter === 'owner' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('owner')"
                    >
                        <i class="fa fa-user pr-2"></i>
                        {{ trans('global.my') }} {{ trans('global.map.title') }}
                    </a>
                </li>
                <li class="nav-item pointer">
                    <a
                        id="custom-filter-shared-with-me"
                        class="nav-link"
                        :class="filter === 'shared_with_me' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('shared_with_me')"
                    >
                        <i class="fa fa-paper-plane pr-2"></i>
                        {{ trans('global.shared_with_me') }}
                    </a>
                </li>
                <li
                    v-permission="'map_create'"
                    class="nav-item pointer"
                >
                    <a
                        id="custom-tabs-shared-by-me"
                        class="nav-link"
                        :class="filter === 'shared_by_me' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('shared_by_me')"
                    >
                        <i class="fa fa-share-nodes  pr-2"></i>
                        {{ trans('global.shared_by_me') }}
                    </a>
                </li>
            </ul>
        </div>

        <div
            id="map-content"
            class="col-md-12 m-0"
        >
            <IndexWidget
                v-permission="'map_create'"
                key="mapCreate"
                modelName="map"
                url="/maps"
                :create=true
                :label="trans('global.map.create')"
            />
            <IndexWidget v-for="map in maps"
                :key="'mapIndex' + map.id"
                :model="map"
                modelName="map"
                url="/maps"
            >
                <template v-slot:icon>
                    <i class="fa fa-map-location-dot pt-2"></i>
                </template>

                <template v-slot:dropdown
                    v-permission="'map_edit, map_delete'"
                >
                    <div
                        class="dropdown-menu dropdown-menu-right"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            v-permission="'map_edit'"
                            :name="'edit-map-' + map.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editMap(map)"
                        >
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.map.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'map_delete'"
                            :id="'delete-map-' + map.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(map)"
                        >
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.map.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div
            id="map-datatable-wrapper"
            class="w-100 dataTablesWrapper"
        >
            <DataTable
                id="map-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display: none;"
            />
        </div>

        <Teleport to="body">
            <MapModal/>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.map.delete')"
                :description="trans('global.map.delete_helper')"
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
import MapModal from "../map/MapModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import {useGlobalStore} from "../../store/global";
DataTable.use(DataTablesCore);

export default {
    props: {},
    setup () {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            maps: null,
            search: '',
            showConfirm: false,
            url: '/maps/list',
            errors: {},
            currentMap: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
                { title: 'description', data: 'description', searchable: true },
            ],
            options : this.$dtOptions,
            filter: 'all',
            dt: null,
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('map-added', (map) => {
            this.maps.push(map);
        });

        this.$eventHub.on('map-updated', (updatedMap) => {
            let map = this.maps.find(m => m.id === updatedMap.id);

            Object.assign(map, updatedMap);
        });

        this.$eventHub.on('filter', (filter) => {
            this.dt.search(filter).draw();
        });
    },
    methods: {
        setFilter(filter) {
            this.filter = filter;
            this.url = '/maps/list?filter=' + this.filter
            this.dt.ajax.url(this.url).load();
        },
        editMap(map) {
            this.globalStore?.showModal('map-modal', map);
        },
        loaderEvent() {
            this.dt = $('#map-datatable').DataTable();

            this.dt.on('draw.dt', () => {
                this.maps = this.dt.rows({page: 'current'}).data().toArray();

                $('#map-content').insertBefore('#map-datatable-wrapper');
            });
        },
        confirmItemDelete(map) {
            this.currentMap = map;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/maps/' + this.currentMap.id)
                .then(res => {
                    let index = this.maps.indexOf(this.currentMap);
                    this.maps.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
    },
    components: {
        ConfirmModal,
        DataTable,
        MapModal,
        IndexWidget,
    },
}
</script>