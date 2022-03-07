<template>
    <div :id="id"
         class="carousel slide ignore"
         data-interval="false">

        <span class="carousel-indicators">
            <li v-for="(item, index) in items"
                v-if="items.length > 1"
                :class="{ 'active': index === 0 }"
                :data-target="'#'+id"
                :data-slide-to="index"
                @click="setSlide(index)">
            </li>
            <a class="pl-2 text-muted text-danger"
               @click="unlinkMedium()">
                <i class="fa fa-trash"></i>
            </a>
        </span>
        <div class="carousel-inner">
            <div v-for="(item, index) in items"
                 :class="{ 'active': index === 0 }"
                 class="carousel-item">
                <medium-renderer
                    :medium="item.medium"
                    :width="width"
                ></medium-renderer>
            </div>

        </div>
        <a class="carousel-control-prev "
           v-if="items.length > 1"
           :href="'#'+id"
           @click="prev()"
           :aria-label="trans('pagination.previous')"
           role="button"
           data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only ignore">Previous</span>
        </a>
        <a class="carousel-control-next"
           v-if="items.length > 1"
           :href="'#'+id"
           role="button"
           data-slide="next"
           :aria-label="trans('pagination.next')"
           @click="next()">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only ignore">Next</span>
        </a>
    </div>
</template>

<script>
import mediumRenderer from '../media/MediaRenderer';

export default {
    props: {
        'subscriptions': Array,
        'width': Number
    },
    data() {
        return {
            id: null,
            currentSlide: 0,
            items: {},
            errors: {},
        }
    },
    mounted() {
        this.id = 'carousel_' + this._uid
        this.items = this.subscriptions;
    },
    methods: {
        setSlide(id) {
            this.currentSlide = id;
        },
        prev() {
            if (this.currentSlide === 0) {
                this.currentSlide = this.items.length;
            } else {
                this.currentSlide--;
            }
        },
        next() {
            if (this.currentSlide === this.items.length) {
                this.currentSlide = 0;
            } else {
                this.currentSlide++;
            }
        },
        unlinkMedium() { //id of external reference and value in db
            axios.post('/mediumSubscriptions/destroy', this.items[this.currentSlide])
                .then(res => {
                    this.items.splice(this.currentSlide, 1);
                    if (this.items.length == this.currentSlide) {
                        $('#' + this.id).carousel('prev');
                    }
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
    },

    components: {
        mediumRenderer,
    }
}
</script>
