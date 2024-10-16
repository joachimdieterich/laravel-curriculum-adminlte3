<template >
    <div class="row ">
        <div class="col-12 pt-2">
            <div class="card ">
                <div class="card-header"
                     style="display: flex;justify-content: flex-start;">
                    <span style="width:30%;">{{ trans('global.exercise.title')}}</span>
                    <span>{{ trans('global.exercisedone.fields.iterations')}}</span>
                </div>

                <span v-if="exercises.length > 0 "
                      v-for="(exercise,index) in exercises">
                    <div style="clear:right;"
                         v-if="(exercise?.title.toLowerCase().indexOf(search.toLowerCase()) !== -1) || search.length < 3"
                         :id="exercise?.id"
                         v-bind:value="exercise.id">
                            <div class="card-footer "
                                 style="display: flex;justify-content: flex-start;">
                                <div style="width:30%;">
                                    <span v-if="exercise.description"
                                          @click="toggle('exercise_'+exercise.id)">
                                        <i  v-if="toggle_id != 'exercise_' + exercise.id"
                                            class="fa fa-caret-right pr-2"
                                        ></i>
                                        <i v-else
                                           class="fa fa-caret-down pr-2"></i>
                                    </span>
                                    {{ exercise.title }}
                                    <span v-if="$userId == training.owner_id">
                                          <i class="pl-2 fa fa-pencil-alt text-secondary pointer"
                                             @click="editExercise(exercise)"></i>
                                    <i class="pl-2 fa fa-trash text-danger"
                                       @click="destroy(exercise)"></i>
                                    </span>
                                </div>

                                <exerciseDones
                                    :exercise="exercise">
                                </exerciseDones>
                            </div>

                        <div v-if="toggle_id == 'exercise_'+exercise.id"
                             :id="'exercise_'+exercise.id"
                             class="card-body"
                             v-dompurify-html="exercise.description">
                        </div>
                    </div>
                </span>

                <div class="card-footer" role="button"
                     v-if="!editor &&  $userId == training.owner_id"
                     @click="create()">
                    <i class="fa fa-add"></i> {{ trans('global.exercise.create') }}
                </div>

            </div>
        </div>

        <Teleport to="body">
            <ExerciseModal></ExerciseModal>
            <ConfirmModal
                :showConfirm="this.showConfirm"
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
import Form from "form-backend-validation";
import ExerciseDones from "./ExerciseDones.vue";
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import ExerciseModal from "./ExerciseModal.vue";
import {useGlobalStore} from "../../store/global.js";

export default {
    props: {
        training: {
            default: null
        },
    },
    setup () {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            requestUrl: '/exercises',
            showConfirm: false,
            form: new Form({
                'id': null,
                'training_id':'',
                'title': '',
                'description': '',
                'recommended_iterations': '',
                'order_id': 0,
            }),
            search: '',
            editor: false,
            exercises: [],
            currentExercise: {},
            toggle_id:'',
            errors: {}

        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('exercise-added', (exercise) => {
            this.globalStore?.closeModal('exercise-modal');
            this.exercises.push(exercise);
        });

        this.$eventHub.on('exercise-updated', (exercise) => {
            this.globalStore?.closeModal('exercise-modal');
            this.update(map);
        });

        this.$initTinyMCE(
            [
                "autolink link"
            ],
            {
                'eventHubCallbackFunction': 'insertContent',
                'eventHubCallbackFunctionParams': this.component_id,
            }
        );
        this.$eventHub.on('exercise_dones_added', (e) => {
            this.loaderEvent();
        });
    },
    methods: {
        loaderEvent(){
            axios.get('/exercises?training_id=' + this.training.id)
                .then(response => {
                    this.exercises = response.data.exercises;
                })
                .catch(e => {
                    console.log(e);
                });
        },
        create(){
            this.globalStore?.showModal('exercise-modal', {
                'training_id': this.training.id
            });
        },
        editExercise(exercise){
            this.globalStore?.showModal('exercise-modal', exercise);
        },
        confirmItemDelete(exercise){
            this.currentExercise = exercise;
            this.showConfirm = true;
        },
        destroy(exercise){
            axios.delete('/exercises/'+exercise.id)
                .then(response => {
                    this.loaderEvent();
                })
                .catch(e => {
                    console.log(e);
                });
        },
        submit() {
            if (!this.validate()) return;

            const method = this.method.toLowerCase();
            this.form.description = tinyMCE.get('description'+this.component_id).getContent();
            this.form.training_id = this.training.id;
            this.form.order_id = this.exercises.length;

            if (method === 'patch') {
                axios.patch(this.requestUrl += '/' + this.form.id, this.form)
                    .then(res => {
                        this.loaderEvent();
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error);
                    });
                this.method = 'post';
            } else {
                axios.post(this.requestUrl, this.form)
                    .then(res => {
                        this.loaderEvent();
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error);
                    });
            }
            this.editor = false;
            this.form.title = '';
            this.form.description = '';
            this.form.recommended_iterations = '';
            this.form.order_id = null;
        },
        validate() {
            const form = this.$el.querySelector('.needs-validation');

            if (!form.checkValidity()) {
                form.classList.add('was-validated')
                return false;
            } else {
                form.classList.remove('was-validated')
                return true;
            }
        },
        toggle(id){
            if (this.toggle_id != id){
                this.toggle_id = id;
            } else {
                this.toggle_id = '';
            }
        },
        update(exercise) {
            const index = this.exercises.findIndex(
                vc => vc.id === exercise.id
            );

            for (const [key, value] of Object.entries(exercise)) {
                this.exercises[index][key] = value;
            }
        }
    },

    components: {
        ExerciseModal,
        ConfirmModal,
        ExerciseDones,
    },
}
</script>
