<template>
    <div
        :id="component_id"
        class="carousel slide"
        data-bs-interval="false"
    >
        <div v-if="subscriptions.length > 1"
            class="carousel-indicators"
        >
            <button v-for="(item, index) in subscriptions"
                :class="{ 'active': index === 0 }"
                type="button"
                :data-bs-target="'#' + component_id"
                :data-bs-slide-to="index"
                @click="setSlide(index)"
            ></button>
        </div>
        <div class="carousel-inner pointer">
            <div
                :id="'loading_' + component_id"
                class="overlay text-center rounded-0 w-100"
                style="display: none;"
            >
                <i class="fa fa-spinner fa-pulse fa-fw"></i>
                <span class="sr-only">{{ trans('global.loading') }}</span>
            </div>

            <button v-if="$userId == subscriptions[currentSlide]?.medium.owner_id"
                type="button"
                class="d-print-none btn btn-icon position-absolute text-danger pointer px-2"
                style="top: 5px; right: 5px; z-index: 30;"
                @click.stop="unlinkMedium(subscriptions[currentSlide])"
            >
                <i class="fa fa-trash"></i>
            </button>

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
            :href="'#' + component_id"
            class="d-print-none carousel-control-prev"
            style="z-index: 20;"
            role="button"
            data-bs-slide="prev"
            :aria-label="trans('pagination.previous')"
            @click="prev()"
        >
            <span class="carousel-control-prev-icon" aria-hidden="true" style="filter: drop-shadow(0 0 1px black);"></span>
            <span class="sr-only">{{ trans('pagination.previous') }}</span>
        </a>
        <a v-if="subscriptions.length > 1"
            :href="'#' + component_id"
            class="d-print-none carousel-control-next"
            style="z-index: 20;"
            role="button"
            data-bs-slide="next"
            :aria-label="trans('pagination.next')"
            @click="next()"
        >
            <span class="carousel-control-next-icon" aria-hidden="true" style="filter: drop-shadow(0 0 1px black);"></span>
            <span class="sr-only">{{ trans('pagination.next') }}</span>
        </a>
    </div>
</template>
<script>
import MediaRenderer from './MediaRenderer.vue';

export default {
    components: { MediaRenderer },
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
            component_id: 'carousel_' + this.$.uid,
            currentSlide: 0,
        }
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
        unlinkMedium(item) { //id of external reference and value in db
            document.getElementById('loading_' + this.component_id).style.display = '';
            axios.post('/mediumSubscriptions/destroy', {
                medium_id: item.medium_id,
                subscribable_id: item.subscribable_id,
                subscribable_type: item.subscribable_type,
                additional_data: true, // hack to skip setting medium_id of model to null
            })
            .then(res => {
                document.getElementById('loading_' + this.component_id).style.display = 'none';
                this.subscriptions.splice(this.currentSlide, 1);
                if (this.subscriptions.length === 0) return;
                new bootstrap.Carousel(document.getElementById(this.component_id)).to(0);
                this.currentSlide = 0;
            })
            .catch(err => {
                document.getElementById('loading_' + this.component_id).style.display = 'none';
                console.log(err.response);
            });
        },
    },
}
</script>