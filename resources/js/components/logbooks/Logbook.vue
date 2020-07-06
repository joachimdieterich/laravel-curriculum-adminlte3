<template> 
    <div class="row">
        <div class="col-md-12 pb-3">
            <div id="logbook_filter" class="dataTables_filter">
                <label >
                    <input type="search" 
                           class="form-control form-control-sm" 
                           placeholder="Suchbegriff" 
                           v-model="search"
                           aria-controls="curricula-datatable">
                </label>
            </div>
        </div>
        

        <div class="col-md-12">
             <LogbookEntry 
                v-for="entry in logbook.entries"
                v-bind:key="entry.id"
                :entry="entry"
                :search="search"
                :logbook="logbook"> 
            </LogbookEntry>
<!--             The time line 
            <div class="timeline">
                 timeline time label 
                <div class="time-label">
                    <span class="bg-green">10 Feb. 2014</span>
                </div>
                 /.timeline-label 
                <LogbookEntry 
                    v-for="entry in logbook.entries"
                    :entry="entry"> 
                </LogbookEntry>
                
                <div>
                  <i class="fas fa-clock bg-gray"></i>
                </div>
            </div>-->
        </div>
        <!-- /.col -->
    </div>
</template>

<script>
    import LogbookEntry from '../logbooks/LogbookEntry.vue'
    
    export default {
        props: {
            'logbook': Object,
        }, 
        data () {
            return {
                 search: ''
            };
        }, 
        
        methods: {
           
        },
        mounted() {
              
        },
        components: {
            LogbookEntry
        }
        
    }
</script>