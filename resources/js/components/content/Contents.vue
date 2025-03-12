<template >
    <div class="card border-0 mb-0">
        <div class="card-header px-3">
            <h3 v-if="subscriptions.length > 0"
                class="card-title"
            >
                <span
                    :data-target="'#contentCarousel_' + uid"
                    data-slide-to="0"
                    class="text-sm"
                >
                    <i class="fa fa-list"></i>
                </span>
                <span v-if="currentSlide === 0"
                    class="pl-2"
                >
                    Index
                </span>
                <span v-else
                    class="pl-2"
                >
                    {{ subscriptions[currentSlide-1].content.title }}
                </span>
            </h3>
            <h3 v-else class="card-title">
                <span>
                    {{ trans('global.content.no_content') }}
                </span>
            </h3>

            <div class="card-tools">
                <button
                    v-permission="'content_create,' + subscribable_type + '_content_create'"
                    class="btn btn-tool"
                    type="button"
                    role="button"
                    :aria-label="trans('global.add')"
                    @click="create()"
                >
                    <i class="fa fa-plus"></i>
                </button>
                <button v-if="subscribable_type === 'App\\Curriculum'"
                    v-permission="'content_create,' + subscribable_type + '_content_create'"
                    class="btn btn-tool "
                    type="button"
                    role="button"
                    :aria-label="trans('global.paste')"
                    @click="this.showContentSubscriptionModal = true"
                >
                    <i class="fa fa-paste"></i>
                </button>
                <button
                    v-permission="'content_create,' + subscribable_type + '_content_create'"
                    class="btn btn-tool "
                    type="button"
                    role="button"
                    :aria-label="trans('global.resetOrder')"
                    data-toggle="tooltip"
                    data-container="body"
                    :title="trans('global.resetOrder')"
                    @click.prevent="fixOrderIds()"
                >
                    <i class="fa fa-wrench"></i>
                </button>
                <button v-if="subscriptions.length > 0"
                    class="btn btn-tool"
                    type="button"
                    role="button"
                    :href="'#contentCarousel_' + uid"
                    :aria-label="trans('pagination.previous')"
                    @click="prev()"
                >
                    <i class="fa fa-arrow-left"></i>
                </button>
                <button v-if="subscriptions.length > 0"
                    class="btn btn-tool "
                    type="button"
                    role="button"
                    :href="'#contentCarousel_' + uid"
                    :aria-label="trans('pagination.next')"
                    @click="next()"
                >
                    <i class="fa fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <div v-if="subscriptions.length !== 0"
            class="card-content">
            <div :id="'contentCarousel_'+uid" class="carousel slide" data-interval="false">
                <ol class="carousel-indicators">
                    <li :data-target="'#contentCarousel_'+uid"
                        data-slide-to="0"
                        class="active"
                        @click="setSlide(0)">
                    </li>
                    <li v-for="(item,index) in subscriptions"
                        data-placement="top"
                        :title="item.content.title"
                        :data-target="'#contentCarousel_'+uid"
                        :data-slide-to="index+1"
                        @click="setSlide(index+1)"
                    ></li>
                </ol>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <ul class="list-unstyled p-3" title="Index">
                            <li v-for="(item,index) in subscriptions"
                                class="pb-2">
                                <span class="pointer">
                                    <span :data-target="'#contentCarousel_'+uid"
                                        :data-slide-to="index+1"
                                        @click="setSlide(index+1)">
                                        {{ item.content.title }}
                                    </span>
                                    <span v-permission="'content_delete, ' + subscribable_type + '_content_delete'"
                                        class="pull-right vuehover"
                                        :aria-label="trans('global.delete')">
                                        <span
                                            class="btn-tool fa fa-trash text-danger"
                                            @click.prevent="deleteSubscription(item)"
                                        >
                                        </span>
                                    </span>
                                    <span v-permission="'content_edit, ' + subscribable_type + '_content_edit'"
                                        class="pull-right vuehover"
                                        :aria-label="trans('global.edit')">
                                        <span
                                            class="btn-tool fa fa-pencil-alt"
                                            @click.prevent="edit(item)"
                                        >
                                        </span>
                                    </span>
                                    <span v-permission="'content_create, ' + subscribable_type + '_content_create'"
                                        class="pull-right vuehover"><!--Order_id: {{ item.order_id }}-->
                                        <span v-if="(item.order_id !== 0)"
                                            class="btn-tool fa fa-arrow-up"
                                            aria-label="up"
                                            @click.prevent="sortEvent(item,-1)">
                                        </span>

                                        <span v-if="( subscriptions.length-1 !== item.order_id)"
                                            class="btn-tool fa fa-arrow-down"
                                            aria-label="down"
                                            @click.prevent="sortEvent(item,1)">
                                        </span>
                                    </span>
                                    <br>
                                    <small class="text-muted"
                                            :data-target="'#contentCarousel_'+uid"
                                            :data-slide-to="index+1"
                                            @click="setSlide(index+1)"
                                    v-html="item.content.content">
                                    </small>
                                </span>
                            </li>
                        </ul>
                    </div>

                    <div v-for="item in subscriptions"
                        class="carousel-item" :title="item.content.title">
                        <div class="p-3"
                            v-html="item.content.content"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <ContentSubscriptionModal
                :show="showContentSubscriptionModal"
                @close="this.showContentSubscriptionModal = false"
                :params="{
                    subscribable_type: subscribable_type,
                    subscribable_id: subscribable_id,
                }"
            />
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.content.delete')"
                :description="trans('global.content.delete_helper')"
                @close="() => {
                    this.showConfirm = false;
                }"
                @confirm="() => {
                    this.showConfirm = false;
                    this.destroy();
                }"
            />
        </Teleport>
    </div>
</template>
<script>
import ContentSubscriptionModal from "./ContentSubscriptionModal.vue";
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
    components: {
        ContentSubscriptionModal,
        ConfirmModal,
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            uid: null,
            subscriptions: [],
            errors: {},
            currentSlide: 0,
            currentContent: {},
            showConfirm: false,
            showContentSubscriptionModal: false,
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
            $('#contentCarousel_' + this.uid).carousel(this.currentSlide);
        },
        next() {
            if (this.currentSlide === this.subscriptions.length) {
                this.currentSlide = 0;
            } else {
                this.currentSlide++;
            }
            $('#contentCarousel_' + this.uid).carousel(this.currentSlide);
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
                this.errors = error.response.data.errors;
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
                this.errors = error.response.data.errors;
            }
        },
        create() {
            this.globalStore?.showModal('content-modal',{
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
        deleteSubscription(contentSubscription) {
            axios.post('/contents/' + contentSubscription.content_id + '/destroy', {
                    subscribable_type:  contentSubscription.subscribable_type,
                    subscribable_id:    contentSubscription.subscribable_id,
                })
                .then(res => {
                    let index = this.subscriptions.indexOf(contentSubscription);
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
                    this.errors = e.data.errors;
                });
        },
    },
    mounted() {
        this.uid = this.$.uid;
        this.currentSlide = 0;

        this.$eventHub.on('content-added', content => {
            if (content.subscribable_id === this.subscribable_id) this.subscriptions.push(content);
        });

        this.$eventHub.on('content-updated', content => {
            if (content.subscribable_id === this.subscribable_id) {
                this.subscriptions.find(s => s.content.id == content.id).content = content;
            }
        });

        this.currentContent = {
            subscribable_type:  this.subscribable_type,
            subscribable_id:    this.subscribable_id,
        }
    },
}
</script>