<template>
    <div id="outermap">
        <div
            id="sidebar"
            class="sidebar"
        >
            <!-- navigation tabs -->
            <div class="sidebar-tabs">
                <ul role="tablist">
                    <li><a href="#ll-home" role="tab"><i class="fa fa-bars"></i></a></li>
                    <li><a href="#ll-layer" role="tab"><i class="fa fa-layer-group"></i></a></li>
                    <li><a href="#ll-marker" role="tab"><i class="fa fa-location-dot"></i></a></li>
                    <li><a href="#ll-search" role="tab"><i class="fa fa-search"></i></a></li>
                    <hr>
                    <li>
                        <a role="tab"
                        @click="createMarker()">
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
                        <a v-if="map.owner_id == this.$userId"
                            v-permission="'map_edit'"
                           class="pull-right link-muted"
                           @click="editMap(this.map)" >
                            <i class="fas fa-pencil-alt text-white"></i>
                        </a>
                    </h1>
                    <span class="pb-2">
                        <h5 >{{ this.map.subtitle }}</h5>
                        <span class="right badge badge-primary">
                            {{ this.map.type.title }}
                        </span>
                    </span>

                    <p  v-if="this.map.description != ''"
                        class="pt-2"
                        v-dompurify-html="this.map.description">
                    </p>

                    <h5 class="pt-2">{{ trans('global.entries') }}</h5>
                    <ul class="todo-list">
                        <li v-for="marker in this.markers">
                            <i class="fa fa-location-dot pr-2"></i>
                            <a @click="setCurrentMarker(marker)"
                               class="text-decoration-none">
                                {{ marker.title }}
                            </a>
                            <div class="tools">
                                <i class="fa fa-pencil-alt text-secondary" @click="edit(marker)"></i>
                                <i class="fa fa-trash text-danger ml-2" @click="confirmItemDelete(marker)"></i>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="sidebar-pane" id="ll-layer">
                    <h1 class="sidebar-header mb-3">
                        Ebenen
                    </h1>

                    <Select2
                        :id="'mapMarkerType' + component_id"
                        :name="'mapMarkerType' + component_id"
                        url="/mapMarkerTypes"
                        model="mapMarkerType"
                        :selected="this.form.type_id"
                        @selectedValue="(id) => {
                            this.form.type_id = id;
                        }"
                    >
                    </Select2>
                    <Select2
                        :id="'mapMarkerCategory' + component_id"
                        :name="'mapMarkerCategory' + component_id"
                        url="/mapMarkerCategories"
                        model="mapMarkerCategory"
                        :selected="this.form.category_id"
                        @selectedValue="(id) => {
                            this.form.category_id = id;
                        }"
                    >
                    </Select2>
                    <button class="btn btn-primary pull-right"
                            @click="loader()">
                        <i class="fa fa-check"></i>
                    </button>
                </div>

                <div v-if="typeof this.currentMarker.ARTIKEL == 'undefined'"
                     class="sidebar-pane" id="ll-marker">
                    <MarkerView :marker="this.currentMarker"/>
                </div>
                <div v-else
                     class="sidebar-pane" id="ll-marker">
                    <h1 class="sidebar-header  mb-3">
                        {{ this.currentMarker.ARTIKEL }}
                    </h1>

                    <div class="py-0 pt-2"
                         v-if="this.currentMarker.BEZ_1_2.length > 2">
                        <strong>Untertitel</strong></div>
                    <div class="py-0 pre-formatted"
                         v-if="this.currentMarker.BEZ_1_2.length > 2"
                         v-dompurify-html="this.currentMarker.BEZ_1_2"></div>

                    <div class="py-0 pt-2">
                        <strong>Beschreibung</strong></div>
                    <div class="py-0 pre-formatted"
                         style="text-align:justify;"
                         v-dompurify-html="this.currentMarker.BEMERKUNG"></div>

                    <div class="py-0 pt-2"><strong>Termine</strong></div>
                    <div class="py-0 pre-formatted">
                        <div v-for="termin in this.currentMarker.termine" >
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
                           target="_blank">
                            <i class="fa fa-info"></i> Details/Anmeldung
                        </a>

                        <a :href="this.currentMarker.LINK_DETAIL+'&print=1'"
                           onclick="return !window.open(this.href, 'Drucken', 'width=800,scrollbars=1')"
                           class="btn btn-default"
                           target="_blank">
                            <i class="fa fa-print"></i> Drucken
                        </a>
                    </div>
                </div>

                <div class="sidebar-pane" id="ll-search">
                    <h1 class="sidebar-header  mb-3">
                        {{ this.currentMarker.title }}
                    </h1>

                    <div class="form-group "
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

        <Teleport to="body">
            <MapModal></MapModal>
            <MediumPreviewModal></MediumPreviewModal>
            <MarkerModal
                :map="this.map"
            >
            </MarkerModal>
            <MediumModal
                subscribable_type="App\\Map"
                :subscribable_id="map.id"
                ></MediumModal>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.marker.delete')"
                :description="trans('global.role.marker')"
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

import axios from 'axios';
import "leaflet/dist/leaflet.js";
import {Icon} from 'leaflet';
import "sidebar-v2/js/leaflet-sidebar.js";
import "leaflet.markercluster/dist/leaflet.markercluster.js";
import Form from "form-backend-validation";
import "leaflet-extra-markers/dist/js/leaflet.extra-markers.js"
import moment from "moment/moment";
import MarkerView from "./MarkerView.vue";
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import MediumModal from "../media/MediumModal.vue";
import MarkerModal from "./MarkerModal.vue";
import {useGlobalStore} from "../../store/global";
import MapModal from "./MapModal.vue";
import Select2 from "../forms/Select2.vue";
import markerIconUrl from "leaflet/dist/images/marker-icon.png";
import markerIconRetinaUrl from "leaflet/dist/images/marker-icon-2x.png";
import markerShadowUrl from "leaflet/dist/images/marker-shadow.png";
import MediumPreviewModal from "../media/MediumPreviewModal.vue";

export default {
    components: {
        MediumPreviewModal,
        Select2,
        MapModal,
        MarkerModal,
        MarkerView,
        ConfirmModal,
        MediumModal
    },
    props: {
        map: {
            default: null
        },
    },
    setup () { //use database store
        const globalStore = useGlobalStore();
        return {
            globalStore
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            mapCanvas: [],
            events: {},
            sidebar: {},
            search: 'digiWerkzeug',
            bordersGroup: {},
            namesGroup: {},
            markers: {},
            leafletMarkers:[],
            currentMarker:{},
            clusterGroup: {},
            form: new Form({
                'type_id': '',
                'category_id': '',
            }),
            initialLatitude: '49.314908280766346',
            initialLongitude: '8.413913138283617',
            zoom: 10,
            marker: null,
            method: {
                type: String,
                default: 'post'
            },
            showConfirm: false,
        }
    },
    methods: {
        createMarker(method = 'post'){
            this.globalStore?.showModal('map-marker-modal', {});
        },
        loader(){
            axios.get('/mapMarkers?type_id=' + this.form.type_id + '&category_id=' + this.form.category_id)
                .then(res => {
                    this.markers = res.data.markers;
                    this.currentMarker = this.markers[0];
                    //console.log(this.markers);
                    this.clusterGroup = L.markerClusterGroup(); // create the new clustergroup

                    this.markers.forEach((marker) => {
                        this.clusterGroup.addLayer(
                            this.generateMarker(
                                marker.latitude,
                                marker.longitude,
                                marker,
                                marker.title,
                                marker.teaser_text,
                                'll-marker',
                                marker.type.css_icon,
                                marker.type.color,
                                marker.category.shape,
                                'fa'
                            )
                        ); // add marker to the clustergroup
                    });
                    this.mapCanvas.addLayer(this.clusterGroup); // add clustergroup to the map
                })
                .catch(err => {
                    console.log(err);
                });
            console.log('Clustergroup');
            console.log(this.clusterGroup);
            console.log('Clustergroup');
        },
        async markerSearch(){
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

        refreshMap(){
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
        generateMarker(lat, lon, entry, title, description, sidebar_target, icon, markerColor, shape, prefix){
            var svgMarker = L.ExtraMarkers.icon({
                icon: icon,
                markerColor: markerColor,
                shape: 'circle',
                prefix: 'fa',
                svg: true
            });

            let leafletMarker = L.marker([lat, lon], {
                'icon': svgMarker,
                'title': title // accessibility
            })
                .bindPopup('<b>'+ title + '</b></br>' + description +'<br/>')
                .addTo(this.mapCanvas).on('click', function(e) {
                this.currentMarker = entry;
                this.sidebar.open(sidebar_target);
            }.bind(this, sidebar_target));

            console.log(leafletMarker);
            this.leafletMarkers.push(leafletMarker);

            return leafletMarker;
        },
        dateforHumans(begin, end = null) {
            if (end === begin || end === null){
                return moment(begin).locale('de').format('LL');
            } else {
                return moment(begin).locale('de').format('LL') + " - " + moment(end).locale('de').format('LL');
            }
        },
        setCurrentMarker(marker){
            this.currentMarker = marker;
            this.sidebar.open('ll-marker');
        },
        syncSelect2(){
            $("#type_id").select2({
                dropdownParent: $("#type_id").parent(),
                allowClear: false
            }).on('select2:select', function (e) {
                this.form.type_id = e.params.data.element.value
            }.bind(this))
                .val(this.form.type_id)
                .trigger('change');
            $("#category_id").select2({
                dropdownParent: $("#category_id").parent(),
                allowClear: false
            }).on('select2:select', function (e) {
                this.form.category_id = e.params.data.element.value
            }.bind(this))
                .val(this.form.category_id)
                .trigger('change');
        },
        confirmItemDelete(marker){
            this.currentMarker = marker;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete("/mapMarkers/" + this.currentMarker.id)
                .then(() => {
                    let index = this.markers.findIndex(
                        i => i.id === this.currentMarker.id
                    );
                    this.markers.splice(index, 1);

                    this.clusterGroup.clearLayers(); // clear layers, then reload
                    this.loader();
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        editMap(currentMap){
            this.globalStore?.showModal('map-modal', currentMap);
        },
        edit(marker) {
            this.globalStore?.showModal('map-marker-modal', marker);
        },
    },
    mounted() {
        this.$eventHub.on('edit_marker', (marker) => {
            this.edit(marker);
        });

        this.$eventHub.on('marker-updated', (marker) => {
            let index = this.markers.findIndex(
                i => i.id === this.currentMarker.id
            );
            this.markers.splice(index, 1);
            this.markers[index] = marker;
        });

        this.$eventHub.on('map-updated', (map) => {
            this.globalStore?.closeModal('map-modal');
            window.location.reload();
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

        this.mapCanvas = L.map('map').setView([this.initialLatitude, this.initialLongitude], this.zoom);

        // default icon-url throws an error (apparently a common problem)
        // so we need to rebind the file-locations
       // delete Icon.Default.prototype._getIconUrl;
       /* Icon.Default.mergeOptions({
            iconRetinaUrl: '/leaflet/dist/images/marker-icon-2x.png',
            iconUrl: '/leaflet/dist/images/marker-icon.png',
            shadowUrl: '/leaflet/dist/images/marker-shadow.png',
        });*/
        L.Icon.Default.prototype.options.iconUrl = markerIconUrl;
        L.Icon.Default.prototype.options.iconRetinaUrl = markerIconRetinaUrl;
        L.Icon.Default.prototype.options.shadowUrl = markerShadowUrl;
        L.Icon.Default.imagePath = ""; // necessary to avoid Leaflet adds some prefix to image path.


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

        this.loader();
    }
}

</script>
<style >
@import "leaflet/dist/leaflet.css";
@import "sidebar-v2/css/leaflet-sidebar.css";
@import "leaflet.markercluster/dist/MarkerCluster.css";
@import "leaflet.markercluster/dist/MarkerCluster.Default.css";
@import "leaflet-extra-markers/dist/css/leaflet.extra-markers.min.css";
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
