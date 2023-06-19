<template>
    <div id="outermap">
        <input
            type="text"
            v-model="search"
            @keyup.enter="loader"
        />
        <div id="map"></div>
    </div>
</template>
<script>
import { Icon } from 'leaflet';
import axios from 'axios';

export default {
    data() {
        return {
            events: {},
            map:[],
            search: ''
        }
    },
    methods: {
        async loader() {

            $("#loading-events").show();
            try {
                this.events = (await axios.post('/eventSubscriptions/getEvents', {
                    search: this.search,
                    page: 1,
                    plugin: 'evewa'
                })).data.message.lesePlrlpVeranstaltungen.data;
            } catch (error) {
                //this.errors = error.response.data.errors;
            }
            this.refreshMap();
        },

        refreshMap(){
            // parse property from Observer to JSON
            const eventsData = JSON.parse(JSON.stringify(this.events));
console.log(eventsData);
            // add all event-locations
            Object.entries(eventsData).forEach(event => {
                const data = event[1];
                const address = data.termine.key_0.VO_ADRESSE;
                console.log(address);
                const url = 'https://nominatim.openstreetmap.org/search?q=' + encodeURI(address) + '&format=jsonv2';
                console.log(url);
                axios.get(url)
                    .then(res => {
                        L.marker([res.data[0].lat, res.data[0].lon], {
                            'title': data.ARTIKEL // accessibility
                        }).bindPopup(data.ARTIKEL).addTo(this.map);
                    });
            })
        }
    },
    mounted() {
        this.map = L.map('map', {
            center: [49.314908280766346, 8.413913138283617], // PL Speyer location
            zoom: 9
        });

        this.loader();


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


    }
}
</script>
<style scoped>
#map, #outermap {
    height: 100%;
}
</style>
