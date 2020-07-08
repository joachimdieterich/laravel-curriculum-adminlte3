<template>
    <div class="col-12">      
<!--        <div id="loadingEvents" class="overlay text-center" style="width:100% !important;">
            <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
            <span class="sr-only">Loading...</span>
        </div>-->
        
        <div class="form-group "
            :class="errors.title ? 'has-error' : ''"
              >
            <label for="title">{{ trans('global.eventSubscription.search') }}</label>
            <input
                type="text" id="search"
                name="eventId"
                class="form-control"
                v-model="search"
                required
                @keyup.enter="loader()" 
                />
             <p class="help-block" v-if="errors.searc" v-text="errors.search[0]"></p>
        </div>
        <div v-for="event in entries" class="border-bottom card collapsed-card">
            <div class="card-header">
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
              <div class="col-2"><strong>VA-Nummer</strong></div>
                <div class="col-10" v-html="event.ARTIKEL_NR"></div>
                
                <div class="col-2"><strong>Beschreibung</strong></div>
                <div class="col-10" v-html="event.BEMERKUNG"></div>
                
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
</template>


<script>
    
    export default {
        props: {
                'model': {},
              },
        data() {
            return {
                entries: null,
                search: this.model.title.replace(/(<([^>]+)>)/ig,""),
                errors: {}
            }
        },
        methods: {
            async loader() {
                try {
                    this.entries = (await axios.post('/eventSubscriptions/getEvents', {
                        subscribable_type: this.subscribable_type(),
                        subscribable_id: this.model.id,
                        search: this.search,
                        plugin: 'evewa'
                    })).data.message.lesePlrlpVeranstaltungen.data;
                    
                } catch(error) {
                    //this.errors = error.response.data.errors;
                }
            },
            subscribable_type() {
                var reference_class = 'App\\TerminalObjective';
                if (typeof this.model.terminal_objective === 'object'){
                    reference_class = 'App\\EnablingObjective';
                } 

                return reference_class;
            },
  
        },
        computed: {
            
        },
//        watch: {
//            entries: function (value, oldValue) { 
//                $("#loadingEvents").remove();
//            }
//        },
        bevorOpen() {
            
        },
   
    }
</script>