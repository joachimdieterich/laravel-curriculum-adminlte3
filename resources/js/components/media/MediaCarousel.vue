<template>
    <div
        :id="id"
        class="carousel slide ignore"
        data-interval="false"
    >
        <div v-if="subscriptions.length > 1"
            class="carousel-indicators"
        >
            <li v-for="(item, index) in subscriptions"
                :class="{ 'active': index === 0 }"
                :data-target="'#' + id"
                :data-slide-to="index"
                @click="setSlide(index)">
            </li>
        </div>
        <div class="carousel-inner">
            <div class="w-100">
                <div class="carousel-indicators-tools">
                    <a
                        :id="'download_medium_' + subscriptions[currentSlide].medium_id"
                        @click.prevent="downloadMedium(subscriptions[currentSlide])"
                        class="text-muted pointer px-2"
                    >
                        <i class="fa fa-download"></i>
                    </a>
                    <a v-if="$userId == subscriptions[currentSlide].medium.owner_id"
                        class="text-danger pointer px-2"
                        @click.prevent="unlinkMedium(subscriptions[currentSlide])"
                    >
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
            </div>

            <div v-for="(item, index) in subscriptions"
                :class="{ 'active': index === 0 }"
                class="carousel-item"
            >
                <MediaRenderer
                    :medium="item.medium"
                    :width="width"
                />
            </div>
        </div>

        <a v-if="subscriptions.length > 1"
            :href="'#' + id"
            class="carousel-control-prev "
            role="button"
            data-slide="prev"
            :aria-label="trans('pagination.previous')"
            @click="prev()"
        >
            <span class="carousel-control-prev-icon" aria-hidden="true" style="filter: drop-shadow(0 0 1px black);"></span>
            <span class="sr-only ignore">{{ trans('pagination.previous') }}</span>
        </a>
        <a v-if="subscriptions.length > 1"
            :href="'#' + id"
            class="carousel-control-next"
            role="button"
            data-slide="next"
            :aria-label="trans('pagination.next')"
            @click="next()"
        >
            <span class="carousel-control-next-icon" aria-hidden="true" style="filter: drop-shadow(0 0 1px black);"></span>
            <span class="sr-only ignore">{{ trans('pagination.next') }}</span>
        </a>
    </div>
</template>

<script>
import MediaRenderer from './MediaRenderer.vue';

export default {
    props: {
        subscriptions: {
            type: Array,
            default: null,
        },
        width: {
            type: Number,
            default: null,
        },
    },
    data() {
        return {
            id: null,
            currentSlide: 0,
            errors: {},
        }
    },
    mounted() {
        this.id = 'carousel_' + this.$.uid
    },
    methods: {
        setSlide(id) {
            this.currentSlide = id;
        },
        prev() {
            if (this.currentSlide === 0) {
                this.currentSlide = this.subscriptions.length - 1;
            } else {
                this.currentSlide--;
            }
        },
        next() {
            if (this.currentSlide === this.subscriptions.length - 1) {
                this.currentSlide = 0;
            } else {
                this.currentSlide++;
            }
        },
        downloadMedium(item) {
            this.$eventHub.emit('download', item.medium);
        },
        unlinkMedium(item) { //id of external reference and value in db
            axios.post('/mediumSubscriptions/destroy', {
                medium_id: item.medium_id,
                subscribable_id: item.subscribable_id,
                subscribable_type: item.subscribable_type,
                sharing_level_id: item.sharing_level_id,
                visibility: item.visibility,
                additional_data: true, // hack to skip setting medium_id of model to null
            })
            .then(res => {
                this.subscriptions.splice(item, 1);
                if (this.subscriptions.length <= 1) return;

                if (this.currentSlide == 0) {
                    $('#' + this.id).carousel('next');
                } else {
                    $('#' + this.id).carousel('prev');
                }
            })
            .catch(err => {
                console.log(err.response);
            });
        },
    },
    components: {
        MediaRenderer,
    },
}
</script>