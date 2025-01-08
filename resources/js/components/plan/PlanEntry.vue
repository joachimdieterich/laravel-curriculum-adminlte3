<template>
    <div class="row">
        <div class="col-12 pt-2">
            <div class="card">
                <div v-if="create">
                    <div 
                        class="card-header pointer"
                        @click="openModal()"
                    >
                        <i class="fas fa-add pr-1"></i>
                        {{ trans('global.planEntry.create') }}
                    </div>
                </div>
                <div v-else>
                    <div
                        :id="'plan-entry-' + entry.id"
                        :style="{ 'border-left-style': 'solid', 'border-radius': '0.25rem', 'border-color': entry.color }"
                    >
                        <div
                            class="card-header collapsed"
                            data-toggle="collapse"
                            :data-target="'#plan-entry-' + entry.id + ' > .card-body'"
                            aria-expanded="false"
                        >
                            <i
                                class="mr-1"
                                :class="entry.css_icon"
                            ></i>
                            {{ entry.title }}
                            <i class="fa fa-angle-up"></i>
                            <div v-if="$userId == plan.owner_id"
                                class="card-tools"
                            >
                                <i
                                    class="fa fa-pencil-alt pointer link-muted mr-3"
                                    @click.stop="openModal(entry)"
                                ></i>
                                <i
                                    class="fas fa-trash pointer text-danger mr-1"
                                    @click.stop="openConfirm()"
                                ></i>
                            </div>
                        </div>
                        <div class="card-body py-2 collapse">
                            <img v-if="Number.isInteger(entry.medium_id)"
                                class="pull-right"
                                :src="'/media/' + entry.medium_id + '/thumb'"
                            />
                            <span v-dompurify-html="entry.description"></span>

                            <objectives
                                referenceable_type="App\PlanEntry"
                                :referenceable_id="entry.id"
                                :owner_id="entry.owner_id"
                                :editable="editable"
                            />

                            <Trainings
                                :plan="plan"
                                subscribable_type="App\PlanEntry"
                                :subscribable_id="entry.id"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Teleport to="body">
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.planEntry.delete')"
                :description="trans('global.planEntry.delete_helper')"
                @close="() => {
                    this.showConfirm = false;
                }"
                @confirm="() => {
                    this.showConfirm = false;
                    this.delete(this.entry);
                }"
            />
        </Teleport>
    </div>
</template>
<script>
import Objectives from "../objectives/Objectives.vue";
import Trainings from '../training/Trainings.vue';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import {useGlobalStore} from "../../store/global";

export default {
    props: {
        entry: {
            default: null,
        },
        create: {
            default: false,
        },
        plan: {
            type: Object,
        },
        editable: {
            default: false,
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
            component_id: this.$.uid,
            showConfirm: false,
        }
    },
    mounted() {
        // Set eventlistener for Media
        this.$eventHub.on('addMedia', (e) => {
            if (this.component_id == e.id) {
                this.form.medium_id = e.selectedMediumId;
                if (Array.isArray(this.form.medium_id))  {
                    this.form.medium_id = this.form.medium_id[0]; //Hack to get existing files working.
                }
            }
        });
    },
    methods: {
        openModal(entry = {}) {
            this.globalStore.showModal('plan-entry-modal', entry);
        },
        openConfirm() {
            this.showConfirm = true;
        },
        delete(entry) {
            axios.delete('/planEntries/' + entry.id)
                .then(response => {
                    this.$eventHub.emit("plan-entry-deleted", entry);
                })
                .catch(e => {
                    console.log(e);
                });
        },
    },
    components: {
        Objectives,
        Trainings,
        ConfirmModal,
    },
}
</script>
<style scoped>
.card-header:hover {
    background-color: #e9ecef;
    cursor: pointer;
}
.card-header .fa-angle-up {
    transition: 0.3s transform;
}
.card-header.collapsed .fa-angle-up {
    transform: rotate(-180deg);
}
</style>
