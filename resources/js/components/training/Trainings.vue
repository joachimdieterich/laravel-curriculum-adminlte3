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
                        <span v-if="editable && showTools">
                            <a v-if="training.subscriptions[0].order_id > 0"
                                class="text-secondary pointer"
                                @click="lower(training)"
                            >
                                <i class="fa fa-arrow-up px-1"></i>
                            </a>
                            <a v-if="training.subscriptions[0].order_id < max_order_id"
                                class="text-secondary pointer ml-2"
                                @click="higher(training)"
                            >
                                <i class="fa fa-arrow-down px-1"></i>
                            </a>
                            <a
                                class="text-secondary pointer ml-3"
                                @click="openModal(training)"
                            >
                                <i class="fa fa-pencil-alt px-1"></i>
                            </a>
                            <a v-if="training.owner_id == $userId || deletable || checkPermission('is_admin')"
                                class="text-danger pointer ml-3"
                                @click="confirmDelete(training)"
                            >
                                <i class="fas fa-trash px-1"></i>
                            </a>
                        </span>
                    </div>
                </div>

                <div v-if="editable && showTools"
                    class="card-footer pointer"
                    @click="openModal()"
                >
                    <i class="fas fa-add pr-1"></i>
                    {{ trans('global.training.create') }}
                </div>
            </div>
        </div>
        <Teleport to="body">
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.training.delete')"
                :description="trans('global.training.delete_helper')"
                @close="() => {
                    this.showConfirm = false;
                }"
                @confirm="() => {
                    this.showConfirm = false;
                    this.destroy(this.training);
                }"
            />
        </Teleport>
    </div>
</template>
<script>
import VueDatePicker from '@vuepic/vue-datepicker';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import moment from "moment/moment";
import {useGlobalStore} from "../../store/global";
import '@vuepic/vue-datepicker/dist/main.css';
import axios from "axios";

export default {
    components: {
        VueDatePicker,
        ConfirmModal,
    },
    props: {
        editable: {
            type: Boolean,
            default: false,
        },
        deletable: {
            type: Boolean,
            default: false,
        },
        showTools: {
            type: Boolean,
            default: false,
        },
        subscribable_id: {
            type: Number,
            default: null,
        },
        subscribable_type: {
            type: String,
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
            trainings: [],
            training: {},
            showConfirm: false,
            max_order_id: 0,
        }
    },
    mounted() {
        this.loaderEvent();

        // TRAINING events
        this.$eventHub.on('training-added', (e) => {
            if (this.subscribable_id === e.id) {
                this.trainings.push(e.training);
                this.max_order_id = e.training.subscriptions[0].order_id;
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

                    if (this.trainings.length > 0) {
                        // trainings are already sorted by order_id
                        this.max_order_id = this.trainings.at(-1).subscriptions[0].order_id;
                    }
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
        confirmDelete(training) {
            this.training = training;
            this.showConfirm = true;
        },
        destroy(training) {
            axios.delete('/trainings/' + training.id)
                .then(response => {
                    let index = this.trainings.indexOf(training);
                    // decrease the order_id of each training below the deleted training
                    for (let i = index + 1; i < this.trainings.length; i++) {
                        this.trainings[i].subscriptions[0].order_id -= 1;
                    }

                    this.trainings.splice(index, 1);
                    this.max_order_id -= 1;
                })
                .catch(e => {
                    console.log(e);
                });
        },
        /**
         * decrease the order_id of the training an increase the order_id of the training below
         * @param training 
         */
        lower(training) {
            axios.patch('/trainingsSubscriptions/' + training.subscriptions[0].id + '/lower')
                .then(response => {
                    this.trainings = response.data;
                })
                .catch(e => {
                    console.log(e);
                });
        },
        /**
         * increase the order_id of the training an decrease the order_id of the training above
         * @param training 
         */
        higher(training) {
            axios.patch('/trainingsSubscriptions/' + training.subscriptions[0].id + '/higher')
                .then(response => {
                    this.trainings = response.data;
                })
                .catch(e => {
                    console.log(e);
                });
        },
        diffForHumans: function (date) {
            return moment(date).locale('de').fromNow();
        },
    },
}
</script>