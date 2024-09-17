<template>
    <div class="row">
        <div v-permission="'logbook_entry_create'"
             class="col-md-12 pl-3 pt-0 pb-2">
            <button id="add-logbook-entry"
                    class="btn btn-success"
                    @click.prevent="add()">
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
            <AbsenceModal></AbsenceModal>
            <ContentModal></ContentModal>
            <TaskModal></TaskModal>
            <MediumPreviewModal></MediumPreviewModal>
            <subscribe-objective-modal></subscribe-objective-modal>
            <lms-modal></lms-modal>
            <MediumModal
                subscribable_type="App\\Logboook"
                :subscribable_id="logbook.id"
                :show="this.mediumStore.getShowMediumModal"
                @close="this.mediumStore.setShowMediumModal(false)"
            ></MediumModal>
            <LogbookModal></LogbookModal>
            <LogbookEntryModal></LogbookEntryModal>
            <SubscribeModal></SubscribeModal>
        </Teleport>
        <teleport
            v-if="$userId == logbook.owner_id"
            to="#customTitle">
            <small>{{ logbook.title }} </small>
            <a class="btn btn-flat"
               @click="editLogbook(logbook)"
            >
                <i class="fa fa-pencil-alt text-secondary"></i>
            </a>
            <button
                v-permission="'kanban_create'"
                v-if="$userId == logbook.owner_id"
                class="btn btn-flat"
                @click="share()">
                <i class="fa fa-share-alt text-secondary"></i>
            </button>
        </teleport>
    </div>
</template>

<script>
import MediumModal from "../media/MediumModal"
import LogbookModal from "../logbook/LogbookModal";
import LogbookEntry from '../logbookEntry/LogbookEntry.vue';
import LogbookPrintOptions from "./LogbookPrintOptions";
import TaskModal from "../task/TaskModal.vue";
import SubscribeModal from "../subscription/SubscribeModal";

import {useMediumStore} from "../../store/media";
import LmsModal from "../lms/LmsModal.vue";
import ContentModal from "../content/ContentModal.vue";
import {useGlobalStore} from "../../store/global";
import MediumPreviewModal from "../media/MediumPreviewModal.vue";
import SubscribeObjectiveModal from "../objectives/SubscribeObjectiveModal.vue";
import AbsenceModal from "../absence/AbsenceModal.vue";
import LogbookEntryModal from "../logbookEntry/LogbookEntryModal.vue";

export default {
    name: "Logbook",
    components:{
        LogbookEntryModal,
        AbsenceModal,
        SubscribeObjectiveModal,
        MediumPreviewModal,
        ContentModal,
        LmsModal,
        LogbookModal,
        LogbookPrintOptions,
        LogbookEntry,
        MediumModal,
        SubscribeModal,
        TaskModal
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
        const globalStore = useGlobalStore();

        return {
            globalStore,
            mediumStore
        }
    },
    data() {
        return {
            componentId: this.$.uid,
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

        this.$eventHub.on('logbookEntry-added', (entry) => {
            this.globalStore?.closeModal('logbook-entry-modal');
            //todo: reload
        });
        this.$eventHub.on('logbookEntry-updated', (entry) => {
            this.globalStore?.closeModal('logbook-entry-modal');
            //todo: reload
        });

        this.$eventHub.on('logbook-updated', (logbook) => {
            this.globalStore?.closeModal('logbook-modal');
            this.currentLogbook = logbook;
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
    },
    methods: {
        add(){
            this.globalStore?.showModal('logbook-entry-modal',
                {
                    'logbook_id': this.logbook.id
                });
        },
        editLogbook(logbook){
            this.globalStore?.showModal('logbook-modal', logbook);
        },
        togglePrintOptions() {
            this.showPrintOptions = !this.showPrintOptions;
        },
        share(){
            this.globalStore?.showModal('subscribe-modal',
                {
                    'modelId': this.logbook.id,
                    'modelUrl': 'logbook',
                    'shareWithUsers': true,
                    'shareWithGroups': true,
                    'shareWithOrganizations': true,
                    'shareWithToken': true,
                    'canEditCheckbox': true
                });
        },
    }
}
</script>
