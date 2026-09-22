<template>
    <div class="d-flex flex-column">
        <ul
            class="nav nav-pills px-3 py-2"
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
                    <i class="fas fa-map-location-dot pe-2"></i>
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
                    <i class="fas fa-university pe-2"></i>
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
                    <i class="fa fa-user pe-2"></i>
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
                    <i class="fa fa-paper-plane pe-2"></i>
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
                    <i class="fa fa-share-nodes  pe-2"></i>
                    {{ trans('global.shared_by_me') }}
                </a>
            </li>
        </ul>

        <div
            id="map-content"
            class="px-3"
        >
            <IndexWidget
                v-permission="'map_create'"
                key="mapCreate"
                modelName="Map"
                url="/maps"
                :create=true
                :label="trans('global.map.create')"
            />
            <IndexWidget v-for="map in maps"
                :key="'mapIndex' + map.id"
                :model="map"
                modelName="Map"
                url="/maps"
            >
                <template #icon>
                    <i class="fa fa-map-location-dot"></i>
                </template>

                <template #dropdown
                    v-permission="'map_edit, map_delete'"
                >
                    <div
                        class="dropdown-menu dropdown-menu-end"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            v-permission="'map_edit'"
                            :name="'edit-map-' + map.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editMap(map)"
                        >
                            <i class="fa fa-pencil-alt me-2"></i>
                            {{ trans('global.map.edit') }}
                        </button>

                        <button
                            :name="'map-share_' + map.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="shareMap(map)"
                        >
                            <i class="fa fa-share-alt me-2"></i>
                            {{ trans('global.map.share') }}
                        </button>

                        <hr class="my-1">
                        <button
                            v-permission="'map_delete'"
                            :id="'delete-map-' + map.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(map)"
                        >
                            <i class="fa fa-trash me-2"></i>
                            {{ trans('global.map.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <DataTable
            ref="datatable"
            id="map-datatable"
            :columns="columns"
            :options="options"
            ajax="/maps/list"
            class="d-none"
            @xhr="(e, settings, json) => maps = json.data"
        />

        <Teleport to="body">
            <MapModal/>
            <MediumModal/>
            <SubscribeModal/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.map.delete')"
                :description="trans('global.map.delete_helper')"
                @close="showConfirm = false"
                @confirm="() => {
                    showConfirm = false;
                    destroy();
                }"
            />
        </Teleport>
    </div>
</template>
<script>
import MapModal from "../map/MapModal.vue";
import SubscribeModal from "../subscription/SubscribeModal.vue";
import MediumModal from "../media/MediumModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
DataTable.use(DataTablesCore);

export default {
    components: {
        ConfirmModal,
        SubscribeModal,
        DataTable,
        MapModal,
        IndexWidget,
        MediumModal,
    },
    data() {
        return {
            component_id: this.$.uid,
            maps: null,
            showConfirm: false,
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
        this.globalStore['showSearchbar'] = true;

        this.dt = this.$refs.datatable.dt;

        this.$eventHub.on('map-added', map => {
            this.maps.push(map);
        });

        this.$eventHub.on('map-updated', updatedMap => {
            let map = this.maps.find(m => m.id === updatedMap.id);

            Object.assign(map, updatedMap);
        });

        this.$eventHub.on('filter', filter => {
            this.dt.search(filter.searchString).draw();
        });
    },
    methods: {
        setFilter(filter) {
            this.filter = filter;
            this.dt.ajax.url('/maps/list?filter=' + this.filter).load();
        },
        editMap(map) {
            this.globalStore.showModal('map-modal', map);
        },
        shareMap(map) {
            this.globalStore.showModal('subscribe-modal', {
                modelId: map.id,
                modelUrl: 'map' ,
                shareWithUsers: true,
                shareWithGroups: true,
                shareWithOrganizations: true,
                shareWithToken: true,
                canEditCheckbox: true,
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
                .catch(e => {
                    console.log(e);
                });
        },
    },
}
</script>
