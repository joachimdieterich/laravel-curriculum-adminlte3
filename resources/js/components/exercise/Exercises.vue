<template >
    <div class="row">
        <div class="col-12 pt-2">
            <div class="card ">
                <div class="card-header d-flex justify-content-start">
                    <span style="width:30%;">{{ trans('global.exercise.title')}}</span>
                    <span>{{ trans('global.exercisedone.fields.iterations')}}</span>
                </div>

                <span v-for="exercise in exercises">
                    <div v-if="search.length < 3
                            || exercise?.title.toLowerCase().indexOf(search.toLowerCase()) !== -1"
                        :id="exercise?.id"
                        style="clear: right;"
                        :value="exercise.id"
                    >
                        <div class="card-footer d-flex justify-content-start">
                            <div style="width: 30%;">
                                <span v-if="exercise.description"
                                    @click="toggle(exercise.id)"
                                >
                                    <i v-if="toggle_id != exercise.id"
                                        class="fa fa-caret-right pr-2"
                                    ></i>
                                    <i v-else
                                        class="fa fa-caret-down pr-2"
                                    ></i>
                                </span>
                                {{ exercise.title }}
                                <span v-if="$userId == training.owner_id">
                                    <a
                                        class="text-secondary pointer mx-1"
                                        @click="openModal(exercise)"
                                    >
                                        <i class="fa fa-pencil-alt px-1"></i>
                                    </a>
                                    <a
                                        class="text-danger pointer mx-1"
                                        @click="confirmItemDelete(exercise)"
                                    >
                                    <i class="fa fa-trash px-1"></i>
                                    </a>
                                </span>
                            </div>

                            <exerciseDones :exercise="exercise"/>
                        </div>

                        <div v-if="toggle_id == exercise.id"
                            :id="'exercise_' + exercise.id"
                            class="card-body"
                            v-html="exercise.description"
                        ></div>
                    </div>
                </span>

                <div v-if="$userId == training.owner_id"
                    class="card-footer"
                    role="button"
                    @click="openModal()"
                >
                    <i class="fa fa-add"></i> {{ trans('global.exercise.create') }}
                </div>
            </div>
        </div>

        <Teleport to="body">
            <ExerciseModal/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.exercise.delete')"
                :description="trans('global.exercise.delete_helper')"
                @close="() => {
                    this.showConfirm = false;
                }"
                @confirm="() => {
                    this.showConfirm = false;
                    this.destroy();
                }"
            ></ConfirmModal>
        </Teleport>
    </div>
</template>
<script>
import ExerciseDones from "./ExerciseDones.vue";
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import ExerciseModal from "./ExerciseModal.vue";
import {useGlobalStore} from "../../store/global.js";

export default {
    props: {
        training: {
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
            component_id: this.$.uid,
            showConfirm: false,
            search: '',
            exercises: [],
            currentExercise: null,
            toggle_id: 0,
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.loaderEvent();

        this.$eventHub.on('exercise-added', (exercise) => {
            exercise.dones = [];
            this.exercises.push(exercise);
        });
        this.$eventHub.on('exercise-updated', (updatedExercise) => {
            Object.assign(this.exercises.find(e => e.id === updatedExercise.id), updatedExercise);
        });
        this.$eventHub.on('exercise_dones_added', (e) => {
            this.loaderEvent();
        });
    },
    methods: {
        loaderEvent() {
            axios.get('/exercises?training_id=' + this.training.id)
                .then(response => {
                    this.exercises = response.data;
                })
                .catch(e => {
                    console.log(e);
                });
        },
        openModal(exercise = { training_id: this.training.id }) {
            this.globalStore?.showModal('exercise-modal', exercise);
        },
        confirmItemDelete(exercise) {
            this.currentExercise = exercise;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/exercises/' + this.currentExercise.id)
                .then(response => {
                    if (response.data) this.exercises.splice(this.exercises.indexOf(this.currentExercise), 1);
                })
                .catch(e => {
                    console.log(e);
                });
        },
        toggle(id) {
            if (this.toggle_id != id) {
                this.toggle_id = id;
            } else {
                this.toggle_id = 0;
            }
        },
    },
    components: {
        ExerciseModal,
        ConfirmModal,
        ExerciseDones,
    },
}
</script>