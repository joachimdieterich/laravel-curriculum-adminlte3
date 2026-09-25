<template>
    <div>
        <button v-if="entry === null"
            type="button"
            class="btn btn-outline-dark text-left p-0"
            @click="openModal()"
        >
            <div class="plan-entry card-header border-0">
                <i class="fas fa-add pe-1"></i>
                {{ trans('global.planEntry.create') }}
            </div>
        </button>
        <div v-else
            class="plan-entry-wrapper rounded-1 shadow-layout"
            :style="{ 'border-color': entry.color }"
        >
            <div
                :id="'plan-entry-' + entry.id"
                class="position-relative bg-white rounded-end-1"
            >
                <div
                    class="plan-entry-header d-flex align-items-center gap-2 px-3 rounded-end-1 pointer collapsed"
                    style="height: 3rem"
                    data-bs-toggle="collapse"
                    :data-bs-target="'#plan-entry-' + entry.id + ' > .plan-entry-body'"
                    aria-expanded="false"
                >
                    <i :class="entry.css_icon"></i>
                    {{ entry.title }}
                    <i class="fa fa-angle-up"></i>
                </div>
    
                <div class="plan-entry-tools position-absolute d-flex align-items-center gap-2 ms-auto me-0">
                    <button v-if="entry.certificates"
                        type="button"
                        class="btn btn-icon"
                        :title="trans('global.certificate.generate')"
                        @click="openCertificateModal()"
                    >
                        <i class="fa fa-certificate link-muted"></i>
                    </button>
                    <span v-if="editable && showTools" style="display: contents;">
                        <button
                            type="button"
                            class="btn btn-icon"
                            :title="trans('global.planEntry.edit')"
                            @click="openModal(entry)"
                        >
                            <i class="fa fa-pencil-alt link-muted"></i>
                        </button>
                        <button v-if="entry.owner_id == $userId
                                || plan.owner_id == $userId
                                || checkPermission('is_admin')
                            "
                            class="btn btn-icon text-danger"
                            :title="trans('global.planEntry.delete')"
                            @click="openConfirm()"
                        >
                            <i class="fas fa-trash"></i>
                        </button>
                    </span>
                </div>
    
                <div class="plan-entry-body border-top collapse">
                    <div class="p-3">
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

        <Teleport to="body">
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.planEntry.delete')"
                :description="trans('global.planEntry.delete_helper')"
                @close="showConfirm = false"
                @confirm="() => {
                    showConfirm = false;
                    destroy(entry);
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

export default {
    components: {
        Objectives,
        Trainings,
        Lms,
        ConfirmModal,
    },
    props: {
        entry: {
            type: Object,
            default: null,
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
        },
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
        openModal() {
            this.globalStore.showModal('plan-entry-modal', this.entry ?? {});
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
        destroy(entry) {
            axios.delete('/planEntries/' + entry.id)
                .then(response => {
                    this.$eventHub.emit("plan-entry-deleted", entry);
                })
                .catch(e => {
                    console.log(e);
                    this.toast.error(this.errorMessage(e))
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
    watch: {
        'entry.description': function() {
            this.$nextTick(() => {
                MathJax.typeset(); // MathJax needs to be re-rendered after the description changes
            });
        },
    },
}
</script>