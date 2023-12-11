<template >
    <div class="row ">
        <div class="col-12 pt-2">
            <div class="card ">
                <div class="card-header"
                     style="display: flex;justify-content: flex-start;">
                    <span style="width:30%;">{{ trans('global.exercise.title')}}</span>
                    <span>{{ trans('global.exercisedone.fields.iterations')}}</span>
                </div>

                <div style="clear:right;"
                     v-for="(exercise,index) in exercises"
                     v-if="(exercise.title.toLowerCase().indexOf(search.toLowerCase()) !== -1) || search.length < 3"
                     :id="exercise.id"
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
                                      <i class="pl-2 fa fa-pencil-alt text-secondary"
                                         @click="edit(exercise)"></i>
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
                        v-html="exercise.description">
                    </div>
                </div>

                <div class="card-footer" role="button"
                     v-if="!editor &&  $userId == training.owner_id"
                     @click="edit()">
                    <i class="fa fa-add"></i> {{ trans('global.exercise.create') }}
                </div>

                <div v-if="editor"
                    class="card-body"
                >
                    <form class="needs-validation">
                        <div class="form-group">
                            <input
                                type="text"
                                id="title"
                                name="title"
                                class="form-control"
                                v-model.trim="form.title"
                                :placeholder="trans('global.exercise.fields.title')"
                                required
                            />
                            <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                            <div class="invalid-feedback">
                                {{ trans('global.invalid_form') }}
                            </div>
                        </div>
    
                        <div class="form-group">
                            <textarea
                                :id="'description'+component_id"
                                :name="'description'+component_id"
                                :placeholder="trans('global.exercise.fields.description')"
                                class="form-control description my-editor"
                                v-model.trim="form.description"
                            ></textarea>
                            <p class="help-block" v-if="form.errors.description" v-text="form.errors.description[0]"></p>
                        </div>
                            
                        <div class="form-group">
                            <input
                                type="number"
                                min="1"
                                id="recommended_iterations"
                                name="recommended_iterations"
                                class="form-control"
                                v-model.trim="form.recommended_iterations"
                                :placeholder="trans('global.exercise.fields.recommended_iterations')"
                                required
                            />
                            <div class="invalid-feedback">
                                {{ trans('global.invalid_form') }}
                            </div>
                        </div>
                        <button :name="'exerciseSave'"
                                class="btn btn-primary p-2 m-2"
                                @click.prevent="submit()"
                        >
                            {{ trans('global.save') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Form from "form-backend-validation";
import moment from "moment/moment";
import ExerciseDones from "./ExerciseDones";

export default {
    props: {
        training: {
            default: null
        },
    },
    data() {
        return {
            component_id: this._uid,
            method: 'post',
            requestUrl: '/exercises',
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
            toggle_id:'',
            errors: {}

        }
    },
    mounted() {
        this.loaderEvent();
        this.$initTinyMCE([
            "autolink link"
        ] );
        this.$eventHub.$on('exercise_dones_added', (e) => {
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
        edit(exercise = false) {
            if (exercise){
                this.form.id = exercise.id;
                this.form.title = exercise.title;
                this.form.description = exercise.description;
                this.form.recommended_iterations =  exercise.recommended_iterations;
                this.form.order_id = exercise.order_id;
                this.method = 'patch';
            }
            this.editor = !this.editor;

            this.$nextTick(() => {
                this.$initTinyMCE([
                    "autolink link example"
                ] );
            });

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
    },

    components: {
        ExerciseDones,
    },
}
</script>
