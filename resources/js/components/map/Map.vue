<template>
    <div id="outermap">

        <div id="sidebar" class="sidebar collapsed">
            <!-- navigation tabs -->
            <div class="sidebar-tabs">
                <ul role="tablist">
                    <li><a href="#ll-home" role="tab"><i class="fa fa-bars"></i></a></li>
                    <li><a href="#ll-layer" role="tab"><i class="fa fa-layer-group"></i></a></li>
                    <li><a href="#ll-marker" role="tab"><i class="fa fa-location-dot"></i></a></li>
                    <li><a href="#ll-search" role="tab"><i class="fa fa-search"></i></a></li>
                    <hr>
                    <li><a href="#ll-create" role="tab"><i class="fa fa-plus"></i></a></li>
                </ul>
            </div>

            <!-- tab panes -->
            <div class="sidebar-content">
                <div class="sidebar-pane" id="ll-home">
                    <h1 class="sidebar-header mb-3">
                        Bildungsnavigator Rheinland-Pfalz
                        <span class="sidebar-close"><i class="fa fa-caret-left"></i></span>
                    </h1>

                    <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>
                </div>

                <div class="sidebar-pane" id="ll-layer">
                    <h1 class="sidebar-header mb-3">
                        Ebenen
                        <span class="sidebar-close"><i class="fa fa-caret-left"></i></span>
                    </h1>


                    <div v-if="mapMarkerTypes"
                         class="form-group ">
                        <label for="mapMarkerType">Typ</label>
                        <select
                            id="mapMarkerType"
                            v-model="form.type_id"
                            class="form-control select2"
                            style="width:100%;">
                            <option v-for="type in mapMarkerTypes"
                                    :value="type.id">
                                {{ type.title }}
                            </option>
                        </select>
                    </div> <!-- mapMarkerTypes -->
                    <div v-if="mapMarkerCategories"
                         class="form-group ">
                        <label for="mapMarkerCategory">Kategorie</label>
                        <select
                            id="mapMarkerCategory"
                            v-model="form.category_id"
                            class="form-control select2"
                            style="width:100%;">
                            <option v-for="category in mapMarkerCategories"
                                    :value="category.id">
                                {{ category.title }}
                            </option>
                        </select>
                    </div> <!-- mapMarkerCategory -->
                    <button class="btn btn-primary pull-right"
                            @click="loadMarkers()">
                        <i class="fa fa-check"></i>
                    </button>
                </div>

                <div class="sidebar-pane" id="ll-marker">
                    <h1 class="sidebar-header  mb-3">
                        {{ this.currentMarker.title }}
                        <span class="sidebar-close"><i class="fa fa-caret-left"></i></span>
                    </h1>

                   <div v-html="this.currentMarker.description"></div>
                </div>

                <div class="sidebar-pane" id="ll-search">
                    <h1 class="sidebar-header  mb-3">
                        {{ this.currentMarker.title }}
                        <span class="sidebar-close"><i class="fa fa-caret-left"></i></span>
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

                <div v-if="mapMarkerTypes && mapMarkerCategories"
                     class="sidebar-pane" id="ll-create">
                    <MapSidebarCreate
                        :mapMarkerTypes="this.mapMarkerTypes"
                        :mapMarkerCategories="this.mapMarkerCategories"/>

                </div>

                <div v-if="markers">
                    <p v-for="item in markers"
                    style="list-style-type: none;"
                       >
                        <i class="fa fa-location-dot"
                           :style="{ 'color': getCss('type', item.type_id)['color'] + ' !important', 'background': getCss('category', item.category_id)['color'] + ' !important' }"
                        ></i>
                        {{ item.title }}
                    </p>
                </div>
            </div>
        </div>

        <div id="map" class="sidebar-map"></div>
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
import MapSidebarCreate from "./MapSidebarCreate";


export default {
    components: {
        MapSidebarCreate
    },
    data() {
        return {
            component_id: this._uid,
            events: {},
            map:[],
            sidebar: {},
            search: 'digiWerkzeug',
            bordersGroup: {},
            namesGroup: {},
            markers: {},
            currentMarker:{},
            clusterGroup: {},
            mapMarkerTypes: {},
            mapMarkerCategories: {},
            form: new Form({
                'type_id': '',
                'category_id': '',
            }),
        }
    },
    methods: {
        loader() {
        //test
            axios.get('/mapMarkerTypes')
                .then(res => {
                    this.mapMarkerTypes = res.data.mapMarkerTypes;
                    $("#mapMarkerType").select2({
                        dropdownParent: $("#mapMarkerType").parent(),
                        allowClear: false
                    }).on('select2:select', function (e) {
                        this.form.type_id = e.params.data.element.value
                    }.bind(this))
                })
                .catch(err => {
                    console.log(err);
                });

            axios.get('/mapMarkerCategories')
                .then(res => {
                    this.mapMarkerCategories = res.data.mapMarkerCategories;
                    $("#mapMarkerCategory").select2({
                        dropdownParent: $("#mapMarkerCategory").parent(),
                        allowClear: false
                    }).on('select2:select', function (e) {
                        this.form.category_id = e.params.data.element.value
                    }.bind(this))
                })
                .catch(err => {
                    console.log(err);
                });
        },

        loadMarkers(){
            axios.get('/mapMarkers?type_id=' + this.form.type_id + '&category_id=' + this.form.category_id)
                .then(res => {
                    this.markers = res.data.markers;
                    //console.log(this.markers);
                    this.clusterGroup = L.markerClusterGroup(); // create the new clustergroup

                    this.markers.forEach((marker) => {
                        this.clusterGroup.addLayer(
                            this.createMarker(
                                marker.latitude,
                                marker.longitude,
                                marker.title,
                                marker.description,
                                'll-marker',
                                this.getCss('type', marker.type_id)['css_icon'],
                                this.getCss('type', marker.category_id)['color'],
                                this.getCss('category', marker.category_id)['shape'],
                                'fa'
                            )
                        ); // add marker to the clustergroup
                    });
                    this.map.addLayer(this.clusterGroup); // add clustergroup to the map
                })
                .catch(err => {
                    console.log(err);
                });
        },
        async markerSearch(){
            $("#loading-events").show();
            try {
                this.events = (await axios.post('/eventSubscriptions/getEvents', {
                    search: this.search,
                    page: 1,
                    plugin: 'evewa'
                })).data.message.lesePlrlpVeranstaltungen.data;
            } catch (error) {
                console.log(error);
            }
            this.refreshMap();
        },

        sendNominatimRequest() {
            axios.get('https://nominatim.openstreetmap.org/search?polygon_geojson=1&format=geojson&polygon_threshold=0.001&country=de&state=RP')
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


            this.map.flyToBounds(countryBounds);
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
                            this.createMarker(
                                res.data[0].lat,
                                res.data[0].lon,
                                data.ARTIKEL,
                                data.LINK_DETAIL,
                                'll-marker',
                                'fa-circle',
                                '#2471A3',
                                'circle',
                                'fa'
                            )
                        ); // add marker to the clustergroup
                    });
            });
            this.map.addLayer(this.clusterGroup); // add clustergroup to the map
        },
        getCss(type, id){

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
        createMarker(lat, lon, title, description, sidebar_target, icon, markerColor, shape, prefix){
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
                .bindPopup('<b>'+ title + '</b>' + description +'<br/>')
                .addTo(this.map).on('click', function(e) {
                    this.currentMarker = {
                        'title': title,
                        'description': description,
                    };
                    this.sidebar.open(sidebar_target);
                }.bind(this, sidebar_target));
        }

    },
    mounted() {
        this.map = L.map('map').setView([49.314908280766346, 8.413913138283617], 10); // PL Speyer location

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
        }).addTo(this.map);


        this.sidebar = L.control.sidebar('sidebar').addTo(map);

        this.bordersGroup = L.geoJSON().addTo(this.map);

        /*var overlays = {
            'Landesgrenze anzeigen': this.bordersGroup
        };

        L.control.layers(null, overlays, {
            collapsed: false
        }).addTo(this.map);*/

        this.sendNominatimRequest();

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
}
</style>
