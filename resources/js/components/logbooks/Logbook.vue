<template>
    <div class="row">
        <div class="col-md-12 pb-3">
            <div id="logbook_filter"
                 class="dataTables_filter"
                 v-if="logbook.entries.length > 0">
                <label>
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
                v-for="(entry, index) in logbook.entries"
                v-bind:key="entry.id"
                :first=" index === 0 "
                :entry="entry"
                :search="search"
                :logbook="logbook">
            </LogbookEntry>
        </div>
        <!-- /.col -->
    </div>
</template>

<script>
    import LogbookEntry from '../logbooks/LogbookEntry.vue';

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
