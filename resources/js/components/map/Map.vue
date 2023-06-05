<template>
    <div id="map"></div>
</template>
<script>
import { Icon } from 'leaflet';
import axios from 'axios';

export default {
    data() {
        return {
            events: {}
        }
    },
    mounted() {
        const map = L.map('map', {
            center: [49.314908280766346, 8.413913138283617], // PL Speyer location
            zoom: 9
        });

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
        }).addTo(map);

        // parse property from Observer to JSON
        const eventsData = JSON.parse(JSON.stringify(this.events));

        // add all event-locations
        Object.entries(eventsData).forEach(event => {
            const data = event[1];
            const address = data.termine.key_0.VO_ADRESSE;
            const url = 'https://nominatim.openstreetmap.org/search?q=' + encodeURI(address) + '&format=jsonv2';

            axios.get(url)
                .then(res => {
                    L.marker([res.data[0].lat, res.data[0].lon], {
                        'title': data.ARTIKEL // accessibility
                    }).bindPopup(data.ARTIKEL).addTo(map);
                });
        })
    }
}
</script>
<style scoped>
#map {
    height: 100%;
}
</style>