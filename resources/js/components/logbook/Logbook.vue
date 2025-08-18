<template>
    <div class="row">
        <div
            v-permission="'logbook_entry_create'"
            class="col-md-12 pl-3 pt-0 pb-2"
        >
            <button
                id="add-logbook-entry"
                class="btn btn-success"
                @click.prevent="openEntryModal()"
            >
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

        <div v-if="showPrintOptions"
            class="col-md-12"
        >
            <LogbookPrintOptions
                :logbook="logbook"
                :period="period"
            />
        </div>

        <div class="col-md-12">
            <LogbookEntry v-for="(entry, index) in entries"
                v-bind:key="entry.id"
                :first="index === 0"
                :entry="entry"
                :search="search"
                :logbook="logbook"
            />
        </div>
        <!-- /.col -->
<!--
        <logbook-entry-modal></logbook-entry-modal>
        <lms-modal></lms-modal>
-->
        <Teleport to="body">
            <AbsenceModal/>
            <ContentModal/>
            <TaskModal/>
            <SubscribeObjectiveModal/>
            <!-- <SetAchievementsModal/> -->
            <lms-modal/>
            <MediumModal/>
            <LogbookModal/>
            <LogbookEntryModal/>
            <LogbookEntrySubjectModal/>
            <SubscribeModal/>
        </Teleport>
        <teleport to="#customTitle">
            <small>{{ logbook.title }}</small>
            <a v-if="logbook.owner_id == $userId || checkPermission('is_admin')"
                class="btn btn-flat text-secondary px-2 mx-1"
                @click="editLogbook(logbook)"
            >
                <i class="fa fa-pencil-alt"></i>
            </a>
            <a v-if="logbook.owner_id == $userId || checkPermission('is_admin')"
                class="btn btn-flat text-secondary px-2"
                @click="share()"
            >
                <i class="fa fa-share-alt"></i>
            </a>
        </teleport>
    </div>
</template>
<script>
import MediumModal from "../media/MediumModal.vue"
import LogbookModal from "../logbook/LogbookModal.vue";
import LogbookEntry from '../logbookEntry/LogbookEntry.vue';
import LogbookPrintOptions from "./LogbookPrintOptions.vue";
import TaskModal from "../task/TaskModal.vue";
import SubscribeModal from "../subscription/SubscribeModal.vue";
import LmsModal from "../lms/LmsModal.vue";
import ContentModal from "../content/ContentModal.vue";
import {useGlobalStore} from "../../store/global";
import SubscribeObjectiveModal from "../objectives/SubscribeObjectiveModal.vue";
// import SetAchievementsModal from "../plan/SetAchievementsModal.vue";
import AbsenceModal from "../absence/AbsenceModal.vue";
import LogbookEntryModal from "../logbookEntry/LogbookEntryModal.vue";
import LogbookEntrySubjectModal from "../logbookEntry/LogbookEntrySubjectModal.vue";

export default {
    name: "Logbook",
    components: {
        LogbookEntrySubjectModal,
        LogbookEntryModal,
        AbsenceModal,
        SubscribeObjectiveModal,
        // SetAchievementsModal,
        ContentModal,
        LmsModal,
        LogbookModal,
        LogbookPrintOptions,
        LogbookEntry,
        MediumModal,
        SubscribeModal,
        TaskModal,
    },
    props: {
        logbook: {
            default: null,
        },
        period: {
            default: null,
        },
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
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

        // entry events
        this.$eventHub.on('logbook-entry-added', (entry) => {
            this.entries.push(entry);
        });

        this.$eventHub.on('logbook-entry-updated', (updated) => {
            const index = this.entries.findIndex(entry => entry.id === updated.id);

            Object.assign(this.entries[index], updated);
        });

        this.$eventHub.on('logbook-entry-deleted', (deletedEntry) => {
            let index = this.entries.indexOf(deletedEntry);
            this.entries.splice(index, 1);
        });

        this.$eventHub.on('update-subject-badge', (data) => {
            let entry = this.entries.find(e => e.id === data.entry_id);

            entry.subject = data.subject;
        });

        // logbook events
        this.$eventHub.on('logbook-updated', (logbook) => {
            Object.assign(this.currentLogbook, logbook);
        });
    },
    methods: {
        openEntryModal() {
            this.globalStore?.showModal('logbook-entry-modal',{
                logbook_id: this.logbook.id,
            });
        },
        editLogbook(logbook) {
            this.globalStore?.showModal('logbook-modal', logbook);
        },
        togglePrintOptions() {
            this.showPrintOptions = !this.showPrintOptions;
        },
        share() {
            this.globalStore?.showModal('subscribe-modal',{
                modelId: this.logbook.id,
                modelUrl: 'logbook',
                shareWithUsers: true,
                shareWithGroups: true,
                shareWithOrganizations: true,
                shareWithToken: false,
                canEditCheckbox: true,
            });
        },
    },
}
</script>