<template >
    <div style="width: 100%;">
        <div style=" width: 100%;display: flex;justify-content: space-between;">
            <div style="border-right: 2px solid; width: 60px">
                {{ dones.length }} / {{exercise.recommended_iterations}}
            </div>
            <div v-for="(done,index) in dones"
                 class="px-2"
                 style="border-left: 1px dashed;border-right: 1px dashed;border-radius: 5px;">
                <div class="pointer"
                     @click="edit('done_'+done.id, done.iterations)">
                    <span v-if="editor !== 'done_'+done.id"
                          data-toggle="tooltip"
                          :title="entryDate(done.created_at)">
                        {{ done.iterations }}
                    </span>
                    <div v-else class="input-group">
                        <input
                            v-if="editor === 'done_'+done.id"
                            :id="'done_'+done.id"
                            :ref="'done_'+done.id"
                            type="text"
                            v-model="form.iterations"
                            style="background: transparent;width:30px;font-size: 1.1rem; font-weight: 400; border: 0; border-bottom: 1px; border-style:solid; margin: 0;"
                            @keyup.enter="submit(done.id)"
                        />
                        <div class="input-group-append">
                            <span class="input-group-text"
                                  style="background: transparent !important; border: 0 !important;margin: 0;">
                                <i  class="fas fa-trash text-danger"
                                    @click="destroy(done.id)" >
                                </i>
                            </span>
                        </div>
                    </div>

                </div>
            </div>

            <div
                class="px-2"
                style="border-left: 1px dashed;border-right: 1px dashed;border-radius: 5px;">
                <div class="pointer"
                     @click="edit('done_new_'+exercise.id)">
                <span v-if="editor !== 'done_new_'+exercise.id">
                    <i class="fa fa-add"></i>
                </span>
                    <input
                        v-if="editor === 'done_new_'+exercise.id"
                        :id="'done_new_'+exercise.id"
                        :ref="'done_new_'+exercise.id"
                        type="text"
                        v-model="form.iterations"
                        style="background: transparent;width:30px;font-size: 1.1rem; font-weight: 400; border: 0; border-bottom: 1px; border-style:solid; margin: 0;"
                        @keyup.enter="submit()"
                    />
                </div>

            </div>
        </div>
        <div style="width:50px;justify-content: flex-start;">
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
            default: null
        },
        done:{}
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            requestUrl: '/exerciseDones',
            form: new Form({
                'id': null,
                'exercise_id':'',
                'iterations': '',
                'user__id': '',
            }),
            editor: '',
            dones: [],

            errors: {}
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
        loaderEvent(){
            axios.get(this.requestUrl+'?exercise_id=' + this.exercise.id)
                .then(response => {
                    this.dones = response.data.exercise.dones;
                })
                .catch(e => {
                    console.log(e);
                });
        },
        edit(id, value = '') {
            this.editor = id;
            this.form.iterations = value;
            this.$nextTick(() => {
                this.$refs[id][0].focus();
            });

        },

        destroy(id){
            axios.delete(this.requestUrl +'/'+ id)
                .then(response => {
                    this.loaderEvent();
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
                axios.patch(this.requestUrl + '/' + this.form.id, this.form)
                    .then(res => {
                        const index = this.dones.findIndex(
                            done => done.id === res.data.entry.id
                        );
                        this.$set(this.dones, index, res.data.entry)
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error);
                    });
            } else {
                axios.post(this.requestUrl, this.form)
                    .then(res => {
                        this.dones.push(res.data.entry)
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error);
                    });
            }
            this.editor = false;
            this.form.iterations = '';
            this.form.id = null;
        },
        chartData: function (exercise){
            if (typeof (exercise.dones) != 'undefined'){
                return exercise.dones.map(i => {return i.iterations});
            }
        }
    },
    components: {
        Sparkline
    },
}
</script>
