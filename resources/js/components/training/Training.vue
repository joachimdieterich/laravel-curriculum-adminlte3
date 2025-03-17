<template>
    <div>
        <div class="card">
            <div class="card-header">
               <!-- <div class="card-tools pr-2 s">
                    <i class="fa fa-pencil-alt"></i>
                </div> -->
                <div class="user-block">
                    <span class="username ml-0">
                        {{ currentTraining.title }}
                        <span class="description ml-0">
                            {{ this.timePeriod }}
                        </span>
                    </span>
                </div>
            </div>
            <div class="card-body" v-html="currentTraining.description"></div>

            <!-- <div class="card-footer">
                <CalendarCreateEvent/>
            </div> -->
        </div>
        <Exercises :training="training"/>

        <Teleport to="body">
            <TrainingModal/>
            <MediumModal/>
        </Teleport>
        <Teleport to="#customTitle">
            <small>{{ trans('global.training.title_singular') }}</small>
            <a v-if="training.owner_id == $userId || checkPermission('is_admin')"
                class="btn btn-flat text-secondary px-2 mx-1"
                @click="editTraining()"
            >
                <i class="fa fa-pencil-alt"></i>
            </a>
        </Teleport>
    </div>
</template>
<script>
import TrainingModal from "./TrainingModal.vue";
import MediumModal from "../media/MediumModal.vue";
import Exercises from "../exercise/Exercises.vue";
import CalendarCreateEvent from "../calendar/CalendarCreateEvent.vue";
import {useGlobalStore} from "../../store/global";

export default {
    props: {
        training: {
            type: Object,
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
            currentTraining: {},
            timePeriod: '',
        }
    },
    mounted() {
        // TODO: improve this
        if (this.training.begin != null && this.training.end != null) this.postDate();

        this.currentTraining = this.training;

        this.$eventHub.on('training-updated', (data) => {
            Object.assign(this.currentTraining, data.training);
        })
    },
    methods: {
        editTraining() {
            this.globalStore.showModal('training-modal', this.training);
        },
        postDate() {
            const start = new Date(this.training.begin.replace(/-/g, "/"));
            const end = new Date(this.training.end.replace(/-/g, "/"));
            const dateFormat = {
                weekday: 'short',
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
              /*  hour: '2-digit',
                minute: '2-digit'*/
            };

            if (start.toDateString() === end.toDateString()) {
                this.timePeriod = start.toLocaleString([], dateFormat) + " - " + end.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } else {
                this.timePeriod = start.toLocaleString([], dateFormat) + " - " + end.toLocaleString([], dateFormat);
            }
        },
    },
    components: {
        TrainingModal,
        MediumModal,
        CalendarCreateEvent,
        Exercises,
    },
}
</script>