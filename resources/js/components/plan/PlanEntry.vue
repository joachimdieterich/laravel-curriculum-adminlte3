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
                            <div v-if="editable && showTools"
                                class="card-tools mr-0"
                            >
                                <i
                                    class="fa fa-pencil-alt link-muted pointer p-1"
                                    @click.stop="openModal(entry)"
                                ></i>
                                <a v-if="entry.owner_id == $userId
                                        || plan.owner_id == $userId
                                        || checkPermission('is_admin')
                                    "
                                    class="text-danger ml-2"
                                >
                                    <i
                                        class="fas fa-trash pointer p-1"
                                        @click.stop="openConfirm()"
                                    ></i>
                                </a>
                            </div>
                        </div>

                        <div class="card-body py-2 collapse">
                            <div class="overflow-auto" v-html="description"></div>

                            <Objectives
                                referenceable_type="App\PlanEntry"
                                :referenceable_id="entry.id"
                                :owner_id="entry.owner_id"
                                :editable="editable"
                                :showTools="showTools"
                            />

                            <Trainings
                                :subscribable_id="entry.id"
                                subscribable_type="App\PlanEntry"
                                :editable="editable"
                                :deletable="entry.owner_id == $userId || plan.owner_id == $userId"
                                :showTools="showTools"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Teleport to="body">
            <ConfirmModal
                :showConfirm="showConfirm"
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
            type: Object,
            default: null,
        },
        create: {
            type: Boolean,
            default: false,
        },
        plan: {
            type: Object,
            default: null,
        },
        editable: {
            type: Boolean,
            default: false,
        },
        showTools: {
            type: Boolean,
            default: false,
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
    computed: {
        // add an img-tag, so the medium can be placed within the text
        description() {
            let img = '';
            if (this.entry.medium_id) {
                img = '<img class="pull-right" style="max-width: 25%;" src="/media/' + this.entry.medium_id + '?preview=true"/>';
            }
            return img + this.entry.description;
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
