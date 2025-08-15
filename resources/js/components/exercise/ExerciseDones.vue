<template >
    <div class="w-100">
        <div class="d-flex justify-content-between w-100">
            <div style="border-right: 2px solid; width: 60px">
                {{ dones.length }} / {{exercise.recommended_iterations}}
            </div>
            <div v-for="done in dones"
                class="px-2"
                style="border-left: 1px dashed; border-right: 1px dashed; border-radius: 5px;"
            >
                <div
                    class="pointer"
                    @click="edit('done_' + done.id, done.iterations)"
                >
                    <span v-if="editor !== 'done_' + done.id"
                        data-toggle="tooltip"
                        :title="entryDate(done.created_at)"
                    >
                        {{ done.iterations }}
                    </span>
                    <div v-else
                        class="input-group"
                    >
                        <input v-if="editor === 'done_' + done.id"
                            :id="'done_' + done.id"
                            :ref="'done_' + done.id"
                            type="text"
                            v-model="form.iterations"
                            class="border-0 m-0"
                            style="background: transparent; width:30px; font-size: 1.1rem; font-weight: 400; border-bottom: 1px solid black !important;"
                            @keyup.enter="submit(done.id)"
                        />
                        <div class="input-group-append ml-1">
                            <span
                                class="input-group-text p-0 border-0 m-0"
                                style="background: transparent !important;"
                                @click="destroy(done.id)"
                            >
                                <i class="fa fa-trash text-danger px-1"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="px-2"
                style="border-left: 1px dashed; border-right: 1px dashed; border-radius: 5px;"
            >
                <div
                    class="pointer"
                    @click="edit('done_new_' + exercise.id)"
                >
                    <span v-if="editor !== 'done_new_' + exercise.id">
                        <i class="fa fa-add"></i>
                    </span>
                    <input v-if="editor === 'done_new_' + exercise.id"
                        :id="'done_new_' + exercise.id"
                        :ref="'done_new_' + exercise.id"
                        type="text"
                        v-model="form.iterations"
                        class="border-0 m-0"
                        style="background: transparent; width: 30px; font-size: 1.1rem; font-weight: 400; border-bottom: 1px solid black !important;"
                        @keyup.enter="submit()"
                    />
                </div>
            </div>
        </div>
        <div style="width: 50px; justify-content: flex-start;">
            <Sparkline :data="chartData(exercise)"></Sparkline>
        </div>
    </div>
</template>
<script>
import Form from "form-backend-validation";
import Sparkline from "../statistic/Sparkline.vue";

export default {
    props: {
        exercise: {
            default: null,
        },
        done: {},
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            form: new Form({
                id: null,
                exercise_id: '',
                iterations: '',
                user__id: '',
            }),
            editor: '',
            dones: [],
        }
    },
    mounted() {
        this.dones = this.exercise.dones;
    },
    methods: {
        entryDate(created_at) {
            const date = new Date(created_at.replace(/-/g, "/"));
            const dateFormat = {
                weekday: 'short',
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                /*  hour: '2-digit',
                  minute: '2-digit'*/
            };

            return date.toLocaleString([], dateFormat) ;
        },
        edit(id, value = '') {
            this.editor = id;
            this.form.iterations = value;
            this.$nextTick(() => {
                document.getElementById(id).focus();
            });
        },
        destroy(id) {
            axios.delete('/exerciseDones/' + id)
                .then(response => {
                    if (response.data) this.dones.splice(this.dones.findIndex(done => done.id === id), 1);
                })
                .catch(e => {
                    console.log(e);
                });
        },
        submit(id = null) {
            let method = this.method.toLowerCase();
            this.form.exercise_id = this.exercise.id;

            if (id != null) {
                method = 'patch'
                this.form.id = id;
                axios.patch('/exerciseDones/' + this.form.id, this.form)
                    .then(res => {
                        Object.assign(this.dones.find(done => done.id === res.data.id), res.data);
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error);
                    });
            } else {
                axios.post('/exerciseDones', this.form)
                    .then(res => {
                        this.dones.push(res.data)
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error);
                    });
            }
            this.editor = false;
            this.form.iterations = '';
            this.form.id = null;
        },
        chartData(exercise) {
            if (typeof (exercise.dones) != 'undefined'){
                return exercise.dones.map(i => i.iterations);
            }
        }
    },
    components: {
        Sparkline,
    },
}
</script>