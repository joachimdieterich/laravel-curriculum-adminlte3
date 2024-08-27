<template>
    <div id="outermap">
        <div id="sidebar"
             class="sidebar">
            <!-- navigation tabs -->
            <div class="sidebar-tabs">
                <ul role="tablist">
                    <li><a href="#ll-home" role="tab">
                        <i class="fa fa-home"></i></a></li>
                    <li v-if="$userId == map.owner_id">
                        <a href="#ll-layer" role="tab">
                            <i class="fa fa-layer-group"></i>
                        </a>
                    </li>
                    <li v-if="currentMarker">
                        <a href="#ll-marker" role="tab">
                            <i class="fa fa-location-dot"></i>
                        </a>
                    </li>
                    <li v-if="$userId == map.owner_id">
                        <a href="#ll-search" role="tab">
                            <i class="fa fa-search"></i>
                        </a>
                    </li>
                    <hr v-if="$userId == map.owner_id">
                    <li v-if="$userId == map.owner_id">
                        <a role="button"
                            @click="createMarker()"
                        >
                            <i class="fa fa-plus"></i>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- tab panes -->
            <div class="sidebar-content">
                <div class="sidebar-pane active"
                     id="ll-home"
                >
                    <h1 class="sidebar-header mb-3">
                        {{ this.map.title }}
                    </h1>
                    <span class="pb-2">
                        <h5 >{{ this.map.subtitle }}</h5>
                        <span class="right badge badge-primary">{{ this.map.type.title }}</span>
                    </span>

                    <p class="pt-2"
                        v-dompurify-html="this.map.description"
                    ></p>

                    <h5 class="pt-2">{{ trans('global.entries') }}</h5>
                    <ul class="todo-list">
                        <li v-for="marker in this.selectedMarkers">
                            <i class="fa fa-location-dot pr-2"></i>
                            <a @click="setCurrentMarker(marker)"
                               class="text-decoration-none"
                            >
                                {{ marker.title }}
                            </a>
                            <div v-if="$userId == marker.owner_id || $userId == map.owner_id"
                                 class="tools user-select-none"
                            >
                                <i class="text-secondary fa fa-pencil-alt" @click="edit(marker)"></i>
                                <i class="text-danger fa fa-trash ml-2" @click="confirmItemDelete(marker)"></i>
                            </div>
                        </li>
                    </ul>

                </div>

                <div class="sidebar-pane" id="ll-layer">
                    <h1 class="sidebar-header mb-3">
                        Ebenen
                    </h1>

                    <div v-if="mapMarkerTypes"
                         class="form-group"
                    >
                        <label for="mapMarkerType">Typ</label>
                        <select
                            id="mapMarkerType"
                            v-model="form.type_id"
                            class="form-control select2"
                            style="width:100%;"
                        >
                            <option v-for="markerType in mapMarkerTypes"
                                    :value="markerType.id"
                            >
                                {{ markerType.title }}
                            </option>
                        </select>
                    </div> <!-- mapMarkerTypes -->
                    <div v-if="mapMarkerCategories"
                         class="form-group"
                    >
                        <label for="mapMarkerCategory">Kategorie</label>
                        <select
                            id="mapMarkerCategory"
                            v-model="form.category_id"
                            class="form-control select2"
                            style="width:100%;"
                        >
                            <option v-for="category in mapMarkerCategories"
                                    :value="category.id"
                            >
                                {{ category.title }}
                            </option>
                        </select>
                    </div> <!-- mapMarkerCategory -->
                    <button class="btn btn-primary pull-right"
                            @click="loadMarkers()"
                    >
                        <i class="fa fa-check"></i>
                    </button>
                </div>

                <div v-if="typeof currentMarker?.ARTIKEL == 'undefined'"
                     id="ll-marker"
                     class="sidebar-pane"
                     :class="currentMarker === undefined && 'd-none'"
                >
                    <MarkerView v-if="currentMarker !== undefined"
                        :marker="this.currentMarker"
                        :map="this.map"
                    />
                </div>
                <div v-else-if="currentMarker?.ARTIKEL !== undefined"
                     class="sidebar-pane" id="ll-marker"
                >
                    <h1 class="sidebar-header  mb-3">
                        {{ this.currentMarker.ARTIKEL }}
                    </h1>

                    <div class="py-0 pt-2"
                         v-if="this.currentMarker.BEZ_1_2.length > 2"
                    >
                        <strong>Untertitel</strong>
                    </div>
                    <div class="py-0 pre-formatted"
                         v-if="this.currentMarker.BEZ_1_2.length > 2"
                         v-dompurify-html="this.currentMarker.BEZ_1_2"
                    ></div>

                    <div class="py-0 pt-2">
                        <strong>Beschreibung</strong>
                    </div>
                    <div class="py-0 pre-formatted"
                         style="text-align:justify;"
                         v-dompurify-html="this.currentMarker.BEMERKUNG"
                    ></div>

                    <div class="py-0 pt-2"><strong>Termine</strong></div>
                    <div class="py-0 pre-formatted">
                        <div v-for="termin in this.currentMarker.termine">
                            {{ dateforHumans(termin.DATUM) }}, {{ termin.BEGINN }} - {{ termin.ENDE }}
                            <br/>
                            {{ termin.VO_ORT }}
                        </div>
                    </div>

                    <div class="py-0 pt-2"><strong>VA-Nummer</strong></div>
                    <div class="py-0 pre-formatted" v-dompurify-html="this.currentMarker.ARTIKEL_NR"></div>

                    <div class="py-0 pt-2">
                        <a :href="this.currentMarker.LINK_DETAIL"
                           class="btn btn-default"
                           target="_blank"
                        >
                            <i class="fa fa-info"></i> Details/Anmeldung
                        </a>

                        <a :href="this.currentMarker.LINK_DETAIL+'&print=1'"
                           onclick="return !window.open(this.href, 'Drucken', 'width=800,scrollbars=1')"
                           class="btn btn-default"
                           target="_blank"
                        >
                            <i class="fa fa-print"></i> Drucken
                        </a>
                    </div>
                </div>

                <div class="sidebar-pane" id="ll-search">
                    <h1 class="sidebar-header  mb-3">
                        {{ this.currentMarker?.title ?? '' }}
                    </h1>

                    <div class="form-group"
                         :class="form.errors.search ? 'has-error' : ''"
                    >
                        <label for="ll-search">{{ trans('global.search') }}</label>
                        <input
                            type="text" id="ll-search"
                            name="ll-search"
                            class="form-control"
                            v-model="search"
                            @keyup.enter="markerSearch"
                            placeholder="Suchbegriff..."
                        />
                        <p class="help-block" v-if="form.errors.search" v-text="form.errors.search[0]"></p>
                    </div>
                </div>
            </div>
        </div>

        <div id="map" class="sidebar-map"></div>

        <!-- Create Modal -->
        <MarkerCreate
            v-can="'marker_create'"
            id="modal-marker-form"
            :method="method"
            :marker="marker"
            :map="map"
        />
        <Modal
            :id="'deleteMarkerModal'"
            css="danger"
            :title="trans('global.marker.delete')"
            :text="trans('global.marker.delete_helper')"
            :ok_label="trans('global.marker.delete')"
            v-on:ok="deleteMarker"
        />
    </div>
</template>
<script>

import axios from 'axios';
import "leaflet/dist/leaflet.js";
import {Icon} from 'leaflet';
import "sidebar-v2/js/leaflet-sidebar.js";
import "leaflet.markercluster/dist/leaflet.markercluster.js";
import Form from "form-backend-validation";
import "leaflet-extra-markers/dist/js/leaflet.extra-markers.js"
import MarkerCreate from "./MarkerCreate";
import moment from "moment/moment";
import MarkerView from "./MarkerView";
const Modal =
    () => import('./../uiElements/Modal');

export default {
    components: {
        MarkerView,
        MarkerCreate,
        Modal,
    },
    props: {
        map: {
            type: Object,
            default: {},
        },
    },
    data() {
        return {
            component_id: this._uid,
            mapCanvas: [],
            events: {},
            sidebar: {},
            search: 'digiWerkzeug',
            bordersGroup: {},
            namesGroup: {},
            markers: {},
            selectedMarkers: {},
            currentMarker: undefined,
            clusterGroup: {},
            mapMarkerTypes: {},
            mapMarkerCategories: {},
            form: new Form({
                'type_id': '',
                'category_id': '',
            }),
            initialLatitude: '49.314908280766346',
            initialLongitude: '8.413913138283617',
            zoom: 10,
            marker: null,
            method: 'post',
        }
    },
    methods: {
        loader() {
            axios.get('/mapMarkerTypes')
                .then(res => {
                    this.mapMarkerTypes = res.data.mapMarkerTypes;axios.get('/mapMarkerCategories')
                        .then(res => {
                            this.mapMarkerCategories = res.data.mapMarkerCategories;

                            this.initMap(); //! after makerTypes and Categories are loaded
                            this.syncSelect2();
                            this.loadMarkers();
                        })
                        .catch(err => {
                            console.log(err);
                        });
                })
                .catch(err => {
                    console.log(err);
                });
        },
        createMarker(method = 'post') {
            this.method = method;
            $('#modal-marker-form').modal('show');
        },
        loadMarkers() {
            axios.get('/mapMarkers?type_id=' + this.form.type_id + '&category_id=' + this.form.category_id)
                .then(res => {
                    this.markers = res.data.markers;
                    this.selectedMarkers = this.markers;
                    //console.log(this.markers);
                    this.refreshMarker();
                })
                .catch(err => {
                    console.log(err);
                });
        },
        refreshMarker(){
            this.mapCanvas.removeLayer(this.clusterGroup);
            this.clusterGroup = L.markerClusterGroup(); // create the new clustergroup

            this.selectedMarkers.forEach((marker) => {
                this.clusterGroup.addLayer(
                    this.generateMarker(
                        marker.latitude,
                        marker.longitude,
                        marker,
                        marker.title,
                        marker.teaser_text,
                        'll-marker',
                        this.getCss('type', marker.type_id)['css_icon'],
                        this.getCss('category', marker.category_id)['color'],
                        this.getCss('category', marker.category_id)['shape'],
                        'fa'
                    )
                ); // add marker to the clustergroup
            });
            this.mapCanvas.addLayer(this.clusterGroup); // add clustergroup to the map
            this.currentMarker = this.markers[0];
        },
        async markerSearch() {
            $("#loading-events").show();
            try {
                this.events = (await axios.post('/eventSubscriptions/getEvents', {
                    search: this.search,
                    page: 1,
                    plugin: 'evewa'
                })).data.events.data;
            } catch (error) {
                console.log(error);
            }
            this.refreshMap();
        },
        getBorder() {
            axios.get(this.map.border_url)
                 .then(res => {
                     this.processNominatimReply(res.data);
                })
                .catch(err => {
                    console.log(err);
                });
        },
        processNominatimReply(data) {
            data.features.forEach(function(feature) {
                this.bordersGroup.addData(feature);
            }.bind(this));

            var bbox = data.features[0].bbox;
            var topLeft = L.latLng(bbox[1], bbox[0]-2);
            var bottomRight = L.latLng(bbox[3], bbox[2]);
            var countryBounds = L.latLngBounds(topLeft, bottomRight);


            this.mapCanvas.flyToBounds(countryBounds);
        },
        refreshMap() {
            // parse property from Observer to JSON
            const eventsData = JSON.parse(JSON.stringify(this.events));
            //console.log(eventsData);

            this.clusterGroup = L.markerClusterGroup(); // create the new clustergroup
            // add all event-locations
            Object.entries(eventsData).forEach(event => {
                const data = event[1];
                let address = data.termine.key_0.VO_ADRESSE;
                //console.log(address);
                if(address.includes("online")||address.includes("Online")){
                    address = 'Rheinland-Pfalz';
                }
                const url = 'https://nominatim.openstreetmap.org/search?q=' + encodeURI(address) + '&format=jsonv2';
                //console.log(url);
                axios.get(url)
                    .then(res => {
                        this.clusterGroup.addLayer(
                            this.generateMarker(
                                res.data[0].lat,
                                res.data[0].lon,
                                data,
                                data.ARTIKEL,
                                data.KATEGORIE,
                                'll-marker',
                                'fa-circle',
                                '#2471A3',
                                'circle',
                                'fa'
                            )
                        ); // add marker to the clustergroup
                    });
            });
            this.mapCanvas.addLayer(this.clusterGroup); // add clustergroup to the map
        },
        getCss(type, id) {
            switch(type) {
                case 'type':
                    let type_index = this.mapMarkerTypes.findIndex(type => type.id === id);
                    return {
                        'color' : this.mapMarkerTypes[type_index].color,
                        'css_icon' : this.mapMarkerTypes[type_index].css_icon,
                    }
                    break;
                case 'category':
                    let category_index = this.mapMarkerCategories.findIndex(category => category.id === id);
                    return {
                        'color' : this.mapMarkerCategories[category_index].color,
                        'shape' : this.mapMarkerCategories[category_index].shape
                    }
                    break;
                default: return '#000';
                // code block
            }
        },
        generateMarker(lat, lon, entry, title, description, sidebar_target, icon, markerColor, shape, prefix) {
            var svgMarker = L.ExtraMarkers.icon({
                icon: icon,
                markerColor: markerColor,
                shape: 'circle',
                prefix: 'fa',
                svg: true
            });
            return L.marker([lat, lon], {
                    'icon': svgMarker,
                    'title': title // accessibility
                })
                .bindPopup('<b>'+ title + '</b></br>' + description +'<br/>')
                .addTo(this.mapCanvas).on('click', function(e) {
                    this.currentMarker = entry;
                    this.sidebar.open(sidebar_target);
                }.bind(this, sidebar_target));
        },
        dateforHumans(begin, end = null) {
            if (end === begin || end === null){
                return moment(begin).locale('de').format('LL');
            } else {
                return moment(begin).locale('de').format('LL') + " - " + moment(end).locale('de').format('LL');
            }
        },
        setCurrentMarker(marker) {
            this.currentMarker = marker;
            this.sidebar.open('ll-marker');
        },
        syncSelect2() {
            $("#mapMarkerType").select2({
                dropdownParent: $("#mapMarkerType").parent(),
                allowClear: false,
            }).on('select2:select', function (e) {
                this.form.type_id = e.params.data.element.value;
            }.bind(this))
                .val(this.form.type_id)
                .trigger('change');
            $("#mapMarkerCategory").select2({
                dropdownParent: $("#mapMarkerCategory").parent(),
                allowClear: false,
            }).on('select2:select', function (e) {
                this.form.category_id = e.params.data.element.value;
            }.bind(this))
                .val(this.form.category_id)
                .trigger('change');
        },
        confirmItemDelete(marker) {
            this.currentMarker = marker;
            $('#deleteMarkerModal').modal('show');
        },
        deleteMarker() {
            axios.delete("/mapMarkers/" + this.currentMarker.id)
                .then(() => {
                    let index = this.markers.findIndex(
                        i => i.id === this.currentMarker.id
                    );
                    this.markers.splice(index, 1);
                    this.loadMarkers();
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        edit(marker) {
            this.marker = marker;
            this.method = 'patch';
            $('#modal-marker-form').modal('show');
        },
        initMap() {
            this.mapCanvas = L.map('map').setView([this.initialLatitude, this.initialLongitude], this.zoom);

            // default icon-url throws an error (apparently a common problem)
            // so we need to rebind the file-locations
            delete Icon.Default.prototype._getIconUrl;
            Icon.Default.mergeOptions({
                iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
                iconUrl: require('leaflet/dist/images/marker-icon.png'),
                shadowUrl: require('leaflet/dist/images/marker-shadow.png'),
            });

            // set OpenStreetMaps as tile-distributor
            L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(this.mapCanvas);


            this.sidebar = L.control.sidebar('sidebar').addTo(this.mapCanvas);

            this.bordersGroup = L.geoJSON().addTo(this.mapCanvas);

            /*var overlays = {
                'Landesgrenze anzeigen': this.bordersGroup
            };

            L.control.layers(null, overlays, {
                collapsed: false
            }).addTo(this.map);*/

            this.getBorder();
        }
    },
    mounted() {
        this.$eventHub.$emit('showSearchbar');
        this.$eventHub.$on('filter', (filter) => {
            //console.log(this.markers.filter(m => m.title.toLowerCase().indexOf(filter) > -1));
            this.selectedMarkers = this.markers.filter(m => m.title.toLowerCase().indexOf(filter.toLowerCase()) > -1);
            this.refreshMarker();
        });

        this.$eventHub.$on('edit_marker', (marker) => {
            this.edit(marker);
        });

        this.$eventHub.$on('marker-added', (marker) => {
            this.markers.push(marker);
            this.loadMarkers();
        });

        this.$eventHub.$on('marker-updated', (marker) => {
            let index = this.markers.findIndex(
                i => i.id === marker.id
            );

            this.markers.splice(index, 1);
            this.markers.push(marker);
            //this.markers[index] = marker; //doesn't trigger change
            this.loadMarkers();
        });


        if (this.map.initialLatitude){
            this.initialLatitude = this.map.initialLatitude;
        }
        if (this.map.initialLongitude){
            this.initialLongitude = this.map.initialLongitude;
        }
        if (this.map.zoom){
            this.zoom = this.map.zoom;
        }
        if (this.map.type_id){
            this.form.type_id = this.map.type_id;
        }
        if (this.map.category_id){
            this.form.category_id = this.map.category_id;
        }

        this.loader();
    }
}

</script>
<style scoped>
@import "~leaflet/dist/leaflet.css";
@import "~sidebar-v2/css/leaflet-sidebar.css";
@import "~leaflet.markercluster/dist/MarkerCluster.css";
@import "~leaflet.markercluster/dist/MarkerCluster.Default.css";
@import "~leaflet-extra-markers/dist/css/leaflet.extra-markers.min.css";
#map, #outermap {
    height: 100%;
}
.sidebar {
    z-index: 1000 !important;
    height: 83% !important;
    margin-top: 67px;
    margin-left: 17px;
}
</style>
