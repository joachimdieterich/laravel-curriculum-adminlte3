<template> 
    <div class="row">
        <div class="col-md-12">
             <LogbookEntry 
                v-for="entry in logbook.entries"
                v-bind:key="entry.id"
                :entry="entry"
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