<template>
    <div
        :id="component_id"
        class="carousel slide ignore"
        data-interval="false"
    >
        <div v-if="subscriptions.length > 1"
            class="carousel-indicators"
        >
            <li v-for="(item, index) in subscriptions"
                :class="{ 'active': index === 0 }"
                :data-target="'#' + component_id"
                :data-slide-to="index"
                @click="setSlide(index)">
            </li>
        </div>
        <div
            class="carousel-inner pointer"
            @click="openGallery()"
        >
            <div
                :id="'loading_' + component_id"
                class="overlay position-absolute text-center rounded-0 w-100"
                style="display: none; inset: 0;"
            >
                <i class="fa fa-spinner fa-pulse fa-fw"></i>
                <span class="sr-only">{{ trans('global.loading') }}</span>
            </div>

            <button v-if="$userId == subscriptions[currentSlide]?.medium.owner_id"
                type="button"
                class="d-print-none btn btn-icon position-absolute text-danger pointer px-2"
                style="top: 5px; right: 5px; z-index: 30;"
                @click.prevent="unlinkMedium(subscriptions[currentSlide])"
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
            data-slide="prev"
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
            data-slide="next"
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
import {useGlobalStore} from "../../store/global";

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
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
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
            $('#loading_' + this.component_id).show();
            axios.post('/mediumSubscriptions/destroy', {
                medium_id: item.medium_id,
                subscribable_id: item.subscribable_id,
                subscribable_type: item.subscribable_type,
                additional_data: true, // hack to skip setting medium_id of model to null
            })
            .then(res => {
                $('#loading_' + this.component_id).hide();
                this.subscriptions.splice(this.currentSlide, 1);
                if (this.subscriptions.length === 0) return;
                $('#' + this.component_id).carousel(0);
                this.currentSlide = 0;
            })
            .catch(err => {
                $('#loading_' + this.component_id).hide();
                console.log(err.response);
            });
        },
        openGallery() {
            this.globalStore.showModal('medium-preview-modal', {
                media: this.subscriptions.map(s => s.medium),
                initialSlide: this.currentSlide,
            });
        },
    },
    components: {
        MediaRenderer,
    },
}
</script>