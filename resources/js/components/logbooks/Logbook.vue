<template>
    <div class="row">

        <div v-permission="'logbook_entry_create'"
             class="col-md-12 pl-3 pt-0 pb-2">
            <button id="add-logbook-entry"
                    class="btn btn-success"
                    @click.prevent="open('logbook-entry-modal')">
                {{ trans('global.logbookEntry.create') }}
            </button>
        </div>

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
                 v-for="(entry, index) in entries"
                 v-bind:key="entry.id"
                 :first=" index === 0 "
                 :entry="entry"
                 :search="search"
                 :logbook="logbook">
            </LogbookEntry>
        </div>
        <!-- /.col -->
        <logbook-entry-modal></logbook-entry-modal>
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
                entries: [],
                search: ''
            };
        },

        methods: {
            open(modal) {
                this.$modal.show(modal, {'logbook_id': this.logbook.id});
            },

        },
        mounted() {
            this.entries = this.logbook.entries;
            this.$on('addLogbookEntry', function (newEntry) {
                this.entries.unshift(newEntry);       // Add newly created entry
            });

            this.$on('deleteLogbookEntry', function (deletedEntry) {
                let index = this.entries.indexOf(deletedEntry);
                this.entries.splice(index, 1);
            });
        },
        components: {
            LogbookEntry
        }

    }
</script>
