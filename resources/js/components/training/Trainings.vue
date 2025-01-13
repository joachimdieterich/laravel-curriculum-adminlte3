<template >
    <div class="row ">
        <div class="col-12 pt-2">
            <div class="card mb-0">
                <div v-if="trainings.length > 0"
                    class="card-header"
                >
                    {{ trans('global.training.title') }}
                </div>
                <div v-for="training in trainings"
                    class="card-footer"
                >
                    <a :href="'/trainings/' + training.id">{{ training.title }}</a>
                    <!-- General tools such as edit or delete-->
                    <div class="tools pull-right">
                        <span>
                            <small v-if="training.begin !== null && training.end !== null" class="badge badge-secondary mr-2">
                                {{ diffForHumans(training.begin) }} - {{ diffForHumans(training.end) }}
                            </small>
                            <small v-else-if="training.begin !== null" class="badge badge-secondary mr-2">
                                {{ trans('global.begin') + ' ' + diffForHumans(training.begin) }}
                            </small>
                            <small v-else-if="training.end !== null" class="badge badge-secondary mr-2">
                                {{ trans('global.end') + ' ' + diffForHumans(training.end) }}
                            </small>
                        </span>
                        <span v-if="$userId == plan.owner_id">
                            <a @click="lower(training)">
                                <i class="px-1 fa fa-caret-up text-muted pointer mx-1"></i>
                            </a>
                            <a @click="higher(training)">
                                <i class="px-1 fa fa-caret-down text-muted pointer mx-1"></i>
                            </a>
                            <a @click="openModal(training)">
                                <i class="px-1 fa fa-pencil-alt text-muted pointer mx-1"></i>
                            </a>
                            <a @click="destroy(training)">
                                <i class="px-1 fas fa-trash text-danger pointer mx-1"></i>
                            </a>
                        </span>
                    </div>
                </div>

                <div v-if="$userId == plan.owner_id"
                    class="card-footer pointer"
                    @click="openModal()"
                >
                    <i class="fas fa-add pr-1"></i>
                    {{ trans('global.training.create') }}
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import moment from "moment/moment";
import {useGlobalStore} from "../../store/global";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

export default {
    components: {
        VueDatePicker,
    },
    props: {
        subscribable_type: {
            default: null,
        },
        subscribable_id: {
            default: null,
        },
        plan: {
            type: Object,
        }
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
            trainings: [],
        }
    },
    mounted() {
        this.loaderEvent();

        // TRAINING events
        this.$eventHub.on('training-added', (e) => {
            if (this.subscribable_id === e.id) {
                this.trainings.push(e.training);
            }
        });
        this.$eventHub.on('training-updated', (e) => {
            if (this.subscribable_id === e.id) {
                Object.assign(
                    this.trainings.find(training => training.id === e.training.id),
                    e.training
                );
            }
        });
    },
    methods: {
        loaderEvent() {
            axios.get('/trainingSubscriptions?subscribable_type=' + this.subscribable_type + '&subscribable_id=' + this.subscribable_id)
                .then(response => {
                    this.trainings = response.data;
                })
                .catch(e => {
                    console.log(e);
                });
        },
        openModal(training = {}) {
            training.subscribable_id = this.subscribable_id;
            training.subscribable_type = this.subscribable_type;
            this.globalStore.showModal('training-modal', training);
        },
        destroy(training){
            axios.delete('/trainings/' + training.id)
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
            this.form.order_id = this.form.order_id + 1;
            this.method = 'patch';
            this.submit(training);
        },
        diffForHumans: function (date) {
            return moment(date).locale('de').fromNow();
        },
    },
}
</script>