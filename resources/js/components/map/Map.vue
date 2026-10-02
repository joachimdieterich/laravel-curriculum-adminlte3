<template>
    <div
        id="outermap"
        class="position-relative h-100"
    >
        <div
            id="sidebar"
            class="position-absolute d-flex flex-row"
        >
            <!-- navigation tabs -->
            <ul
                class="nav nav-pills flex-column mb-auto rounded-start-3 bg-white"
                role="tablist"
            >
                <li
                    class="nav-item"
                    role="presentation"
                >
                    <button
                        type="button"
                        id="home-nav-tab"
                        class="nav-link btn btn-light w-100 shadow-none active"
                        role="tab"
                        data-bs-toggle="pill"
                        data-bs-target="#ll-home"
                        aria-controls="ll-home"
                        aria-selected="true"
                    >
                        <i class="fa fa-bars"></i>
                    </button>
                </li>
                <li
                    class="nav-item"
                    role="presentation"
                >
                    <button
                        type="button"
                        id="layer-nav-tab"
                        class="nav-link btn btn-light w-100 shadow-none"
                        role="tab"
                        data-bs-toggle="pill"
                        data-bs-target="#ll-layer"
                        aria-controls="ll-layer"
                        aria-selected="false"
                    >
                        <i class="fa fa-layer-group"></i>
                    </button>
                </li>
                <li
                    class="nav-item"
                    role="presentation"
                >
                    <button
                        type="button"
                        id="marker-nav-tab"
                        class="nav-link btn btn-light w-100 shadow-none"
                        role="tab"
                        data-bs-toggle="pill"
                        data-bs-target="#ll-marker"
                        aria-controls="ll-marker"
                        aria-selected="false"
                    >
                        <i class="fa fa-location-dot"></i>
                    </button>
                </li>
                <!-- <li><a href="#ll-search" role="tab"><i class="fa fa-search"></i></a></li> -->
                <hr v-if="editable"/>
                <li v-if="editable">
                    <button
                        type="button"
                        class="btn btn-light w-100 shadow-none"
                        @click="createMarker()"
                    >
                        <i class="fa fa-plus"></i>
                    </button>
                </li>
            </ul>

            <!-- tab panes -->
            <div class="tab-content rounded-end-3 bg-white h-100">
                <div
                    id="ll-home"
                    class="tab-pane fade show active"
                    role="tabpanel"
                    aria-labelledby="home-nav-tab"
                    tabindex="0"
                >
                    <div class="tab-header d-flex align-items-center ps-3 bg-primary">
                        <span class="t-20 line-clamp">{{ map.title }}</span>
                        <span v-if="map.owner_id == $userId || checkPermission('is_admin')"
                            v-permission="'map_edit'"
                            class="d-print-none d-flex gap-2 ms-auto me-1"
                        >
                            <button
                                type="button"
                                class="btn btn-icon-alt"
                                data-bs-toggle="tooltip"
                                :data-bs-title="trans('global.map.edit')"
                                @click="editMap(map)"
                            >
                                <i class="fas fa-pencil-alt p-2"></i>
                            </button>
                            <button
                                type="button"
                                class="btn btn-icon-alt"
                                data-bs-toggle="tooltip"
                                :data-bs-title="trans('global.map.share')"
                                @click="share()"
                            >
                                <i class="fa fa-share-alt p-2"></i>
                            </button>
                        </span>
                    </div>

                    <div class="d-flex flex-column gap-2 px-3 py-2">
                        <div>
                            <div class="h5">{{ map.subtitle }}</div>
                            <span class="badge rounded-pill text-bg-primary">{{ map.type.title }}</span>
                        </div>
    
                        <div v-if="map.description"
                            class="p-margin-0"
                            v-html="map.description"
                        ></div>
    
                        <h5 class="pt-2">{{ trans('global.entries') }}</h5>
                        <ul class="todo-list">
                            <li v-for="marker in markers"
                                class="d-flex align-items-center show-hidden-animate"
                                @mouseover="showMarkerPopup(marker)"
                                @mouseleave="hideMarkerPopup(marker)"
                            >
                                <i class="fa fa-location-dot pe-2"></i>
                                <button
                                    type="button"
                                    class="btn btn-link p-0 text-decoration-none"
                                    @click="setCurrentMarker(marker)"
                                >
                                    {{ marker.title }}
                                </button>
                                <span v-if="editable"
                                    class="d-print-none d-flex align-items-center gap-2 ms-auto"
                                    style="height: 0px;"
                                >
                                    <button
                                        type="button"
                                        class="btn btn-icon text-secondary hide-lg"
                                        @click="edit(marker)"
                                    >
                                        <i class="fa fa-pencil-alt"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-icon text-danger hide-lg"
                                        @click="confirmItemDelete(marker)"
                                    >
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div
                    id="ll-layer"
                    class="tab-pane fade"
                    role="tabpanel"
                    aria-labelledby="layer-nav-tab"
                    tabindex="0"
                >
                    <div class="tab-header d-flex align-items-center ps-3 bg-primary">
                        <span class="t-20">Ebenen</span>
                    </div>

                    <div class="d-flex flex-column gap-3 px-3 py-2">
                        <Select2
                            :id="'mapMarkerType' + component_id"
                            url="/mapMarkerTypes"
                            model="mapMarkerType"
                            :selected="form.type_id"
                            @selectedValue="id => form.type_id = id[0]"
                        />
                        <Select2
                            :id="'mapMarkerCategory' + component_id"
                            url="/mapMarkerCategories"
                            model="mapMarkerCategory"
                            :selected="form.category_id"
                            @selectedValue="id => form.category_id = id[0]"
                        />
                        <button
                            type="button"
                            class="btn btn-primary"
                            @click="loader()"
                        >
                            <i class="fa fa-check"></i>
                        </button>
                    </div>
                </div>

                <div
                    id="ll-marker"
                    class="tab-pane fade"
                    role="tabpanel"
                    aria-labelledby="marker-nav-tab"
                    tabindex="0"
                >
                    <div v-if="currentMarker">
                        <div v-if="currentMarker.ARTIKEL == undefined">
                            <MarkerView
                                :marker="currentMarker"
                                :editable="editable"
                            />
                        </div>
                        <div v-else>
                            <div class="tab-header d-flex align-items-center ps-3 bg-primary">
                                <span class="t-20">{{ currentMarker.ARTIKEL }}</span>
                            </div>
        
                            <div v-if="currentMarker.BEZ_1_2.length > 2"
                                class="py-0 pt-2"
                            >
                                <strong>Untertitel</strong>
                            </div>
        
                            <div v-if="currentMarker.BEZ_1_2.length > 2"
                                class="py-0 pre-formatted"
                                v-html="currentMarker.BEZ_1_2"
                            ></div>
        
                            <div class="py-0 pt-2">
                                <strong>{{ trans('global.description') }}</strong>
                            </div>
        
                            <div
                                class="py-0 pre-formatted text-justify"
                                v-html="currentMarker.BEMERKUNG"
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
        
                            <div class="py-0 pre-formatted" v-html="currentMarker.ARTIKEL_NR"></div>
        
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
                    </div>
                </div>

                <!-- <div class="tab-pane fade" id="ll-search">
                    <div class="tab-header d-flex align-items-center ps-3 bg-primary">{{ currentMarker?.title }}</div>

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
                </div> -->
            </div>
        </div>

        <div id="map" class="user-select-none h-100"></div>

        <Teleport to="body">
            <MapModal/>
            <MediumModal/>
            <MarkerModal
                :map="map"
                :clickedCoordinates="clickedCoordinates"
                @setCoordinates="showClearMap()"
            />
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.marker.delete')"
                :description="trans('global.marker.delete_helper')"
                @close="showConfirm = false"
                @confirm="destroy()"
            />
        </Teleport>
    </div>
</template>
<script>
import {Icon} from 'leaflet';
import "leaflet/dist/leaflet.js";
import "leaflet.markercluster/dist/leaflet.markercluster.js";
import "leaflet-extra-markers/dist/js/leaflet.extra-markers.js"
import Form from "form-backend-validation";
import MarkerView from "./MarkerView.vue";
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import MediumModal from "../media/MediumModal.vue";
import MarkerModal from "./MarkerModal.vue";
import MapModal from "./MapModal.vue";
import Select2 from "../forms/Select2.vue";
import markerIconUrl from "leaflet/dist/images/marker-icon.png";
import markerIconRetinaUrl from "leaflet/dist/images/marker-icon-2x.png";
import markerShadowUrl from "leaflet/dist/images/marker-shadow.png";

export default {
    components: {
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
        editable: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            mapCanvas: {}, // actual rendered map
            events: {},
            sidebar: {},
            search: 'digiWerkzeug',
            // searchCircle: null,
            // searchDistance: 20000,
            // foundMarkers: [],
            bordersGroup: {},
            // currentPositionMarker: null,
            markers: [], // contains information for the markers (stored in DB)
            leafletMarkers: [], // actual positions/layers of the markers on the map
            currentMarker: null,
            clusterGroup: {}, // layer of 'markercluster'-plugin, which is wrapped around all markers
            form: new Form({
                type_id: null,
                category_id: null,
            }),
            showConfirm: false,
            clickedCoordinates: null,
        }
    },
    methods: {
        createMarker() {
            this.globalStore.showModal('map-marker-modal', {
                map_id: this.map.id,
                type_id: this.form.type_id,
                category_id: this.form.category_id,
            });
        },
        loader() {
            axios.get('/mapMarkers?type_id=' + this.form.type_id + '&category_id=' + this.form.category_id)
                .then(res => {
                    this.markers = res.data;
                    this.currentMarker = this.markers[0];
                    this.generateClusterGroup();
                })
                .catch(e => {
                    console.log(e);
                    this.toast.error(this.errorMessage(e));
                });
        },
        generateClusterGroup() {
            this.clusterGroup = L.markerClusterGroup(); // create the new clustergroup

            this.markers.forEach((marker) => {
                this.clusterGroup.addLayer( // add marker to the clustergroup
                    this.generateMarker(
                        marker.latitude,
                        marker.longitude,
                        marker,
                        marker.title,
                        marker.teaser_text ?? '',
                        'll-marker',
                        marker.type.css_icon,
                        marker.type.color,
                        marker.category.shape,
                        'fa'
                    )
                );
            });

            // show list of all clustered markers on hover
            this.clusterGroup.on('clustermouseover', (e) => {
                let titles = e.layer.getAllChildMarkers().map(m => m.options.title).sort().join('<hr class="my-2"/>');
                e.layer.bindPopup(titles).openPopup();
            });
            this.clusterGroup.on('clustermouseout', (e) => {
                e.layer.closePopup();
            });

            this.mapCanvas.addLayer(this.clusterGroup); // add clustergroup to the map
        },
        generateMarker(lat, lon, entry, title, teaser_text, sidebar_target, icon, markerColor, shape = 'circle', prefix = 'fa') {
            let svgMarker = L.ExtraMarkers.icon({
                icon,
                markerColor,
                shape,
                prefix,
                svg: true
            });

            let leafletMarker = L.marker([lat, lon], {
                'id': entry.id,
                'icon': svgMarker,
                'title': title // accessibility
            })
                .bindPopup('<b>' + title + '</b><br/>' + (teaser_text ?? ''))
                .on('click', () => {
                    this.currentMarker = entry;
                    this.sidebar.open(sidebar_target);
                });
            
            this.leafletMarkers.push(leafletMarker);

            return leafletMarker;
        },
        // async markerSearch() {
        //     $("#loading-events").show();
        //     try {
        //         this.events = (await axios.post('/eventSubscriptions/getEvents', {
        //             search: this.search,
        //             page: 1,
        //             plugin: 'evewa'
        //         })).data.events.data;
        //     } catch (error) {
        //         console.log(error);
        //     }
        //     this.refreshMap();
        // },
        getBorder() {
            axios.get(this.map.border_url)
                .then(res => {
                    this.processNominatimReply(res.data);
                })
                .catch(e => {
                    console.log(e);
                    this.toast.error(this.errorMessage(e));
                });
        },
        showMarkerPopup(marker) {
            let leafletMarker = this.leafletMarkers.find(m => m.options.id === marker.id);
            // check if marker is clustered
            if (leafletMarker._map == undefined) {
                this.clusterGroup.getVisibleParent(leafletMarker)
                    .bindPopup('<b>' + marker.title + '</b><br/>' + (marker.teaser_text ?? ''))
                    .openPopup();
            } else {
                leafletMarker.openPopup();
            }
        },
        hideMarkerPopup(marker) {
            let leafletMarker = this.leafletMarkers.find(m => m.options.id === marker.id);
            if (leafletMarker._map == undefined) {
                this.clusterGroup.getVisibleParent(leafletMarker).closePopup();
            } else {
                leafletMarker.closePopup();
            }
        },
        showClearMap() {
            let markerElements = this.$el.querySelectorAll('.leaflet-marker-pane, .leaflet-shadow-pane');

            // hide markers from map
            markerElements.forEach((element) => element.classList.add('d-none'));

            const message = this.trans('global.map.click_for_coordinates');
            // add toast-notification to inform user
            const toast = this.toast.info(message + '\nLatitude:\nLongitude:', {
                timeout: false,
                closeButton: false,
                closeOnClick: false,
            });
            // update coordinates in toast-notification
            this.mapCanvas.on('mousemove', (e) => {
                this.toast.update(toast, {
                    content: message + '\nLatitude: ' + e.latlng.lat.toFixed(5) + '\nLongitude: ' + e.latlng.lng.toFixed(5),
                });
            })

            // click to set position
            this.mapCanvas.on('click', (e) => {
                this.clickedCoordinates = e.latlng;
                // show markers again
                markerElements.forEach((element) => element.classList.remove('d-none'));
                // only trigger event once
                this.mapCanvas.off('click');
                this.mapCanvas.off('mouseover')
                this.toast.dismiss(toast);
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

            this.clusterGroup = L.markerClusterGroup(); // create the new clustergroup
            // add all event-locations
            Object.entries(eventsData).forEach(event => {
                const data = event[1];
                let address = data.termine.key_0.VO_ADRESSE;
 
                if (address.includes("online") || address.includes("Online")) {
                    address = 'Rheinland-Pfalz';
                }
                const url = 'https://nominatim.openstreetmap.org/search?q=' + encodeURI(address) + '&format=jsonv2';

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
            this.leafletMarkers.find(m => m.options.id === marker.id).openPopup();
        },
        confirmItemDelete(marker) {
            this.currentMarker = marker;
            this.showConfirm = true;
        },
        destroy() {
            this.showConfirm = false;

            axios.delete("/mapMarkers/" + this.currentMarker.id)
                .then(() => {
                    // the index of both arrays should be the same, but its better to check both just to be safe
                    let index = this.markers.findIndex(i => i.id === this.currentMarker.id);
                    let leafletIndex = this.leafletMarkers.findIndex(i => i.options.id === this.currentMarker.id)
                    // first remove the actual marker from the map
                    this.clusterGroup.removeLayer(this.leafletMarkers[leafletIndex]);
                    // then remove its entry in out marker-arrays
                    this.markers.splice(index, 1);
                    this.leafletMarkers.splice(leafletIndex, 1);
                })
                .catch(e => {
                    console.log(e);
                    this.toast.error(this.errorMessage(e));
                });
        },
        editMap(currentMap) {
            this.globalStore.showModal('map-modal', currentMap);
        },
        edit(marker) {
            this.globalStore.showModal('map-marker-modal', marker);
        },
        share() {
            this.globalStore.showModal('subscribe-modal', {
                modelId: this.map.id,
                modelUrl: 'map',
                shareWithUsers: true,
                shareWithGroups: true,
                shareWithOrganizations: true,
                shareWithToken: true,
                canEditCheckbox: true,
            });
        },
        processClick(lat,lon) {
            console.log("You clicked the map at LAT: " + lat + " and LONG: " + lon );

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
        selectPoints(lat,lon) {
            this.foundMarkers.length = 0; //Reset the array if selecting new points

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
            this.searchCircle = L.circle([lat,lon], this.searchDistance , { // Number is in Meters
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
        this.$eventHub.on('marker-added', (marker) => {
            this.markers.push(marker);
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
            );
        });

        this.$eventHub.on('marker-updated', (updatedMarker) => {
            let marker = this.markers.find(m => m.id === updatedMarker.id);
            Object.assign(marker, updatedMarker);
        });

        this.$eventHub.on('map-updated', (map) => {
            window.location.reload();
        });

        // remove the placeholder-element for the title
        document.querySelector('section.p-3')?.remove();

        this.enableTooltips();

        this.form.type_id = this.map.type_id;
        this.form.category_id = this.map.category_id;

        this.mapCanvas = L.map('map', { zoomControl: false })
            .setView([this.map.latitude, this.map.longitude], this.map.zoom);

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

        this.bordersGroup = L.geoJSON().addTo(this.mapCanvas);

        this.getBorder();

        this.markers = this.map.markers;
        this.currentMarker = this.markers[0];

        this.generateClusterGroup();

        //  click to set position > wip on distance search
        // this.mapCanvas.on('click', function(e) {
        //     this.processClick(e.latlng.lat, e.latlng.lng);
        // }.bind(this));
    },
}
</script>
<style>
@import "leaflet/dist/leaflet.css";
@import "leaflet.markercluster/dist/MarkerCluster.css";
@import "leaflet.markercluster/dist/MarkerCluster.Default.css";
@import "leaflet-extra-markers/dist/css/leaflet.extra-markers.min.css";

#sidebar {
    top: 1rem;
    left: 1rem;
    z-index: 1000;

    & > ul {
        & button { border-radius: 0; }
        & li:first-child button {
            border-top-left-radius: 0.5rem;
        }
        & li:last-child button {
            border-bottom-left-radius: 0.5rem;
        }
    }

}
.tab-content {
    width: 400px;

    & .tab-header {
        height: 40px;
        border-top-right-radius: 0.5rem;
    }
}

@media (max-width: 768px) {
    .tab-content {
        width: calc(100vw - 2rem - 50px);
    }
}
</style>