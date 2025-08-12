<template>
    <div class="row">
        <div class="col-12 pt-2">
            <div class="card">
                <button v-if="create"
                    class="btn text-left p-0"
                    @click="openModal()"
                >
                    <div class="plan-entry card-header border-0">
                        <i class="fas fa-add pr-1"></i>
                        {{ trans('global.planEntry.create') }}
                    </div>
                </button>
                <div v-else>
                    <div
                        :id="'plan-entry-' + entry.id"
                        class="plan-entry"
                        :style="{ 'border-color': entry.color }"
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
                            <div
                                class="card-tools d-flex align-items-center mr-0"
                                style="height: 24px;"
                            >
                                <button v-if="entry.certificates"
                                    class="btn btn-icon"
                                    :title="trans('global.certificate.generate')"
                                    @click.stop="openCertificateModal()"
                                >
                                    <i class="fa fa-certificate link-muted"></i>
                                </button>
                                <span v-if="editable && showTools" class="d-flex">
                                    <button
                                        class="btn btn-icon ml-2"
                                        :title="trans('global.planEntry.edit')"
                                        @click.stop="openModal(entry)"
                                    >
                                        <i class="fa fa-pencil-alt link-muted"></i>
                                    </button>
                                    <button v-if="entry.owner_id == $userId
                                            || plan.owner_id == $userId
                                            || checkPermission('is_admin')
                                        "
                                        class="btn btn-icon text-danger ml-2"
                                        :title="trans('global.planEntry.delete')"
                                        @click.stop="openConfirm()"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </span>
                            </div>
                        </div>

                        <div class="card-body py-0 collapse">
                            <div class="py-2">
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

                                <Lms
                                    ref="LmsPlugin"
                                    :editable="editable && showTools"
                                    :referenceable_id="entry.id"
                                    referenceable_type="App\\PlanEntry"
                                />
                            </div>
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
import Lms from "../lms/Lms.vue";
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
        this.$refs.LmsPlugin?.loaderEvent();
    },
    methods: {
        openModal(entry = {}) {
            this.globalStore.showModal('plan-entry-modal', entry);
        },
        openCertificateModal() {
            this.globalStore.showModal('generate-certificate-modal', {
                certificates: this.entry.certificates,
                user_ids: this.$parent.$parent.users.map(user => user.id), // TODO: give user an option to select students
            });
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
        Lms,
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
.plan-entry {
    border-radius: 0.25rem;
    border-left-style: solid;
}
</style>