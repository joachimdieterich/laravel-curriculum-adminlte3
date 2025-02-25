<template>
    <div>
        <div
            class="card position-sticky mb-0"
            style="top: 3.5rem; z-index: 10; border-radius: 0px;"
        >
            <div class="card-header d-flex align-items-center">
                <div class="card-title">{{ currentPlan.title }}</div>
                <div v-if="$userId == plan.owner_id"
                    v-permission="'plan_edit'"
                    class="card-tools d-flex pr-2 ml-auto no-print"
                    style="gap: 5px;"
                >
                    <a onclick="window.print()" class="link-muted px-1 pointer">
                        <i class="fa fa-print"></i>
                    </a>
                    <span class="pr-2 mr-2" style="border-right: 1px solid black;"></span>
                    <span
                        class="link-muted pointer"
                        @click.prevent="showTools = !showTools"
                    >
                        <span class="pr-2">{{ trans('global.edit') }}</span>
                        <span class="custom-switch custom-switch-on-green">
                            <input
                                id="edit_toggle"
                                type="checkbox"
                                class="custom-control-input"
                                v-model="showTools"
                            />
                            <label
                                for="edit_toggle"
                                class="custom-control-label"
                            ></label>
                        </span>
                    </span>
                </div>
            </div>
        </div>
        <div class="card rounded-0">
            <!-- /.card-header -->
            <div class="card-body">
                <div class="d-flex">
                    <span class="flex-fill">
                        {{ htmlToText(currentPlan.description) }}
                    </span>
                    <div v-if="currentPlan.medium_id"
                        class="w-25"
                        :style="{'background': 'url(/media/' + currentPlan.medium_id + '?model=App\\Plan&model_id=' + currentPlan.id + ') center no-repeat'}"
                        style="min-height: 100px; background-size: contain !important;"
                    ></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 pt-2">
                <draggable
                    v-model="entries"
                    v-bind="columnDragOptions"
                    :disabled="!editable"
                    itemKey="id"
                    @start="drag=true"
                    @end="handleEntryOrder"
                >
                    <template #item="{ element: entry }">
                        <PlanEntry
                            :key="entry.id"
                            :entry="entry"
                            :plan="plan"
                            :editable="editable"
                            :showTools="showTools"
                        />
                    </template>
                </draggable>
            </div>

            <div class="col-12">
                <PlanEntry v-if="editable && showTools"
                    :plan="plan"
                    :create="true"
                />
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
            <MediumModal/>
            <SubscribeObjectiveModal/>
            <TrainingModal :plan="plan"/>
            <SetAchievementsModal :users="users"/>
            <SubscribeModal/>
        </Teleport>
        <Teleport to="#customTitle">
            <small>{{ currentPlan.title }}</small>
            <a v-if="plan.owner_id == $userId || checkPermission('is_admin')"
                class="btn btn-flat"
                @click="editPlan()"
            >
                <i class="fa fa-pencil-alt text-secondary"></i>
            </a>
            <button v-if="plan.owner_id == $userId || checkPermission('is_admin')"
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
import PlanModal from "./PlanModal.vue";
import PlanEntry from './PlanEntry.vue';
import PlanEntryModal from "./PlanEntryModal.vue";
import MediumModal from "../media/MediumModal.vue";
import SubscribeObjectiveModal from "../objectives/SubscribeObjectiveModal.vue";
import TrainingModal from "../training/TrainingModal.vue";
import SetAchievementsModal from "./SetAchievementsModal.vue";
import SubscribeModal from "../subscription/SubscribeModal.vue";
import {useGlobalStore} from "../../store/global";

export default {
    props: {
        plan: {
            type: Object,
            default: null,
        },
        editable: {
            type: Boolean,
            default: false,
        },
        users: {
            type: Object,
            default: null
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
            currentPlan: this.plan,
            entries: [],
            entry_order: [],
            subscriptions: {},
            search: '',
            showTools: false,
            errors: {},
        }
    },
    methods: {
        loaderEvent() {
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
                    modelId: this.plan.id,
                    modelUrl: 'plan',
                    shareWithUsers: true,
                    shareWithGroups: true,
                    shareWithOrganizations: true,
                    shareWithToken: false,
                    canEditCheckbox: true,
                });
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
        this.loaderEvent();

        this.$eventHub.on('plan-updated', (updatedPlan) => {
            Object.assign(this.currentPlan, updatedPlan);
        });
        // ENTRY events
        this.$eventHub.on('plan-entry-added', (entry) => {
            this.entries.push(entry);
            this.entry_order.push(entry.id);
            this.updateEntryOrder();
        });
        this.$eventHub.on('plan-entry-updated', (updatedEntry) => {
            let entry = this.entries.find(e => e.id === updatedEntry.id);
            Object.assign(entry, updatedEntry);
        });
        this.$eventHub.on('plan-entry-deleted', (entry) => {
            let index = this.entries.indexOf(entry);
            this.entries.splice(index, 1);
            this.entry_order.splice(index, 1);
            this.updateEntryOrder();
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
        MediumModal,
        SubscribeObjectiveModal,
        TrainingModal,
        SetAchievementsModal,
        SubscribeModal,
        draggable,
    },
}
</script>