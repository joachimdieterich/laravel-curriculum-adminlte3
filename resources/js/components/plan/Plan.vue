<template>
    <div>
        <div class="card pb-3">
            <div class="card-header">
                <div class="card-title">{{ currentPlan.title }}</div>
                <div v-if="$userId == plan.owner_id"
                    v-can="'plan_edit'"
                    class="card-tools pr-2 no-print"
                >
                    <a onclick="window.print()" class="link-muted mr-3 px-1 pointer">
                        <i class="fa fa-print"></i>
                    </a>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <span class="col-12">
                        {{ htmlToText(currentPlan.description) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 pt-2">
                <draggable
                    v-model="entries"
                    v-bind="columnDragOptions"
                    :disabled="this.disabled"
                    @start="drag=true"
                    @end="handleEntryOrder"
                    itemKey="id"
                >
                    <template #item="{ element: entry , index }">
                        <PlanEntry
                            :key="entry.id"
                            :editable="editable"
                            :entry="entry"
                            :plan="plan"
                        ></PlanEntry>
                    </template>
                </draggable>
            </div>

            <div class="col-12">
                <!--<Calendar></Calendar>-->
                <PlanEntry v-if="$userId == plan.owner_id"
                    :plan="plan"
                    create="true"
                ></PlanEntry>
            </div>
        </div>
        <!-- overlay button in bottom right corner -->
        <!-- <div
            id="corner-button"
            class="position-sticky d-flex justify-content-center align-items-center float-right mb-3"
            role="button"
            @click="open()"
        >
            <i class="fa fa-users"></i>
        </div> -->
        <Teleport to="body">
            <PlanModal/>
            <PlanEntryModal :plan="plan"/>
            <SetAchievementsModal
                :users="users"
            ></SetAchievementsModal>
            <SubscribeModal/>
            <SubscribeObjectiveModal/>
        </Teleport>
        <Teleport v-if="$userId == plan.owner_id"
            to="#customTitle"
        >
            <small>{{ currentPlan.title }}</small>
            <a
                class="btn btn-flat"
                @click="editPlan()"
            >
                <i class="fa fa-pencil-alt text-secondary"></i>
            </a>
            <button
                class="btn btn-fla"
                @click="share()"
            >
                <i class="fa fa-share-alt text-secondary"></i>
            </button>
        </Teleport>
    </div>
</template>

<script>
import draggable from "vuedraggable";
import SetAchievementsModal from "./SetAchievementsModal.vue";
import PlanEntry from './PlanEntry.vue';
import PlanEntryModal from "./PlanEntryModal.vue";
import PlanModal from "./PlanModal.vue";
import SubscribeModal from "../subscription/SubscribeModal.vue";
import SubscribeObjectiveModal from "../objectives/SubscribeObjectiveModal.vue";
import {useGlobalStore} from "../../store/global";

export default {
    props: {
        plan: {
            type: Object
        },
        editable: {
            type: Boolean,
            default: false,
        },
        users: {
            type: Object,
            default: null
        }
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            currentPlan: this.plan,
            entries: [],
            entry_order: [],
            subscriptions: {},
            search: '',
            disabled: false,
            errors: {},
        }
    },
    methods: {
        loaderEvent(){
            axios.get('/planEntries?plan_id=' + this.plan.id)
                .then(response => {
                    if (this.plan.entry_order != null) {
                        this.entry_order = this.plan.entry_order;
                        // rearrange entries to the specified order by their ID
                        // since this is O[n^2], it could become a performance issue in the future
                        this.entries = this.entry_order.map(
                            id => response.data.entries.find(entry => entry.id === id)
                        );
                    } else {
                        this.entries = response.data.entries;
                    }
                })
                .catch(e => {
                    console.log(e);
                });
        },
        editPlan() {
            this.globalStore.showModal('plan-modal', this.currentPlan);
        },
        share() {
            this.globalStore.showModal('subscribe-modal',
                {
                    'modelId': this.plan.id,
                    'modelUrl': 'plan',
                    'shareWithUsers': true,
                    'shareWithGroups': true,
                    'shareWithOrganizations': true,
                    'shareWithToken': false,
                    'canEditCheckbox': true,
                });
        },
        handleEntryAdded(entry) {
            this.entries.push(entry);
            this.entry_order.push(entry.id);
            this.updateEntryOrder();
        },
        handleEntryUpdated(updatedEntry) {
            let entry = this.entries.find(e => e.id === updatedEntry.id);
            Object.assign(entry, updatedEntry);
        },
        handleEntryDeleted(entry) {
            let index = this.entries.indexOf(entry);
            this.entries.splice(index, 1);
            this.entry_order.splice(index, 1);
            this.updateEntryOrder();
        },
        handleEntryOrder(e) {
            if (e.newIndex === e.oldIndex) return;
            this.entry_order = this.entries.map(entry => entry.id);
            this.updateEntryOrder();
        },
        updateEntryOrder() {
            // Send the current order of entries to the server
            axios.put("/plans/" + this.plan.id + "/syncEntriesOrder", {entry_order: this.entry_order})
                .catch(err => {
                    console.log(err.response);
                    alert(err.response.statusText);
                });
        },
    },
    mounted() {
        localStorage.removeItem('user-datatable-selection'); // reset selection to prevent wrong inputs
        this.disabled = this.$userId != this.plan.owner_id;
        this.loaderEvent();

        this.$eventHub.on('plan-updated', (e) => {
            this.currentPlan = e;
        });
        // ENTRY events
        this.$eventHub.on('plan-entry-added', (e) => {
            this.handleEntryAdded(e);
        });
        this.$eventHub.on('plan-entry-updated', (e) => {
            this.handleEntryUpdated(e);
        });
        this.$eventHub.on('plan-entry-deleted', (e) => {
            this.handleEntryDeleted(e);
        });
    },
    computed: {
        columnDragOptions() {
            return {
                animation: 200,
                // checks if a mobile-browser is used and if true, add delay
                ...(/Mobi/i.test(window.navigator.userAgent) && {delay: 200}),
                group: "columns",
                dragClass: "status-drag",
                fallbackTolerance: 5,
                disabled: !this.editable
            };
        },
    },
    components: {
        PlanModal,
        PlanEntry,
        PlanEntryModal,
        SetAchievementsModal,
        SubscribeModal,
        SubscribeObjectiveModal,
        draggable,
    },
}
</script>
