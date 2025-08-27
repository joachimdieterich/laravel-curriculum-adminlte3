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
            <button v-if="$userId == subscriptions[currentSlide]?.medium.owner_id"
                type="button"
                class="btn btn-icon position-absolute text-danger pointer px-2"
                style="top: 5px; right: 5px; z-index: 30;"
                @click.prevent="unlinkMedium(subscriptions[currentSlide])"
            >
                <i class="fa fa-trash"></i>
            </button>

            <div
                id="link-overlay"
                @click.self="(e) => e.target.classList.toggle('active')"
            >
                <div
                    id="link-wrapper"
                    class="d-flex align-items-center justify-content-center bg-light rounded-pill hide-lg"
                    :style="{ width: generatingLinks ? '50px' : '175px' }"
                >
                    <button v-if="!generatingLinks && !URLsLoaded"
                        type="button"
                        class="btn btn-default bg-transparent rounded-pill border-0 w-100"
                        @click="getURLs()"
                    >
                        <i class="fa fa-link"></i>
                        {{ trans('global.medium.generate_links') }}
                    </button>
                    <i v-if="generatingLinks" class="fa fa-spinner fa-pulse p-2"></i>

                </div>
                <div v-if="currentViewLink && currentDownloadLink"
                    id="link-buttons"
                    class="btn-group-vertical d-flex flex-column bg-light hide"
                >
                    <button
                        type="button"
                        class="btn btn-light"
                        @click="openLink(currentViewLink)"
                    >
                        <i class="fa fa-arrow-up-right-from-square"></i>
                        {{ trans('global.open') }}
                    </button>
                    <button
                        type="button"
                        class="btn btn-light"
                        @click="openLink(currentDownloadLink)"
                    >
                        <i class="fa fa-download"></i>
                        {{ trans('global.download') }}
                    </button>
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
            class="carousel-control-prev"
            style="z-index: 20;"
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
            style="z-index: 20;"
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
            generatingLinks: false,
            currentViewLink: null,
            currentDownloadLink: null,
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
        openLink(link) {
            if (link) window.open(link, '_blank');
        },
        getURLs() {
            this.generatingLinks = true;
            this.generateViewLink();
            this.generateDownloadLink();
        },
        generateViewLink() {
            axios.get('/media/' + this.subscriptions[this.currentSlide].medium.id + '?content=true')
                .then((response) => this.currentViewLink = response.data);
        },
        generateDownloadLink() {
            axios.get('/media/' + this.subscriptions[this.currentSlide].medium.id + '?download=true')
                .then((response) => this.currentDownloadLink = response.data);
        },
        setURLs() {
            const animationTime = 300;
            const linkValidTime = 5000; // edusharing links are only valid for a couple of seconds

            // to activate the transitions, we need to implement the logic through timeouts
            setTimeout(() => { // first we hide the loading-indicator
                const buttons = document.getElementById('link-buttons'); // element doesn't exist before
                this.generatingLinks = false;
                buttons.classList.remove('hide');

                setTimeout(() => { // then we show the actual links
                    buttons.classList.add('hide');

                    setTimeout(() => { // after the links aren't valid anymore, we reset everything
                        this.currentViewLink = null;
                        this.currentDownloadLink = null;
                    }, animationTime);
                }, linkValidTime);
            }, animationTime);
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
                this.subscriptions.splice(this.currentSlide, 1);
                if (this.subscriptions.length === 0) return;
                $('#' + this.id).carousel(0);
                this.currentSlide = 0;
            })
            .catch(err => {
                console.log(err.response);
            });
        },
    },
    computed: {
        URLsLoaded() {
            return this.currentViewLink !== null && this.currentDownloadLink !== null;
        },
    },
    watch: {
        URLsLoaded(loaded) {
            if (loaded) this.setURLs();
        },
    },
    components: {
        MediaRenderer,
    },
}
</script>
<style scoped>
#link-overlay {
    position: absolute;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    z-index: 10;
    transition: opacity 0.3s linear;

    & > #link-wrapper, & > #link-buttons {
        transition: width 0.3s ease, opacity 0.3s linear, box-shadow 0.3s ease;
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.25);

        &:hover { box-shadow: 4px 4px 5px rgba(0, 0, 0, 0.25); }
    }
    & > #link-buttons {
        position: absolute;
        z-index: 15;
        border-radius: 1rem;

        & > button:first-child { border-top-left-radius: 1rem; border-top-right-radius: 1rem; }
        & > button:last-child { border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem; }
    }
    &:hover > #link-wrapper, &.active > #link-wrapper { opacity: 1 !important; }
}
</style>