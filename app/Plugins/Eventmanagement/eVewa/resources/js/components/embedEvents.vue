<template>
    <div>
        <div class="p-3 m-0 text-white"
             style="background-color: #871D33">
            <a :href="this.backlinkurl"
               target="_self"
                class="text-white text-decoration-none">
                <i class="fa fa-arrow-left"></i>
                {{backlinktitle}}
            </a>
        </div>
        <div class="m-0"
             style="background-color: #EDEDED">
            <div class="input-group col-2 mr-0 p-3 float-right" >
                <input
                    type="text" id="search"
                    name="eventId"
                    class="form-control"
                    v-model="search"
                    required
                    @keyup.enter="loader(search, true)"
                />

                <div class="input-group-append" @click="loader(search, true)">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
            </div>
            <img src="/favicons/logo.png"
                 height="142"
                 class="ml-4">
            <h4 class="px-3">{{title}}</h4>
            <div class="col-12">

                <button v-for="(value, id) in tags"
                        type="button"
                        class="btn btn-default mr-3 mb-3"
                        @click="loader(id, true)">
                    {{ value }}
                </button>
            </div>

        </div>
        <div class="col-12 p-0">
            <div v-for="event in entries" class="border-bottom card collapsed-card mb-0">
                <div class="card-header pointer">
                    <span data-target="'navigator-item-content-'+event.ARTIKEL_NR" data-card-widget="collapse">
                        {{event.ARTIKEL}}
                    </span>
                    <div class="card-tools pull-right">
                        {{ dateforHumans(event.B_DAT, event.E_DAT) }}
                        <button
                            :id="'navigator-item-content-'+event.ARTIKEL_NR"
                            class="btn btn-tool"
                            :data-target="'#navigator-item-content-'+event.ARTIKEL_NR" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div :id="'navigator-item-content-'+event.ARTIKEL_NR" class="card-body collapse bg-gray-light">
                    <div class="row">
                        <div class="col-2"
                            v-if="event.BEZ_1_2.length > 2">
                            <strong>Untertitel</strong></div>
                        <div class="col-10 pre-formatted"
                             v-if="event.BEZ_1_2.length > 2"
                             v-dompurify-html="event.BEZ_1_2"></div>

                        <div class="col-2">
                            <strong>Beschreibung</strong></div>
                        <div class="col-10 pre-formatted" v-dompurify-html="event.BEMERKUNG"></div>

                        <div class="col-2"><strong>Termine</strong></div>
                        <div class="col-10">
                            <div v-for="termin in event.termine" class="row m--padding-bottom-5">
                                <div class="col-2 m-badge m-badge--info m-badge--wide m-badge--rounded">
                                    {{ dateforHumans(termin.DATUM) }}
                                </div>
                                <div class="col-10 text-left">
                                    <i class="fa fa-clock-o"></i> {{ termin.BEGINN }} - {{ termin.ENDE }}
                                    <span v-if="termin.ARTIKEL !== event.ARTIKEL">
                                        {{ termin.ARTIKEL }}
                                    </span>

                                    <span class="pull-right">
                                        {{ termin.VO_ORT }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-2 pt-2"><strong>VA-Nummer</strong></div>
                        <div class="col-10 pt-2" v-dompurify-html="event.ARTIKEL_NR"></div>

                        <div class="col-12 mt-2">
                            <a :href="event.LINK_DETAIL"
                               class="btn btn-default"
                               target="_blank">
                                <i class="fa fa-info"></i> Details/Anmeldung
                            </a>

                            <a :href="event.LINK_DETAIL+'&print=1'"
                               onclick="return !window.open(this.href, 'Drucken', 'width=800,scrollbars=1')"
                               class="btn btn-default"
                               target="_blank">
                                <i class="fa fa-print"></i> Drucken
                            </a>

    <!--                        <a :href="event.LINK_ANMELDUNG"
                               class="btn btn-secondary"
                               target="_blank">
                                <i class="fa fa-sign-in"></i> Anmelden
                            </a>-->
                        </div>
                    </div>
                </div>
            </div>

            <div id="loading-events" class="overlay text-center" style="width:100% !important;">
                <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>

        </div>
        <div class="m-0"
             style="background-color: #EDEDED">
            <div v-if="Object.keys(entries).length > 0"
                 class="row mr-1 pt-3" >
                <span
                    class="col-8">
                </span>
                <span
                    v-if="Object.keys(entries).length > 0"
                    class="col-2 pr-0">
                        <button v-if="page > 1"
                                type="button"
                                class="btn btn-block btn-secondary"
                                @click="lastPage()">
                            <i class="fa fa-arrow-left"></i>
                        </button>
                    </span>

                <span
                    v-if="Object.keys(entries).length > 9"
                    class="col-2">
                        <button type="button"
                                class="btn btn-block btn-secondary"
                                @click="nextPage()">
                            <i class="fa fa-arrow-right"></i>
                        </button>
                    </span>
            </div>
            <div class="row" >
                <div class="col-12 col-centered pt-2"
                     style="text-align: center;">
                    <span >
                        {{ eventlinkdescription }}
                    </span>

                    <a
                        :href="eventlinkurl"
                        type="button"
                        class="btn btn-block btn-default col-6 col-centered my-2">
                        {{ eventlinktitle }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
import moment from 'moment';

    export default {
        props: {
            'search': {},
            'eventlinktitle': String,
            'eventlinkdescription': String,
            'eventlinkurl': String,
            'eventsearchtag': String,
            'title': String,
            'backlinktitle': String,
            'backlinkurl': String
        },
        data() {
            return {
                entries: [],
                page:    1,
                tags: {},
                errors:  {},
                currentSearch: ''
            }
        },
        methods: {
            async loader(id = this.search, reset = false) {
                if (reset){
                    this.page = 1;
                }
                if (id == ''){
                    id = this.currentSearch;
                }

                $("#loading-events").show();
                try {
                    this.entries = (await axios.post('/eventSubscriptions/getEvents', {
                        search: id,
                        page: this.page,
                        plugin: 'evewa'
                    })).data.events.data;
                    $("#loading-events").hide();
                } catch(error) {
                    //this.errors = error.response.data.errors;
                }
                this.tags = JSON.parse(this.eventsearchtag);
                this.currentSearch = id;
                this.search = ''; //empty search field
            },
            lastPage() {
                this.page = this.page - 1
                if (this.page == 0){
                    this.page = 1;
                } else{
                    this.loader();
                }

            },
            nextPage() {
                this.page = this.page + 1;
                this.loader();
            },
            dateforHumans(begin, end = null) {
                if (end === begin || end === null){
                    return moment(begin).locale('de').format('LL');
                } else {
                    return moment(begin).locale('de').format('LL') + " - " + moment(end).locale('de').format('LL');
                }
            },

        },
        mounted() {
            this.loader();
        },
        computed: {},

    }
</script>
<style>
.pre-formatted {
    white-space: pre-wrap;
}
</style>
