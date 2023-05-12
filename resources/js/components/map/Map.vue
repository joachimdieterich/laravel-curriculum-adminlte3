<template>
    <div id="map"></div>
</template>
<script>
import { Icon } from 'leaflet';
import axios from 'axios';

export default {
    data() {
        return {
            events: {
                key_0: {
                    "MANDANT": "M001",
                    "ARTIKEL": "Gehirn und Lernen - Achtsamkeit und Meditation als Hilfsmittel für den Einsatz in der Schule",
                    "ARTIKEL_NR": "231417FGP3",
                    "B_DAT": "2023-04-13",
                    "E_DAT": "2023-04-13",
                    "PLAN_STAT": "Findet statt",
                    "BEMERKUNG": 'Schülerinnen und Schüler zu befähigen, Expertinnen und Experten ihres eigenen Lernens zu werden, ist ein wichtiges Ziel von Schule. Dabei helfen ihnen das Wissen um die biologischen Grundlagen des Lernens im Gehirn und die daraus abgeleiteten Vorgehensweisen beim Lernen. Am Otto-Schott-Gymnasium in Mainz wird "Gehirn und Lernen" (= GuL) seit einigen Jahren als Enrichment in den Hochbegabtenklassen und in Klassenleiterstunden des Regelgymnasiums unterrichtet. Die Fortbildungsreihe stellt Inhalte und Vorgehensweise dieses Ansatzes vor. Die Veranstaltungen stehen in Zusammenhang. Der Besuch einzelner Veranstaltungen ist aber dennoch sinnvoll möglich. Inhalte: • Einführung in das Konzept der Achtsamkeit • Wirkung von Achtsamkeits- und Meditationsübungen auf das Gehirn • Kennenlernen verschiedener Übungen und Reflexion (Selbsterfahrung) • Achtsamkeit und Meditation als Hilfsmittel für erfolgreiches Lernen • Diskussion über den Umgang mit auftretenden Schwierigkeiten • Ideensammlung zum Einsatz von Achtsamkeitsübungen in der Schule Ziele: • Kennenlernen theoretischer Grundlagen • Kennenlernen einzelner Beispiele für die Umsetzung im Unterricht • Entwickeln eigener Ideen, wie man dieses Wissen im Unterricht nutzen kann, um zu differenzieren und die Schülerinnen und Schüler individuelle Lernwege gehen zu lassen Veranstaltungsform: Tagung, E-Session-Reihe mit einem begleitenden Kurs auf der Moodle-Lernplattform. Dozentinnen und Dozenten: Lehrkräfte des Otto-Schott-Gymnasiums, Mainz ',
                    "KATEGORIE": "Tagung",
                    "ZIELGRUPPE": [
                    ],
                    "LINK_ANMELDUNG": "https://evewa.bildung-rp.de/workflow?id=0&va_id=55691&m=dbca5ecc-a3b5-11e9-8e0c-0050569784c7&ev_workflow2_guid=34ef7572-1074-11ec-9e02-1287f4009885",
                    "LINK_DETAIL": "https://evewa.bildung-rp.de/veranstaltungskatalog_detail?id=55691&m=dbca5ecc-a3b5-11e9-8e0c-0050569784c7",
                    "ANBIETER": [
                    ],
                    "termine": {
                        "key_0": {
                            "ARTIKEL": "Gehirn und Lernen - Achtsamkeit und Meditation als Hilfsmittel für den Einsatz in der Schule",
                            "ARTIKEL_NR": "231417FGP3",
                            "DATUM": "2023-04-13",
                            "BEGINN": "14:00",
                            "ENDE": "16:00",
                            "RAUM": [
                            ],
                            "VO_ADRESSE": "Otto-Schott-Gymnasium Gonsenheim An Schneiders Mühle 1 55122 Mainz",
                            "VO_ORT": "Mainz"
                        }
                    }
                },
                key_1: {
                    "MANDANT": "M001",
                    "ARTIKEL": "Drogen, Medien und Co – wie gelingt ein risikokompetenter Konsum?",
                    "ARTIKEL_NR": "2312171006",
                    "B_DAT": "2023-04-17",
                    "E_DAT": "2023-04-17",
                    "PLAN_STAT": "Findet statt",
                    "BEMERKUNG": 'Workshop: **** Unterrichtspraxis/ Praxisbezug: *** Austausch: **** Methodik/ Didaktik: * Materialentwicklung: **** Innovation/ Wissenschaft: *** Kurze Beschreibung: Gemeinsam gegen die Sucht ist das Motto des Hauses der Diakonie Die "Fachstelle Sucht" bietet Informations-, Beratungs- und Behandlungsangebote für Interessierte, Betroffene und Angehörige: -Prävention - niedrigschwellige Hilfen (zum Beispiel bei gesundheitlichen und finanziellen Problemen) - spezielle Angebote für Männer und Frauen - ambulante Rehabilitation - Therapievermittlung - Angebote für Kinder und Jugendliche aus suchtkranken Familien - Nachsorge. Täglich sind wir zahlreichen Verführungen ausgesetzt, wie wir unseren Alltag angenehmer, entspannter oder abwechslungsreicher gestalten können. Es bedarf zielgruppengerechter Präventionsmaßnahmen, damit sich Kinder und Jugendliche kritisch, risikobewusst und ohne schwerwiegende Folgen damit auseinandersetzen können, eigene Bedürfnisse wahrnehmen und auch vertreten können. Erfolgreiche Suchtprävention fördert psychische und soziale Ressourcen, stärkt die Persönlichkeit und zeigt so Alternativen zum Suchtmittelkonsum auf. Die Angebote der "Fachstellen Sucht" stehen allen zur Verfügung. Die Schule kann untestützt werden, um den Kontakt zu betroffenen Schüler*innen aufzubauen. Es werden Präventionen vorgestellt, aber auch akute Angebote für Betroffene gegeben. Schwerpunkte an diesem Tag sind: - Cannabis – schlau statt breit - 24/7 – always on (Online Sucht) - KlarSichtparcours zur Tabak- und Alkoholprävention Lehrplanbezug/ Berufsfelder/ Zielgruppen: - Fachschule Sozialwesen - Schulsozialarbeiter und Schulsozialarbeiterinnen - Lehrer und Lehrerinnen Inhalte: - Sucht - Beratung - Beratungsansatz - Therapieangebote - Cannabis - Alkohol - Tabak - Online Sucht Veranstaltungsform: Tagung Ziele: - Sensibilsierung - Fachwissen über Sucht - Methoden für den Umgang - praktische Hilfen kennenlernen und einüben Dauer: 1 Tag Voraussetzung: keine Dozentin: Stefanie Fischer (Diplom Pädagogin im Haus der Diakonie) arbeitet in der Fachstelle Suchtprävention. Johanna Surrey (Diplom Sozialpädagogin Haus der Diakonie) ist in der Fachstelle für Glückspielsucht und Medienabhängigkeit tätig. Anja Grey (Diplom Sozialpädagogin im Haus der Diakonie) ist in der Suchtberatung eingesetzt.',
                    "KATEGORIE": "Tagung",
                    "ZIELGRUPPE": [
                    ],
                    "LINK_ANMELDUNG": "https://evewa.bildung-rp.de/workflow?id=0&va_id=54491&m=dbca5ecc-a3b5-11e9-8e0c-0050569784c7&ev_workflow2_guid=34ef7572-1074-11ec-9e02-1287f4009885",
                    "LINK_DETAIL": "https://evewa.bildung-rp.de/veranstaltungskatalog_detail?id=54491&m=dbca5ecc-a3b5-11e9-8e0c-0050569784c7",
                    "ANBIETER": [
                    ],
                    "termine": {
                        "key_0": {
                            "ARTIKEL": "Tipps und Tricks für Sucht im Schulalltag",
                            "ARTIKEL_NR": "2312171006",
                            "DATUM": "2023-04-17",
                            "BEGINN": "09:00",
                            "ENDE": "15:00",
                            "RAUM": {
                                "key_0": {
                                    "RAUM": "C.EG.06 Seminarraum im Hauptgebäude (40 Personen)",
                                    "RAUM_NR": "C.EG.06",
                                    "STANDORT": "Pädagogisches Landesinstitut Rheinland-Pfalz 1054314",
                                    "ORT": "Speyer",
                                    "MGL_N_RMGSA_LFDNR": "32",
                                    "MGL_N_RMGUID": "83b6a9a0-2654-11e6-94b6-001517f7c8c0",
                                    "MGL_N_RMZMGL_1_63": [
                                    ],
                                    "MGL_N_RMSCHREIBRECHT": "1",
                                    "GSA_LFDNR": "32"
                                },
                                "key_1": {
                                    "RAUM": "C.EG.08 Besprechungsraum im Hauptgebäude (12 Personene)",
                                    "RAUM_NR": "C.EG.08",
                                    "STANDORT": "Pädagogisches Landesinstitut Rheinland-Pfalz 1054314",
                                    "ORT": "Speyer",
                                    "MGL_N_RMGSA_LFDNR": "37",
                                    "MGL_N_RMGUID": "83b6b10c-2654-11e6-94b6-001517f7c8c0",
                                    "MGL_N_RMZMGL_1_63": [
                                    ],
                                    "MGL_N_RMSCHREIBRECHT": "1",
                                    "GSA_LFDNR": "37"
                                },
                                "key_2": {
                                    "RAUM": "C.EG.09 Besprechungsraum im Hauptgebäude (12 Personen)",
                                    "RAUM_NR": "C.EG.09",
                                    "STANDORT": "Pädagogisches Landesinstitut Rheinland-Pfalz 1054314",
                                    "ORT": "Speyer",
                                    "MGL_N_RMGSA_LFDNR": "38",
                                    "MGL_N_RMGUID": "83b6b288-2654-11e6-94b6-001517f7c8c0",
                                    "MGL_N_RMZMGL_1_63": [
                                    ],
                                    "MGL_N_RMSCHREIBRECHT": "1",
                                    "GSA_LFDNR": "38"
                                }
                            },
                            "VO_ADRESSE": "Pädagogisches Landesinstitut Rheinland-Pfalz Butenschönstraße 2 67346 Speyer",
                            "VO_ORT": "Speyer"
                        }
                    }
                }
            }
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