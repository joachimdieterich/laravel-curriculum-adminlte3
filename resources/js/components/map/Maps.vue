<template >
    <div class="row">
        <div class="col-md-12 ">
            <ul v-if="typeof (this.subscribable_type) == 'undefined' && typeof(this.subscribable_id) == 'undefined'"
                class="nav nav-pills py-2" role="tablist">
                <li class="nav-item">
                    <a class="nav-link "
                       :class="filter === 'all' ? 'active' : ''"
                       id="map-filter-all"
                       @click="setFilter('all')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fas fa-chalkboard-teacher pr-2"></i>Alle Karten
                    </a>
                </li>
                <li v-can="'map_create'"
                    class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'owner' ? 'active' : ''"
                       id="custom-filter-owner"
                       @click="setFilter('owner')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fa fa-user  pr-2"></i>Meine Karten
                    </a>
                </li>
                <li v-can="'map_create'"
                    class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'shared_with_me' ? 'active' : ''"
                       id="custom-filter-shared-with-me"
                       @click="setFilter('shared_with_me')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fa fa-paper-plane pr-2"></i>Für mich freigegeben
                    </a>
                </li>
                <li v-can="'map_create'"
                    class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'shared_by_me' ? 'active' : ''"
                       id="custom-tabs-shared-by-me"
                       @click="setFilter('shared_by_me')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fa fa-share-nodes  pr-2"></i>Von mir freigegeben
                    </a>
                </li>

            </ul>
        </div>

        <table id="map-datatable" style="display: none;"></table>
        <div id="map-content" >
            <div v-for="map in maps"
                 v-if="(map.title.toLowerCase().indexOf(search.toLowerCase()) !== -1)
                || search.length < 3"
                 :id="map.id"
                 v-bind:value="map.id"
                 class="box box-objective nav-item-box-image pointer my-1 "
                 style="min-width: 200px !important;"
                 :style="'border-bottom: 5px solid ' + map.color"
            >
                <a :href="'/maps/' + map.id"
                   class="text-decoration-none text-black"
                >
                    <div v-if="map.medium_id"
                         class="nav-item-box-image-size"
                         :style="{'background': 'url(/media/' + map.medium_id + '?model=map&model_id=' + map.id +') top center no-repeat', 'background-size': 'cover', }">
                        <div class="nav-item-box-image-size"
                             style="width: 100% !important;"
                             :style="{backgroundColor: map.color + ' !important',  'opacity': '0.5'}">
                        </div>
                    </div>
                    <div v-else
                         class="nav-item-box-image-size text-center"
                         :style="{backgroundColor: map.color + ' !important'}">
                    </div>
                    <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                   <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                       {{ map.title }}
                   </h1>
                   <p class="text-muted small">
                    {{ htmlToText(map.description) }}
                   </p>
                </span>
                    <div class="symbol"
                         :style="'color:' + $textcolor(map.color) + '!important'"
                         style="position: absolute; width: 30px; height: 40px;">
                        <i class="fa fa-map-location-dot pt-2"></i>
                    </div>
                    <div v-if="$userId == map.owner_id"
                         class="btn btn-flat pull-right"
                         :id="'mapDropdown_' + map.id"
                         style="position:absolute; top:0; right: 0; background-color: transparent;"
                         data-toggle="dropdown"
                         aria-expanded="false"
                        >
                        <i class="fas fa-ellipsis-v"
                           :style="'color:' + $textcolor(map.color)"></i>
                        <div class="dropdown-menu dropdown-menu-right"
                             style="z-index: 1050;"
                             x-placement="left-start">
                            <button :name="'map-edit_' + map.id"
                                    class="dropdown-item text-secondary"
                                    @click.prevent="editMap(map)">
                                <i class="fa fa-pencil-alt mr-2"></i>
                                {{ trans('global.map.edit') }}
                            </button>
                            <button :name="'map-share_' + map.id"
                                    class="dropdown-item text-secondary"
                                    @click.prevent="shareMap(map)">
                                <i class="fa fa-share-alt mr-2"></i>
                                {{ trans('global.map.share') }}
                            </button>
                            <hr class="my-1">
                            <button
                                :id="'delete-map-' + map.id"
                                type="submit"
                                class="dropdown-item py-1 text-red"
                                @click.prevent="confirmItemDelete(map)">
                                <i class="fa fa-trash mr-2"></i>
                                {{ trans('global.map.delete') }}
                            </button>
                        </div>
                    </div>
                </a>
            </div>
            <map-index-add-widget
                v-if="((this.filter == 'all' && typeof (this.subscribable_type) == 'undefined' && typeof(this.subscribable_id) == 'undefined')|| this.filter  == 'owner') "
                v-can="'map_create'"/>
        </div>
        <Modal
            id="mapModal"
            css="danger"
            :title="trans('global.map.delete')"
            :text="trans('global.map.delete_helper')"
            :ok_label="trans('global.map.delete')"
            v-on:ok="destroy()"
        />
    </div>
</template>

<script>
import MapIndexAddWidget from "./MapIndexAddWidget";
const Modal =
    () => import('./../uiElements/Modal');

export default {
    props: {
        subscribable_type: '',
        subscribable_id: '',
    },
    data() {
        return {
            maps: [],
            subscriptions: {},
            search: '',
            url: '/maps/list',
            errors: {},
            currentMap: {},
            filter: 'all'
        }
    },
    methods: {
        confirmItemDelete(map){
            $('#mapModal').modal('show');
            this.currentMap = map;
        },
        editMap(map){
            this.$eventHub.$emit('edit_map', map);
            //window.location = "/maps/" + map.id + "/edit";
        },
        shareMap(map){
            this.$modal.show('subscribe-modal', { 'modelId': map.id, 'modelUrl': 'map' , 'shareWithToken': true});
        },
        loaderEvent(){
            if (typeof (this.subscribable_type) !== 'undefined' && typeof(this.subscribable_id) !== 'undefined'){
                this.url = '/mapSubscriptions?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id
            } else {
                this.url = '/maps/list?filter=' + this.filter
            }

            if ($.fn.dataTable.isDataTable( '#map-datatable' )){
                $('#map-datatable').DataTable().ajax.url(this.url).load();
            } else {
                const dtObject = $('#map-datatable').DataTable({
                    ajax: this.url,
                    dom: 'tilpr',
                    pageLength: 50,
                    language: {
                        url: '/datatables/i18n/German.json',
                        paginate: {
                            "first":      '<i class="fa fa-angle-double-left"></id>',
                            "last":       '<i class="fa fa-angle-double-right"></id>',
                            "next":       '<i class="fa fa-angle-right"></id>',
                            "previous":   '<i class="fa fa-angle-left"></id>',
                        },
                    },
                    columns: [
                        { title: 'id', data: 'id' },
                        { title: 'title', data: 'title' },
                        { title: 'subtitle', data: 'subtitle', searchable: true},
                        { title: 'tags', data: 'tags', searchable: true },

                    ],
                }).on('draw.dt', () => { // checks if the datatable-data changes, to update the map-data
                    this.maps = dtObject.rows({ page: 'current' }).data().toArray();
                    $('#map-content').insertBefore('#map-datatable');
                });
            }
        },
        setFilter(filter){
            this.filter = filter;
            this.loaderEvent();
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
    mounted() {
        if (document.getElementById('searchbar') != null) {
            document.getElementById('searchbar').classList.remove('d-none');
        }

        const filters = ["all", "owner", "shared_with_me", "shared_by_me"];
        let url = new URL(window.location.href);
        let urlFilter = url.searchParams.get("filter");

        if (filters.includes(urlFilter)){
          this.filter = urlFilter
        }

        this.$eventHub.$on('filter', (filter) => {
            $('#map-datatable').DataTable().search(filter).draw();
        });
        this.$eventHub.$on('map-added', (map) => {
            this.maps.push(map);
        });
        this.$eventHub.$on('map-updated', (map) => {
            //console.log(map);
            const index = this.maps.findIndex(
                vc => vc.id === map.id
            );

            for (const [key, value] of Object.entries(map)) {
                this.maps[index][key] = value;
            }
        });
        this.loaderEvent()
    },

    components: {
        MapIndexAddWidget,
        Modal
    },
}
</script>
<style>
#map-datatable_wrapper {
    width: 100%;
    padding: 0px 15px;
}
</style>
<style scoped>
.nav-link:hover {
    cursor: default;
    user-select: none;
}

.nav-item:hover .nav-link:not(.active) {
    background-color: rgba(0, 0, 0, 0.1);
    cursor: pointer;
}
</style>
