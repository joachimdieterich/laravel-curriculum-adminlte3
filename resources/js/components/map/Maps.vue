<template >
    <div class="row">
        <div class="col-md-12 ">
            <ul v-if="typeof (this.subscribable_type) == 'undefined' && typeof(this.subscribable_id) == 'undefined'"
                class="nav nav-pills py-2" role="tablist">
                <li class="nav-item">
                    <a class="nav-link "
                       :class="filter === 'all' ? 'active' : ''"
                       id="curriculum-filter-all"
                       @click="setFilter('all')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fas fa-th pr-2"></i>{{ trans('global.all') }} {{ trans('global.curriculum.title') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'by_organization' ? 'active' : ''"
                       id="custom-filter-by-organization"
                       @click="setFilter('by_organization')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fas fa-university pr-2"></i>{{ trans('global.my') }} {{ trans('global.organization.title_singular') }}
                    </a>
                </li>
                <li v-can="'curriculum_create'"
                    class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'owner' ? 'active' : ''"
                       id="custom-filter-owner"
                       @click="setFilter('owner')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fa fa-user pr-2"></i>{{ trans('global.my') }} {{ trans('global.curriculum.title') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'shared_with_me' ? 'active' : ''"
                       id="custom-filter-shared-with-me"
                       @click="setFilter('shared_with_me')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fa fa-paper-plane pr-2"></i>{{ trans('global.shared_with_me') }}
                    </a>
                </li>
                <li v-can="'curriculum_create'"
                    class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'shared_by_me' ? 'active' : ''"
                       id="custom-tabs-shared-by-me"
                       @click="setFilter('shared_by_me')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fa fa-share-nodes  pr-2"></i>{{ trans('global.shared_by_me') }}
                    </a>
                </li>
            </ul>
        </div>

        <div id="map-content"
             class="col-md-12 m-0">
            <IndexWidget
                v-permission="'map_create'"
                key="'mapCreate'"
                modelName="Map"
                url="/maps"
                :create=true
                :createLabel="trans('global.map.create')">
            </IndexWidget>
            <IndexWidget
                v-for="map in maps"
                :key="'mapIndex'+map.id"
                :model="map"
                modelName= "map"
                url="/maps">
                <template v-slot:icon>
                    <i class="fa fa-map-location-dot pt-2"></i>
                </template>

                <template
                    v-permission="'map_edit, map_delete'"
                    v-slot:dropdown>
                    <div class="dropdown-menu dropdown-menu-right"
                         style="z-index: 1050;"
                         x-placement="left-start">
                        <button
                            v-permission="'map_edit'"
                            :name="'edit-map-' + map.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editMap(map)">
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.map.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'map_delete'"
                            :id="'delete-map-' + map.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(map)">
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.map.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div id="map-datatable-wrapper"
             class="w-100 dataTablesWrapper">
            <DataTable
                id="map-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display:none; "
            ></DataTable>
        </div>

        <Teleport to="body">
            <MapModal
                :show="this.showMapModal"
                @close="this.showMapModal = false"
                :params="currentMap"
            ></MapModal>
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
            ></ConfirmModal>
        </Teleport>
    </div>
</template>


<script>
import MapModal from "../map/MapModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
DataTable.use(DataTablesCore);

export default {
    props: {
        subscribable_type: '',
        subscribable_id: '',
    },
    data() {
        return {
            component_id: this.$.uid,
            maps: null,
            search: '',
            showMapModal: false,
            showConfirm: false,
            url: '/maps/list',
            errors: {},
            currentMap: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
            ],
            options : this.$dtOptions,
            modalMode: 'edit',
            filter: 'all',
            dt: null
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('map-added', (map) => {
            this.showMapModal = false;
            this.maps.push(map);
        });

        this.$eventHub.on('map-updated', (map) => {
            this.showMapModal = false;
            this.update(map);
        });
        this.$eventHub.on('createMap', () => {
            this.currentMap = {};
            this.showMapModal = true;
        });
    },
    methods: {
        setFilter(filter){
            this.filter = filter;
            if (typeof (this.subscribable_type) !== 'undefined' && typeof(this.subscribable_id) !== 'undefined'){
                this.url = '/mapSubscriptions?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id
            } else {
                this.url = '/maps/list?filter=' + this.filter
            }

            this.dt.ajax.url(this.url).load();
        },
        editMap(map){
            this.currentMap = map;
            this.showMapModal = true;
        },
        loaderEvent(){
            this.dt = $('#map-datatable').DataTable();

            this.dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.maps = this.dt.rows({page: 'current'}).data().toArray();

                $('#map-content').insertBefore('#map-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                this.dt.search(filter).draw();
            });
        },
        confirmItemDelete(map){
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
        update(map) {
            const index = this.maps.findIndex(
                vc => vc.id === map.id
            );

            for (const [key, value] of Object.entries(map)) {
                this.maps[index][key] = value;
            }
        }
    },
    components: {
        ConfirmModal,
        DataTable,
        MapModal,
        IndexWidget
    },
}
</script>
