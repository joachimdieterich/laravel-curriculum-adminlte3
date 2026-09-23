<template>
    <div class="d-flex flex-column px-3">
        <div
            class="position-sticky bg-white mb-0"
            style="top: 52px; z-index: 10; border-radius: 0px;"
        >
            <div class="d-flex align-items-center p-3 border-bottom">
                <div class="t-18">{{ currentPlan.title }}</div>
                <div v-if="editable || checkPermission('is_admin')"
                    v-permission="'plan_edit'"
                    class="d-print-none d-flex gap-2 pe-2 ms-auto"
                >
                    <button
                        class="btn btn-icon text-secondary"
                        :title="trans('global.plan.evaluate_user')"
                        :disabled="users.length === 0"
                        @click="openUserModal()"
                    >
                        <i class="fa fa-chart-simple"></i>
                    </button>
                    <!-- <button
                        class="btn btn-icon link-muted px-1"
                        :title="trans('global.plan.print')"
                        @click="window.print()"
                    >
                        <i class="fa fa-print"></i>
                    </button> -->

                    <span style="border-right: 1px solid black;"></span>

                    <Switch
                        id="plan-edit"
                        class="d-flex align-items-center gap-2"
                        label="global.edit"
                        v-model="showTools"
                    />
                </div>
            </div>
        </div>

        <div class="d-flex bg-white p-3 mb-3">
            <div class="overflow-auto" v-html="description"></div>
        </div>

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

        <PlanEntry v-if="editable && showTools"
            :plan="plan"
            :create="true"
        />

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
            <MediumModal/>
            <SubscribeModal/>
            <TrainingModal/>
            <LmsModal/>
            <GenerateCertificateModal/>
            <PlanEntryModal :plan="plan"/>
            <SelectUsersModal :users="users" :multiple="true"/>
            <SetAchievementsModal :users="users"/>
            <SubscribeObjectiveModal :users="users"/>
        </Teleport>
        <Teleport to="#customTitle">
            <div class="d-flex align-items-center">
                <small>{{ currentPlan.title }}</small>
                <button v-if="plan.owner_id == $userId || checkPermission('is_admin')"
                    type="button"
                    class="d-print-none btn btn-icon text-secondary mx-1"
                    data-bs-toggle="tooltip"
                    :data-bs-title="trans('global.plan.edit')"
                    @click="editPlan()"
                >
                    <i class="fa fa-pencil-alt"></i>
                </button>
                <button v-if="plan.owner_id == $userId || checkPermission('is_admin')"
                    type="button"
                    class="d-print-none btn btn-icon text-secondary mx-1"
                    data-bs-toggle="tooltip"
                    :data-bs-title="trans('global.share')"
                    @click="share()"
                >
                    <i class="fa fa-share-alt"></i>
                </button>
            </div>
        </Teleport>
    </div>
</template>
<script>
import Switch from "../forms/Switch.vue";
import draggable from "vuedraggable";
import PlanModal from "./PlanModal.vue";
import PlanEntry from './PlanEntry.vue';
import PlanEntryModal from "./PlanEntryModal.vue";
import MediumModal from "../media/MediumModal.vue";
import GenerateCertificateModal from "../certificate/GenerateCertificateModal.vue";
import SelectUsersModal from "../user/SelectUsersModal.vue";
import SubscribeObjectiveModal from "../objectives/SubscribeObjectiveModal.vue";
import TrainingModal from "../training/TrainingModal.vue";
import LmsModal from "../lms/LmsModal.vue";
import SetAchievementsModal from "./SetAchievementsModal.vue";
import SubscribeModal from "../subscription/SubscribeModal.vue";

export default {
    components: {
        Switch,
        PlanModal,
        PlanEntry,
        PlanEntryModal,
        MediumModal,
        GenerateCertificateModal,
        SelectUsersModal,
        SubscribeObjectiveModal,
        TrainingModal,
        LmsModal,
        SetAchievementsModal,
        SubscribeModal,
        draggable,
    },
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
            default: null,
        },
    },
    data() {
        return {
            currentPlan: this.plan,
            entries: [],
            entry_order: [],
            subscriptions: {},
            search: '',
            showTools: false,
        }
    },
    methods: {
        loaderEvent() {
            axios.get('/planEntries?plan_id=' + this.plan.id)
                .then(response => {
                    if (this.plan.entry_order != null) {
                        this.entry_order = this.plan.entry_order;
                        // rearrange entries (by their ID) to the specified order
                        this.entries = this.entry_order.map(
                            id => response.data.entries.find(entry => entry.id === id)
                        );
                    } else {
                        this.entries = response.data.entries;
                    }
                    // assign each certificate to its associated entry
                    for (const entry_id in response.data.certificates) {
                        let entry = this.entries.find(e => e.id == entry_id);
                        entry.certificates = response.data.certificates[entry_id];
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
            this.globalStore.showModal('subscribe-modal', {
                modelId: this.plan.id,
                modelUrl: 'plan',
                shareWithUsers: true,
                shareWithGroups: true,
                shareWithOrganizations: true,
                shareWithToken: false,
                canEditCheckbox: true,
            });
        },
        openUserModal() {
            this.globalStore.showModal('select-users-modal');
        },
        handleEntryOrder(e) {
            if (e.newIndex === e.oldIndex) return;

            this.entry_order = this.entries.map(entry => entry.id);
            this.updateEntryOrder();
        },
        updateEntryOrder() {
            // send the current order of entries to the server
            axios.put("/plans/" + this.plan.id + "/syncEntriesOrder", { entry_order: this.entry_order })
                .catch(e => {
                    console.log(e);
                    this.toast.error(this.errorMessage(e));
                });
        },
    },
    mounted() {
        this.enableTooltips();

        this.loaderEvent();

        this.$eventHub.on('plan-updated', updatedPlan => {
            Object.assign(this.currentPlan, updatedPlan);
        });

        this.$eventHub.on('users-selected', users => {
            window.open('/plans/' + this.plan.id + '/getUserAchievements/' + users.map(u => u.id));
        });

        // ENTRY events
        this.$eventHub.on('planEntry-added', entry => {
            this.entries.push(entry);
            this.entry_order.push(entry.id);
            this.updateEntryOrder();
        });
        this.$eventHub.on('planEntry-updated', updatedEntry => {
            let entry = this.entries.find(e => e.id === updatedEntry.id);
            Object.assign(entry, updatedEntry);
        });
        this.$eventHub.on('plan-entry-deleted', entry => {
            let index = this.entries.indexOf(entry);
            this.entries.splice(index, 1);
            this.entry_order.splice(index, 1);
            this.updateEntryOrder();
        });
    },
    computed: {
        // add an img-tag, so the medium can be placed within the text
        description() {
            let description = this.currentPlan.description;
            if (this.currentPlan.medium_id) { // prepend img-tag
                description = '<img class="pull-right" style="max-width: 25%;" src="/media/' + this.currentPlan.medium_id + '?preview=true"/>' + description;
            }

            if (description.trim().length === 0) description = this.trans('global.no_description');

            return description;
        },
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
}
</script>