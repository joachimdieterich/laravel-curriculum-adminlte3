<template>
    <div class="col-12">      
<!--        <div id="loadingEvents" class="overlay text-center" style="width:100% !important;">
            <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
            <span class="sr-only">Loading...</span>
        </div>-->
        
        <div v-for="event in entries.lesePlrlpVeranstaltungen.data" class=" pb-3 border-bottom">
            <h5 > {{event.ARTIKEL}}</h5>
            <div class="row">
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
                errors: {}
            }
        },
        methods: {
            async loader() {
                try {
                    this.entries = (await axios.post('/eventSubscriptions/getEvents', {
                        subscribable_type: this.subscribable_type(),
                        subscribable_id: this.model.id,
                        search: this.model.title,
                        plugin: 'evewa'
                    })).data.message;
                    
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