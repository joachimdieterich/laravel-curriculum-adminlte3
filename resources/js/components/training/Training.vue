<template>
    <div class="d-flex flex-column px-3">
        <div class="card">
            <div class="card-header">
                <div class="user-block">
                    <span class="username ms-0">
                        {{ currentTraining.title }}
                        <span class="description ms-0">
                            {{ training.begin }} - {{ training.end }}
                        </span>
                    </span>
                </div>
            </div>
            <div class="card-body" v-html="currentTraining.description"></div>
        </div>

        <Exercises :training="training"/>

        <Teleport to="body">
            <TrainingModal/>
            <MediumModal/>
        </Teleport>
        <Teleport to="#customTitle">
            <div class="d-flex align-items-center gap-2">
                <small>{{ trans('global.training.title_singular') }}</small>
                <button v-if="training.owner_id == $userId || checkPermission('is_admin')"
                    type="button"
                    class="btn btn-icon text-secondary"
                    data-bs-toggle="tooltip"
                    :data-bs-title="trans('global.training.edit')"
                    @click="editTraining()"
                >
                    <i class="fa fa-pencil-alt"></i>
                </button>
            </div>
        </Teleport>
    </div>
</template>
<script>
import TrainingModal from "./TrainingModal.vue";
import MediumModal from "../media/MediumModal.vue";
import Exercises from "../exercise/Exercises.vue";
import CalendarCreateEvent from "../calendar/CalendarCreateEvent.vue";

export default {
    components: {
        TrainingModal,
        MediumModal,
        CalendarCreateEvent,
        Exercises,
    },
    props: {
        training: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            currentTraining: {},
        }
    },
    mounted() {
        this.enableTooltips();

        this.currentTraining = this.training;

        this.$eventHub.on('training-updated', data => {
            Object.assign(this.currentTraining, data.training);
        })
    },
    methods: {
        editTraining() {
            this.globalStore.showModal('training-modal', this.training);
        },
    },
}
</script>