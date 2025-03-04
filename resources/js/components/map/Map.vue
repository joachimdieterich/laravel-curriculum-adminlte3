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
                        <a
                            role="tab"
                            class="pointer"
                            @click="createMarker()"
                        >
                            <i class="fa fa-plus"></i>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- tab panes -->
            <div class="sidebar-content">
                <div
                    id="ll-home"
                    class="sidebar-pane active"
                >
                    <h1 class="sidebar-header pr-2 mb-3">
                        {{ map.title }}
                        <a v-if="map.owner_id == $userId || checkPermission('is_admin')"
                            v-permission="'map_edit'"
                            class="pull-right link-muted text-light pointer"
                            @click="editMap(map)"
                        >
                            <i class="fas fa-pencil-alt p-2"></i>
                        </a>
                    </h1>
                    <span class="pb-2">
                        <h5>{{ map.subtitle }}</h5>
                        <span class="right badge badge-primary">
                            {{ map.type.title }}
                        </span>
                    </span>

                    <p v-if="map.description != ''"
                        class="pt-2"
                        v-dompurify-html="map.description"
                    ></p>

                    <h5 class="pt-2">{{ trans('global.entries') }}</h5>
                    <ul class="todo-list">
                        <li v-for="marker in this.markers">
                            <i class="fa fa-location-dot pr-2"></i>
                            <a
                                class="text-decoration-none"
                                @click="setCurrentMarker(marker)"
                            >
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
                        :selected="form.type_id"
                        @selectedValue="(id) => {
                            this.form.type_id = id;
                        }"
                    />
                    <Select2
                        :id="'mapMarkerCategory' + component_id"
                        :name="'mapMarkerCategory' + component_id"
                        url="/mapMarkerCategories"
                        model="mapMarkerCategory"
                        :selected="form.category_id"
                        @selectedValue="(id) => {
                            this.form.category_id = id;
                        }"
                    />
                    <button
                        class="btn btn-primary pull-right"
                        @click="loader()"
                    >
                        <i class="fa fa-check"></i>
                    </button>
                </div>

                <div v-if="typeof this.currentMarker.ARTIKEL == 'undefined'"
                    id="ll-marker"
                    class="sidebar-pane"
                >
                    <MarkerView :marker="currentMarker"/>
                </div>
                <div v-else
                    id="ll-marker"
                    class="sidebar-pane"
                >
                    <h1 class="sidebar-header  mb-3">
                        {{ currentMarker.ARTIKEL }}
                    </h1>

                    <div v-if="this.currentMarker.BEZ_1_2.length > 2"
                        class="py-0 pt-2"
                    >
                        <strong>Untertitel</strong>
                    </div>

                    <div v-if="this.currentMarker.BEZ_1_2.length > 2"
                        class="py-0 pre-formatted"
                        v-dompurify-html="currentMarker.BEZ_1_2"
                    ></div>

                    <div class="py-0 pt-2">
                        <strong>Beschreibung</strong>
                    </div>

                    <div
                        class="py-0 pre-formatted text-justify"
                        v-dompurify-html="currentMarker.BEMERKUNG"
                    ></div>

                    <div class="py-0 pt-2"><strong>Termine</strong></div>

                    <div class="py-0 pre-formatted">
                        <div v-for="termin in currentMarker.termine">
                            {{ dateforHumans(termin.DATUM) }}, {{ termin.BEGINN }} - {{ termin.ENDE }}
                            <br/>
                            {{ termin.VO_ORT }}
                        </div>
                    </div>

                    <div class="py-0 pt-2"><strong>VA-Nummer</strong></div>

                    <div class="py-0 pre-formatted" v-dompurify-html="currentMarker.ARTIKEL_NR"></div>

                    <div class="py-0 pt-2">
                        <a
                            :href="currentMarker.LINK_DETAIL"
                            class="btn btn-default"
                            target="_blank"
                        >
                            <i class="fa fa-info"></i> Details/Anmeldung
                        </a>

                        <a
                            :href="currentMarker.LINK_DETAIL + '&print=1'"
                            class="btn btn-default"
                            target="_blank"
                            @click="window.open(this.href, 'Drucken', 'width=800, scrollbars=1')"
                        >
                            <i class="fa fa-print"></i> Drucken
                        </a>
                    </div>
                </div>

                <div class="sidebar-pane" id="ll-search">
                    <h1 class="sidebar-header mb-3">
                        {{ currentMarker.title }}
                    </h1>

                    <div
                        class="form-group"
                        :class="form.errors.search ? 'has-error' : ''"
                    >
                        <label for="ll-search">{{ trans('global.search') }}</label>
                        <input
                            id="ll-search"
                            type="text"
                            name="ll-search"
                            class="form-control"
                            v-model="search"
                            placeholder="Suchbegriff..."
                            @keyup.enter="markerSearch"
                        />
                        <p class="help-block" v-if="form.errors.search" v-text="form.errors.search[0]"></p>
                    </div>
                </div>
            </div>
        </div>

        <div id="map" class="sidebar-map"></div>

        <Teleport to="body">
            <MapModal/>
            <MediumModal/>
            <MediumPreviewModal/>
            <MarkerModal :map="map"/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.marker.delete')"
                :description="trans('global.role.marker')"
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
        MediumModal,
    },
    props: {
        map: {
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
            mapCanvas: [],
            events: {},
            sidebar: {},
            search: 'digiWerkzeug',
            searchCircle: null,
            searchDistance: 20000,
            foundMarkers: [],
            bordersGroup: {},
            namesGroup: {},
            currentPositionMarker: null,
            markers: {},
            leafletMarkers:[],
            currentMarker:{},
            clusterGroup: {},
            form: new Form({
                type_id: '',
                category_id: '',
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
        createMarker(method = 'post') {
            this.globalStore?.showModal('map-marker-modal', {});
        },
        loader() {
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
                if(address.includes("online")||address.includes("Online")) {
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
        generateMarker(lat, lon, entry, title, description, sidebar_target, icon, markerColor, shape, prefix) {
            var svgMarker = L.ExtraMarkers.icon({
                icon: icon,
                markerColor: markerColor,
                shape: 'circle',
                prefix: 'fa',
                svg: true
            });

            let leafletMarker = L.marker([lat, lon], {
                'id': entry.id,
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
        setCurrentMarker(marker) {
            this.currentMarker = marker;
            this.sidebar.open('ll-marker');
        },
        syncSelect2() {
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
        confirmItemDelete(marker) {
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
        editMap(currentMap) {
            this.globalStore?.showModal('map-modal', currentMap);
        },
        edit(marker) {
            this.globalStore?.showModal('map-marker-modal', marker);
        },

        processClick(lat,lon){
            console.log("You clicked the map at LAT: "+ lat+" and LONG: "+lon );

            //Clear existing marker, circle, and selected points if selecting new points
            if (this.searchCircle != null) {
                this.mapCanvas.removeLayer(this.searchCircle);
            };
            if (this.currentPositionMarker != null) {
                this.mapCanvas.removeLayer(this.currentPositionMarker);
            };
            /*if (geojsonLayer != undefined) {
                this.mapCanvas.removeLayer(geojsonLayer);
            };*/

            //Add a marker to show where you clicked.
            this.currentPositionMarker = L.marker([lat,lon]).addTo(this.mapCanvas);
            this.selectPoints(lat,lon);
        },
        selectPoints(lat,lon){
            this.foundMarkers.length = 0;  //Reset the array if selecting new points

            this.clusterGroup.eachLayer(function (layer) {
                // Lat, long of current point as it loops through.
                let layer_lat_long = layer.getLatLng();

                // See if meters is within radius, add the to array
                console.log(this.searchDistance)
                if (layer_lat_long.distanceTo([lat,lon]) <= this.searchDistance) {
                    console.log(layer.options);
                    this.foundMarkers.push(layer.feature);
                }
            }.bind(this));

            // draw circle to see the selection area
            this.searchCircle = L.circle([lat,lon], this.searchDistance , {   /// Number is in Meters
                color: 'orange',
                fillOpacity: 0,
                opacity: 1
            }).addTo(this.mapCanvas);

            /*//Symbolize the Selected Points
            geojsonLayer = L.geoJson(this.foundMarkers, {

                pointToLayer: function(feature, latlng) {
                    return L.circleMarker(latlng, {
                        radius: 4, //expressed in pixels circle size
                        color: "green",
                        stroke: true,
                        weight: 7,		//outline width  increased width to look like a filled circle.
                        fillOpcaity: 1
                    });
                }
            });
            //Add selected points back into map as green circles.
            this.mapCanvas.addLayer(geojsonLayer); */

            //Take array of features and make a GeoJSON feature collection
            var GeoJS = { type: "FeatureCollection",  features: this.foundMarkers   };

            //Show number of selected features.
            console.log(GeoJS.features.length +" Selected features");

            // show selected GEOJSON data in console
            console.log(JSON.stringify(GeoJS));

            //////////////////////////////////////////

            /// Putting the selected team name in the table

            //Clean up prior records
           /* $("#myTable tr").remove();

            var table = document.getElementById("myTable");
            //Add the header row.
            var row = table.insertRow(-1);
            var headerCell = document.createElement("th");
            headerCell.innerHTML = "Team";  //Fieldname
            row.appendChild(headerCell);*/

            //Add the data rows.
            //console.log(this.foundMarkers);
           /* for (var i = 0; i < this.foundMarkers.length; i++) {
                //console.log(this.foundMarkers[i].properties.Team);
                row = table.insertRow(-1);

                var cell = row.insertCell(-1);
                cell.innerHTML = this.foundMarkers[i].properties.Team;
            }
            //Get the Team name in the cell.
            $('#myTable tr').click(function(x) {
                theTeam = (this.getElementsByTagName("td").item(0)).innerHTML;
                console.log(theTeam);
                map._layers[theTeam].fire('click');
                var coords = map._layers[theTeam]._latlng;
                console.log(coords);
                map.setView(coords, 12);
            });*/


        }

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

        if (this.map.initialLatitude) {
            this.initialLatitude = this.map.initialLatitude;
        }
        if (this.map.initialLongitude) {
            this.initialLongitude = this.map.initialLongitude;
        }
        if (this.map.zoom) {
            this.zoom = this.map.zoom;
        }
        if (this.map.type_id) {
            this.form.type_id = this.map.type_id;
        }
        if (this.map.category_id) {
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

       /* //  click to set position > wip on distance search
        this.mapCanvas.on('click', function(e){
            this.processClick(e.latlng.lat, e.latlng.lng);
        }.bind(this));
*/
    },
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
