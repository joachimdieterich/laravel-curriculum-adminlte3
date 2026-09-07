<template >
    <div class="bg-white border border-top-0 rounded-bottom-2">
        <div class="d-flex align-items-center p-3">
            <div v-if="subscriptions.length > 0"
                class="d-flex align-items-center"
            >
                <button
                    type="button"
                    class="btn btn-icon text-secondary"
                    :data-bs-target="'#content-carousel-' + uid"
                    data-bs-slide-to="0"
                    @click="setSlide(0)"
                >
                    <i class="fa fa-list"></i>
                </button>
                <span v-if="currentSlide === 0" class="t-18 ms-2">Index</span>
                <span v-else
                    class="d-flex align-items-center"
                >
                    <span class="t-18 mx-2">{{ subscriptions[currentSlide - 1].content.title }}</span>
                    <button
                        type="button"
                        class="btn btn-icon text-secondary"
                        :aria-label="trans('global.content.edit')"
                        @click="edit(subscriptions[currentSlide - 1])"
                    >
                        <i class="fa fa-pencil"></i>
                    </button>
                </span>
            </div>
            <span v-else class="t-18">
                {{ trans('global.content.no_content') }}
            </span>

            <div class="d-flex align-items-center gap-2 ms-auto">
                <button v-if="allowCreate"
                    type="button"
                    class="d-print-none btn btn-icon text-secondary"
                    data-bs-toggle="tooltip"
                    :data-bs-title="trans('global.content.create')"
                    @click="create()"
                >
                    <i class="fa fa-plus"></i>
                </button>
                <button v-if="allowCreate"
                    type="button"
                    class="d-print-none btn btn-icon text-secondary"
                    data-bs-toggle="tooltip"
                    :data-bs-title="trans('global.resetOrder')"
                    @click.prevent="fixOrderIds()"
                >
                    <i class="fa fa-wrench"></i>
                </button>
                <button v-if="subscriptions.length > 0"
                    type="button"
                    class="d-print-none btn btn-icon text-secondary"
                    :data-bs-target="'#content-carousel-' + uid"
                    data-bs-slide="prev"
                    data-bs-toggle="tooltip"
                    :aria-label="trans('pagination.previous')"
                    @click="prev()"
                >
                    <i class="fa fa-arrow-left"></i>
                </button>
                <button v-if="subscriptions.length > 0"
                    type="button"
                    class="d-print-none btn btn-icon text-secondary"
                    :data-bs-target="'#content-carousel-' + uid"
                    data-bs-slide="next"
                    :aria-label="trans('pagination.next')"
                    @click="next()"
                >
                    <i class="fa fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <div v-if="subscriptions.length !== 0"
            :id="'content-carousel-' + uid"
            class="carousel carousel-dark slide border-top"
        >
            <div class="carousel-indicators">
                <button
                    class="active"
                    type="button"
                    :data-bs-target="'#content-carousel-' + uid"
                    data-bs-slide-to="0"
                    aria-current="true"
                    aria-label="Index"
                    @click="setSlide(0)"
                ></button>
                <button v-for="(item, index) in subscriptions"
                    type="button"
                    :data-bs-target="'#content-carousel-' + uid"
                    :data-bs-slide-to="index + 1"
                    :aria-label="item.content.title"
                    @click="setSlide(index + 1)"
                ></button>
            </div>
            <div class="carousel-inner pb-3">
                <div class="carousel-item active">
                    <div class="d-flex flex-column gap-2 p-3">
                        <div v-for="(item, index) in subscriptions"
                            class="position-relative btn-icon-hover"
                        >
                            <span
                                class="pointer"
                                :data-bs-target="'#content-carousel-' + uid"
                                :data-bs-slide-to="index + 1"
                                @click="setSlide(index + 1)"
                            >
                                <span>{{ item.content.title }}</span>
                                <br>
                                <small
                                    class="text-muted line-clamp"
                                    v-html="item.content.content"
                                    :data-target="'#content-carousel-' + uid"
                                    :data-slide-to="index + 1"
                                    @click="setSlide(index + 1)"
                                ></small>
                            </span>
                            <div class="position-absolute top-0 end-0 d-flex gap-2">
                                <button v-if="allowEdit && item.order_id !== 0"
                                    type="button"
                                    class="btn btn-icon btn-hide text-secondary"
                                    :aria-label="trans('global.content.move_up')"
                                    @click="sortEvent(item, -1)"
                                >
                                    <i class="fa fa-arrow-up"></i>
                                </button>
                                <button v-if="allowEdit && subscriptions.length - 1 !== item.order_id"
                                    type="button"
                                    class="btn btn-icon btn-hide text-secondary"
                                    :aria-label="trans('global.content.move_down')"
                                    @click.prevent="sortEvent(item, 1)"
                                >
                                    <i class="fa fa-arrow-down"></i>
                                </button>
                                <button v-if="allowEdit"
                                    type="button"
                                    class="btn btn-icon btn-hide text-secondary"
                                    :aria-label="trans('global.content.edit')"
                                    @click="edit(item)"
                                >
                                    <i class="fa fa-pencil-alt"></i>
                                </button>
                                <button v-if="allowDelete"
                                    type="button"
                                    class="btn btn-icon btn-hide text-danger"
                                    :aria-label="trans('global.content.delete')"
                                    @click="confirmDelete(item)"
                                >
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-for="item in subscriptions"
                    class="carousel-item"
                    :title="item.content.title"
                >
                    <div class="p-3" v-html="item.content.content"></div>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.content.delete')"
                :description="trans('global.content.delete_helper')"
                @close="() => showConfirm = false"
                @confirm="() => {
                    showConfirm = false;
                    destroy();
                }"
            />
        </Teleport>
    </div>
</template>
<script>
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import {useGlobalStore} from "../../store/global";

export default {
    props: {
        subscription: {
            type: Object,
            default: null,
        },
        subscribable_type: {
            type: String,
            default: null,
        },
        subscribable_id: {
            type: Number,
            default: null,
        },
        medium: {
            type: Object,
            default: null,
        },
    },
    components: { ConfirmModal },
    setup() {
        return { globalStore: useGlobalStore() }
    },
    data() {
        return {
            uid: this.$.uid,
            subscriptions: [],
            currentSlide: 0,
            currentContent: {},
            showConfirm: false,
        }
    },
    methods: {
        setSlide(id) {
            this.currentSlide = id;
        },
        prev() {
            if (this.currentSlide === 0) {
                this.currentSlide = this.subscriptions.length;
            } else {
                this.currentSlide--;
            }
        },
        next() {
            if (this.currentSlide === this.subscriptions.length) {
                this.currentSlide = 0;
            } else {
                this.currentSlide++;
            }
        },
        async sortEvent(contentSubscription,amount) {
            let subscription = {
                subscribable_type: contentSubscription.subscribable_type,
                subscribable_id:   contentSubscription.subscribable_id,
                content_id:        contentSubscription.content_id,
                order_id:          contentSubscription.order_id + parseInt(amount),
            }

            try {
                this.subscriptions = (await axios.patch('/contentSubscriptions/', subscription)).data.message;
            } catch(error) {
                console.log(error);
            }
        },
        async fixOrderIds() {
            let subscription = {
                subscribable_type: this.subscribable_type,
                subscribable_id:   this.subscribable_id,
            }

            try {
                this.subscriptions = (await axios.patch('/contentSubscriptions/reset', subscription)).data.message;
            } catch(error) {
                console.log(error);
            }
        },
        create() {
            this.globalStore?.showModal('content-modal', {
                subscribable_type:  this.subscribable_type,
                subscribable_id:    this.subscribable_id,
            });
        },
        edit(item) {
            this.globalStore?.showModal('content-modal', {
                id:                 item.content.id,
                title:              item.content.title,
                content:            item.content.content,
                subscribable_type:  item.subscribable_type,
                subscribable_id:    item.subscribable_id,
            });
        },
        confirmDelete(item) {
            this.currentContent = item;
            this.showConfirm = true;
        },
        destroy() {
            axios.post('/contents/' + this.currentContent.content_id + '/destroy', {
                    subscribable_type:  this.currentContent.subscribable_type,
                    subscribable_id:    this.currentContent.subscribable_id,
                })
                .then(res => {
                    let index = this.subscriptions.indexOf(this.currentContent);
                    this.subscriptions.splice(index, 1);
                })
                .catch(e => {
                    console.log(e);
                });
        },
        loaderEvent() {
            axios.get('/contentSubscriptions?subscribable_type=' + this.subscribable_type + '&subscribable_id=' + this.subscribable_id)
                .then(response => {
                    this.subscriptions = response.data.message;
                })
                .catch(e => {
                    console.log(e);
                });
        },
    },
    mounted() {
        this.currentSlide = 0;

        this.$eventHub.on('content-added', content => {
            if (content.subscribable_id === this.subscribable_id) this.subscriptions.push(content);
        });

        this.$eventHub.on('content-updated', content => {
            if (content.subscribable_id === this.subscribable_id) {
                this.subscriptions.find(s => s.content.id == content.id).content = content;
            }
        });
    },
    computed: {
        allowCreate() {
            return this.checkPermission(this.subscribable_type + '_content_create');
        },
        allowEdit() {
            return this.checkPermission(this.subscribable_type + '_content_edit');
        },
        allowDelete() {
            return this.checkPermission(this.subscribable_type + '_content_delete');
        },
    },
}
</script>