<template>
    <div class="col-12">
        {{ trans('global.eventSubscription.search') }}
        <div class="row my-3">
            <span class="col-6 ">
                {{ trans('global.eventSubscription.search_subject') }}
                <button type="button" class="btn btn-block btn-secondary" @click="loader(curriculum.subject.title)">
                    {{ curriculum.subject.title }}
                </button>
            </span>
            <span class="col-6">
                {{ trans('global.eventSubscription.search_keyword') }}

                <div class="input-group my-colorpicker2 colorpicker-element" data-colorpicker-id="2">
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

            </span>
        </div>


        <div v-for="event in entries" class="border-bottom card collapsed-card">
            <div class="card-header pointer">
            <span data-target="'navigator-item-content-'+event.ARTIKEL_NR" data-card-widget="collapse">{{event.ARTIKEL}}</span>
            <div class="card-tools pull-right">
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
                    <div class="col-2"><strong>VA-Nummer</strong></div>
                    <div class="col-10" v-html="event.ARTIKEL_NR"></div>

                    <div class="col-2"><strong>Beschreibung</strong></div>
                    <div class="col-10 pre-formatted" v-html="event.BEMERKUNG"></div>

                    <div class="col-2"><strong>Veranstalter</strong></div>
                    <div class="col-10" v-html="event.MANDANT"></div>


                    <div class="col-12 mt-2">
                        <a :href="event.LINK_DETAIL"
                           class="btn bg-gray"
                           target="_blank">
                            <i class="fa fa-info"></i> Details
                        </a>

                        <a :href="event.LINK_DETAIL+'&print=1'"
                           onclick="return !window.open(this.href, 'Drucken', 'width=800,scrollbars=1')"
                           class="btn bg-gray-light"
                           target="_blank">
                            <i class="fa fa-print"></i> Drucken
                        </a>

                        <a :href="event.LINK_ANMELDUNG"
                           class="btn bg-info"
                           target="_blank">
                            <i class="fa fa-sign-in"></i> Anmelden
                        </a>
                    </div>
                </div>
            </div>
        </div>

<!--        <div id="loading-events" class="overlay text-center" style="width:100% !important;">
            <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
            <span class="sr-only">Loading...</span>
        </div>-->

        <div v-if="Object.keys(entries).length > 0"
             class="row" >
                <span
                    v-if="Object.keys(entries).length > 0"
                    class="col-6">
                    <button v-if="page === 1"
                            type="button"
                            class="btn btn-block btn-secondary disabled"
                            @click="lastPage()">
                        <i class="fa fa-arrow-left"></i>
                    </button>
                    <button v-else
                            type="button"
                            class="btn btn-block btn-secondary"
                            @click="lastPage()">
                    <i class="fa fa-arrow-left"></i>
                </button>
                </span>

            <span
                v-if="Object.keys(entries).length > 9"
                class="col-6">
                    <button type="button"
                            class="btn btn-block btn-secondary"
                            @click="nextPage()">
                        <i class="fa fa-arrow-right"></i>
                    </button>
                </span>
        </div>

    </div>
</template>


<script>

    export default {
        props: {
                'model': {},
                'curriculum': {},
              },
        data() {
            return {
                entries: [],
                search:  '',//this.model.title.replace(/(<([^>]+)>)/ig,""),
                page:    1,
                errors:  {}
            }
        },
        methods: {
            async loader(search) {

                try {
                    this.search = (search ? search : this.search);

                    this.entries = (await axios.post('/eventSubscriptions/getEvents', {
                        subscribable_type: this.subscribable_type(),
                        subscribable_id: this.model.id,
                        search: (search ? search : this.search),
                        page: this.page,
                        plugin: 'evewa'
                    })).data.message.lesePlrlpVeranstaltungen.data;
                    $("#loading-events").hide();

                } catch(error) {
                    //this.errors = error.response.data.errors;
                }
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
            subscribable_type() {
                var reference_class = 'App\\TerminalObjective';
                if (typeof this.model.terminal_objective === 'object'){
                    reference_class = 'App\\EnablingObjective';
                }

                return reference_class;
            },

        },
        computed: {},

    }
</script>
<style>
.pre-formatted {
    white-space: pre-wrap;
}
</style>
