<template>
    <div class="row">
        <div v-permission="'logbook_entry_create'"
             class="col-md-12 pl-3 pt-0 pb-2">
            <button id="add-logbook-entry"
                    class="btn btn-success"
                    @click.prevent="open('logbook-entry-modal')">
                {{ trans('global.logbookEntry.create') }}
            </button>
            <button
                id="print-logbook"
                type="button"
                class="pull-right btn btn-tool pt-3"
                @click.p.prevent="togglePrintOptions();"
            >
                <i class="fa fa-print"></i>
            </button> <!--<button
                id="print-logbook"
                type="button"
                class="pull-right btn btn-tool pt-3"
                @click="this.$eventHub.emit('setMediumModalParams', this.$mediumModalParams)"
            >
                <i class="fa fa-usert"></i>d
            </button>-->
        </div>

        <div class="col-md-12"
             v-if="showPrintOptions">
            <LogbookPrintOptions
                :logbook="logbook"
                :period="period">
            </LogbookPrintOptions>
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
<!--
        <logbook-entry-modal></logbook-entry-modal>
        <lms-modal></lms-modal>
-->

        <Teleport to="body">
            <MediumModal
                subscribable_type="App\\Logboook"
                :subscribable_id="logbook.id"
                :show="this.mediumStore.getShowMediumModal"
                @close="this.mediumStore.setShowMediumModal(false)"
            ></MediumModal>
            <LogbookModal
                :show="this.showLogbookModal"
                @close="this.showLogbookModal = false"
                :params="this.currentLogbook"
            ></LogbookModal>
        </Teleport>
    </div>
</template>

<script>
import MediumModal from "../media/MediumModal"
import LogbookModal from "../logbook/LogbookModal";
import LogbookEntry from '../logbookEntry/LogbookEntry.vue';
import LogbookPrintOptions from "./LogbookPrintOptions";
import {useMediumStore} from "../../store/media";

export default {
    name: "Logbook",
    components:{
        LogbookModal,
        LogbookPrintOptions,
        LogbookEntry,
        MediumModal
    },
    props: {
        logbook: {
            default: null
        },
        period: {
            default: null
        },
    },
    setup () { //use database store
        const mediumStore = useMediumStore();
        return {
            mediumStore
        }
    },
    data() {
        return {
            componentId: this._uid,
            showLogbookModal: false,
            currentLogbook: {},
            entries: [],
            search: '',
            showPrintOptions: false
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar');
        this.entries = this.logbook.entries;

        this.currentLogbook = this.logbook;

        this.$eventHub.on('logbook-updated', (logbook) => {
            this.currentLogbook = logbook;
            this.showLogbookModal = false;
        });

        // Entries
        this.$eventHub.on('addLogbookEntry', (newEntry) => {
            if (newEntry.subject == undefined) {
                newEntry.subject = {
                    id: null,
                    title: null,
                };
            }
            this.entries.unshift(newEntry);       // Add newly created entry
        });
        this.$eventHub.on('updateLogbookEntry', (updatedEntry) => {
            const index = this.entries.findIndex(            // Find the index of the status where we should replace the item
                entry => entry.id === updatedEntry.id
            );
            // only update entry, do not manipulate relations.
            this.entries[index].title = updatedEntry.title;
            this.entries[index].description = updatedEntry.description;
            this.entries[index].updated_at = updatedEntry.updated_at;
            this.entries[index].begin = updatedEntry.begin;
            this.entries[index].end = updatedEntry.end;
        });
        this.$eventHub.on('updateSubjectBadge', (updatedEntry) => {
            const index = this.entries.findIndex(
                entry => entry.id === updatedEntry.entry_id
            );
            this.entries[index].subject = {
                id: updatedEntry.subject_id,
                title: updatedEntry.title,
            }
        });

        this.$eventHub.on('deleteLogbookEntry', function (deletedEntry) {
            let index = this.entries.indexOf(deletedEntry);
            this.entries.splice(index, 1);
        });
        // End Entries


    },
    methods: {
        editLogbook(){
            this.showLogbookModal = true;
        },
        togglePrintOptions() {
            this.showPrintOptions = !this.showPrintOptions;
        }
    }
}
</script>
