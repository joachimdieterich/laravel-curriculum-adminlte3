<template>
    <div class="d-flex flex-column">
        <div class="card">
            <div class="card-header d-flex justify-content-start">
                <span style="width:30%;">{{ trans('global.exercise.title')}}</span>
                <span>{{ trans('global.exercisedone.fields.iterations')}}</span>
            </div>

            <span v-for="exercise in exercises">
                <div v-if="search.length < 3
                        || exercise?.title.toLowerCase().indexOf(search.toLowerCase()) !== -1"
                    :id="exercise?.id"
                >
                    <div class="card-footer d-flex justify-content-start">
                        <div
                            class="d-flex align-items-center gap-2"
                            style="width: 30%;"
                        >
                            <span class="d-flex align-items-center">
                                <button v-if="exercise.description"
                                    type="button"
                                    class="btn btn-icon"
                                    @click="toggle(exercise.id)"
                                >
                                    <i
                                        class="fa"
                                        :class="toggle_id == exercise.id ? 'fa-caret-down' : 'fa-caret-right'"
                                    ></i>
                                </button>
                                {{ exercise.title }}
                            </span>
                            <span v-if="ownerOrAdmin" class="d-contents">
                                <button
                                    type="button"
                                    class="btn btn-icon text-secondary"
                                    @click="openModal(exercise)"
                                >
                                    <i class="fa fa-pencil-alt"></i>
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-icon text-danger"
                                    @click="confirmItemDelete(exercise)"
                                >
                                    <i class="fa fa-trash"></i>
                                </button>
                            </span>
                        </div>

                        <ExerciseDones :exercise="exercise"/>
                    </div>

                    <div v-if="toggle_id == exercise.id"
                        class="card-body"
                        v-html="exercise.description"
                    ></div>
                </div>
            </span>

            <div v-if="ownerOrAdmin"
                class="card-footer"
                role="button"
                @click="openModal()"
            >
                <i class="fa fa-add"></i> {{ trans('global.exercise.create') }}
            </div>
        </div>

        <Teleport to="body">
            <ExerciseModal/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.exercise.delete')"
                :description="trans('global.exercise.delete_helper')"
                @close="showConfirm = false"
                @confirm="destroy()"
            />
        </Teleport>
    </div>
</template>
<script>
import ExerciseDones from "./ExerciseDones.vue";
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import ExerciseModal from "./ExerciseModal.vue";

export default {
    components: {
        ExerciseModal,
        ConfirmModal,
        ExerciseDones,
    },
    props: {
        training: {
            type: Object,
            default: null,
        },
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

        this.$eventHub.on('exercise-added', exercise => {
            exercise.dones = [];
            this.exercises.push(exercise);
        });
        this.$eventHub.on('exercise-updated', updatedExercise => {
            Object.assign(this.exercises.find(e => e.id === updatedExercise.id), updatedExercise);
        });
        this.$eventHub.on('exercise_dones_added', () => this.loaderEvent());

        this.$eventHub.on('filter', filter => this.search = filter.searchString);
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
            this.globalStore.showModal('exercise-modal', exercise);
        },
        confirmItemDelete(exercise) {
            this.currentExercise = exercise;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/exercises/' + this.currentExercise.id)
                .then(response => {
                    if (response.data) {
                        this.showConfirm = false;
                        this.exercises.splice(this.exercises.indexOf(this.currentExercise), 1);
                    }
                })
                .catch(e => {
                    console.log(e);
                    this.showConfirm = false;
                    this.toast.error(this.errorMessage(e));
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
    computed: {
        ownerOrAdmin() {
            return this.training.owner_id == this.$userId || this.checkPermission('is_admin');
        },
    },
}
</script>