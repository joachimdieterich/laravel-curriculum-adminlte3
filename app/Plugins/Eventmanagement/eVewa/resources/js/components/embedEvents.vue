<template>
    <div class="col-12">


        <h3 class="pb-2">{{title}}</h3>
        <div class="input-group col-2 mr-0 pr-0 mb-4 float-right" >
            <input
                type="text" id="search"
                name="eventId"
                class="form-control"
                v-model="search"
                required
                @keyup.enter="loader()"
            />

            <div class="input-group-append" @click="loader()">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
        </div>
        <button v-for="(value, id) in tags"
            type="button"
            class="btn btn-default mr-4 mb-4"
            @click="loader(id)">
            {{ value }}
        </button>

<hr style="clear:both">

        <div v-for="event in entries" class="border-bottom card collapsed-card">
            <div class="card-header pointer">
            <span data-target="'navigator-item-content-'+event.ARTIKEL_NR" data-card-widget="collapse">{{event.ARTIKEL}} </span>
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
            <div :id="'navigator-item-content-'+event.ARTIKEL_NR" class="card-body collapse">
                <div class="row">
                    <div class="col-2"><strong>Beschreibung</strong></div>
                    <div class="col-10 pre-formatted" v-html="event.BEMERKUNG"></div>

                    <div class="col-2"><strong>Termine</strong></div>
                    <div class="col-10">
                        <div v-for="termin in event.termine" class="row m--padding-bottom-5">
                            <div class="col-2 m-badge m-badge--info m-badge--wide m-badge--rounded">{{ dateforHumans(termin.DATUM) }}</div>
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
                    <div class="col-10 pt-2" v-html="event.ARTIKEL_NR"></div>

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

        <div v-if="Object.keys(entries).length > 0"
             class="row" >
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
            <div class="col-12 col-centered"
                 style="text-align: center; padding-top:50px"
                >
                <span >
                    {{ eventlinkdescription }}
                </span>


                <a
                   :href="eventlinkurl"
                    type="button"
                    class="btn btn-block btn-default col-6 col-centered mt-2">
                    {{ eventlinktitle }}
                </a>
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
            'title': String
        },
        data() {
            return {
                entries: [],
                page:    1,
                tags: {},
                errors:  {}
            }
        },
        methods: {
            async loader(id = this.search) {
                $("#loading-events").show();
                try {
                    this.entries = (await axios.post('/eventSubscriptions/getEvents', {
                        search: id,
                        page: this.page,
                        plugin: 'evewa'
                    })).data.message.lesePlrlpVeranstaltungen.data;
                    $("#loading-events").hide();
                } catch(error) {
                    //this.errors = error.response.data.errors;
                }
                this.tags = JSON.parse(this.eventsearchtag);
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
