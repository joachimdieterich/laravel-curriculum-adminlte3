<template>
    <div>
        <div class="mb-0">
            <div v-if="trainings.length > 0"
                class="p-3 border-bottom"
            >
                {{ trans('global.training.title') }}
            </div>
            <div v-for="training in trainings"
                class="d-block d-sm-flex align-items-center justify-content-between px-3 py-2 border-top bg-gray-light"
            >
                <a
                    :href="'/trainings/' + training.id"
                    class="link-underline-hover"
                >
                    {{ training.title }}
                </a>
                <div class="d-flex align-items-center gap-2 float-end">
                    <span class="d-contents">
                        <small v-if="training.begin !== null && training.end !== null" class="badge text-bg-secondary">
                            {{ diffForHumans(training.begin) }} - {{ diffForHumans(training.end) }}
                        </small>
                        <small v-else-if="training.begin !== null" class="badge text-bg-secondary">
                            {{ trans('global.begin') + ' ' + diffForHumans(training.begin) }}
                        </small>
                        <small v-else-if="training.end !== null" class="badge text-bg-secondary">
                            {{ trans('global.end') + ' ' + diffForHumans(training.end) }}
                        </small>
                    </span>
                    <span v-if="editable && showTools"
                        class="d-contents"
                    >
                        <button v-if="training.subscriptions[0].order_id > 0"
                            type="button"
                            class="btn btn-icon text-secondary"
                            @click="lower(training)"
                        >
                            <i class="fa fa-arrow-up px-1"></i>
                        </button>
                        <button v-if="training.subscriptions[0].order_id < max_order_id"
                            type="button"
                            class="btn btn-icon text-secondary"
                            @click="higher(training)"
                        >
                            <i class="fa fa-arrow-down px-1"></i>
                        </button>
                        <button
                            type="button"
                            class="btn btn-icon text-secondary"
                            @click="openModal(training)"
                        >
                            <i class="fa fa-pencil-alt px-1"></i>
                        </button>
                        <button v-if="training.owner_id == $userId || deletable || checkPermission('is_admin')"
                            type="button"
                            class="btn btn-icon text-danger"
                            @click="confirmDelete(training)"
                        >
                            <i class="fas fa-trash px-1"></i>
                        </button>
                    </span>
                </div>
            </div>

            <div v-if="editable && showTools">
                <button
                    type="button"
                    class="btn btn-default border-0 mb-1 mt-2 rounded-pill"
                    style="padding: 0.75rem 1.25rem;"
                    @click="openModal()"
                >
                    <i class="fas fa-add pe-1"></i>
                    {{ trans('global.training.create') }}
                </button>
            </div>
        </div>

        <Teleport to="body">
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.training.delete')"
                :description="trans('global.training.delete_helper')"
                @close="showConfirm = false"
                @confirm="() => {
                    showConfirm = false;
                    destroy(training);
                }"
            />
        </Teleport>
    </div>
</template>
<script>
import VueDatePicker from "@vuepic/vue-datepicker";
import '@vuepic/vue-datepicker/dist/main.css';
import ConfirmModal from "../uiElements/ConfirmModal.vue";

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
        this.$eventHub.on('training-added', training => {
            if (this.subscribable_id === training.subscriptions[0].subscribable_id) {
                this.trainings.push(training);
                this.max_order_id = training.subscriptions[0].order_id;
            }
        });
        this.$eventHub.on('training-updated', training => {
            if (this.subscribable_id === training.subscriptions[0].subscribable_id) {
                Object.assign(this.trainings.find(t => t.id === training.id), training);
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
        destroy() {
            axios.delete('/trainings/' + this.training.id)
                .then(response => {
                    let index = this.trainings.indexOf(this.training);
                    // decrease the order_id of each training below the deleted training
                    for (let i = index + 1; i < this.trainings.length; i++) {
                        this.trainings[i].subscriptions[0].order_id -= 1;
                    }

                    this.trainings.splice(index, 1);
                    this.max_order_id -= 1;
                })
                .catch(e => {
                    console.log(e);
                    this.toast.error(this.errorMessage(e));
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
        diffForHumans(date) {
            return moment(date).locale(window.navigator.language).fromNow();
        },
    },
}
</script>