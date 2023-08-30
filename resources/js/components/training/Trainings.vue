<template >
    <div class="row ">
        <div class="col-12 pt-2">
            <div class="card mb-0">
                <div v-if="trainings.length > 0"
                     class="card-header">
                    {{ trans('global.training.title') }}
                </div>
                <div v-for="(training, index) in trainings"
                     class="card-footer">
                    <a :href="'/trainings/'+training.id">{{training.title}}</a>

                    <!-- General tools such as edit or delete-->
                    <div
                         class="tools pull-right ">
                        <small class="badge badge-secondary mr-2 ">
                            {{ diffForHumans(training.begin) }} - {{ diffForHumans(training.end) }}
                        </small>
                        <span v-if="$userId == plan.owner_id">
                            <a @click="lower(training)" >
                                <i class="pr-2 fa fa-caret-up text-muted"></i>
                            </a>
                            <a @click="higher(training)" >
                                <i class="pr-2 fa fa-caret-down text-muted"></i>
                            </a>
                            <a @click="edit(training)" >
                                <i class="pr-2 fa fa-pencil-alt text-muted"></i>
                            </a>

                            <a @click="destroy(training)" >
                                <i class="fas fa-trash text-danger"></i>
                            </a>
                        </span>

                    </div>
                </div>

                <div v-if="$userId == plan.owner_id"
                    class="card-footer pointer"
                     @click="edit()">
                    <i class="fas fa-add pr-1"></i>
                    {{ trans('global.training.create') }}
                </div>
                <div v-if="editor"
                     class="card-body">
                    <div class="form-group">
                        <input
                            type="text"
                            id="title"
                            name="title"
                            class="form-control"
                            v-model.trim="form.title"
                            :placeholder="trans('global.training.fields.title')"
                            required
                        />
                        <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                    </div>

                    <div class="form-group">
                            <textarea
                                :id="'description'+component_id"
                                :name="'description'+component_id"
                                :placeholder="trans('global.training.fields.description')"
                                class="form-control description my-editor"
                                v-model.trim="form.description"
                            ></textarea>
                        <p class="help-block" v-if="form.errors.description" v-text="form.errors.description[0]"></p>
                    </div>

                    <div class="form-group">
                        <date-picker
                            v-if="editor !== false"
                            class="w-100 mt-3"
                            v-model="form.begin"
                            type="date"
                            valueType="YYYY-MM-DD HH:mm:ss"
                            :placeholder="trans('global.training.begin')">
                        </date-picker>

                        <date-picker
                            v-if="editor !== false"
                            class="w-100 mt-3"
                            v-model="form.end"
                            type="date"
                            valueType="YYYY-MM-DD HH:mm:ss"
                            :placeholder="trans('global.training.end')">
                        </date-picker>
                    </div>
                    <button :name="'trainingSave'"
                            class="btn btn-primary p-2 m-2"
                            @click="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>

            </div>

        </div>

    </div>



</template>

<script>
import Form from "form-backend-validation";
import moment from "moment/moment";

export default {
    props: {
        subscribable_type: {
            default: null
        },
        subscribable_id: {
            default: null
        },
        plan: []
    },
    data() {
        return {
            component_id: '',
            method: 'post',
            requestUrl: '/trainings',
            form: new Form({
                'id': null,
                'title':'',
                'description': '',
                'order_id': 0,
                'begin':'',
                'end': '',
                'subscribable_type': '',
                'subscribable_id': '',
            }),
            editor: false,
            trainings: [],
            errors: {}
        }
    },
    mounted() {
        this.loaderEvent();
        this.$initTinyMCE([
            "autolink link"
        ] );
    },
    methods: {
        loaderEvent(){
            axios.get('/trainingSubscriptions?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id)
                .then(response => {
                    this.trainings = response.data.trainings;
                })
                .catch(e => {
                    console.log(e);
                });
        },
        edit(training = false) {
            this.editor = !this.editor;
            if (training){
                this.form.id = training.id;
                this.form.title = training.title;
                this.form.description = training.description;
                this.form.order_id = training.order_id;
                this.form.begin = training.begin;
                this.form.end = training.end;
                this.method = 'patch';
            }


            this.$nextTick(() => {
                this.$initTinyMCE( );
            });

        },
        destroy(training){
            axios.delete('/trainings/'+training.id)
                .then(response => {
                    this.loaderEvent();
                })
                .catch(e => {
                    console.log(e);
                });
        },
        lower(training){
            this.form.id = training.id;
            this.form.order_id = this.form.order_id - 1;
            this.method = 'patch';
            this.submit(training);
        },
        higher(training){
            this.form.id = training.id;
            this.form.order_id = this.form.order_id + 1 ;
            this.method = 'patch';
            this.submit(training);
        },

        submit(training = null) {
            let method = this.method.toLowerCase();
            this.form.description = tinyMCE.get('description'+this.component_id)?.getContent() ?? training.description;
            this.form.subscribable_type = this.subscribable_type;
            this.form.subscribable_id = this.subscribable_id;
            this.form.order_id = this.trainings.length;
            if (method === 'patch') {
                axios.patch(this.requestUrl + '/' + this.form.id, this.form)
                    .then(res => {
                        this.loaderEvent();
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error);
                    });
            } else {
                axios.post(this.requestUrl, this.form)
                    .then(res => {
                        window.location = '/trainings/' + res.data.entry.id;
                        //this.loaderEvent();
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error);
                    });
            }
            this.editor = false;
        },
        diffForHumans: function (date) {
            return  moment(date).locale('de').fromNow();
        },
    },

}
</script>
