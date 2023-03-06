<template>
    <div :id="id"
         class="carousel slide ignore"
         data-interval="false">

        <span class="carousel-indicators">
            <li v-for="(item, index) in subscriptions"
                v-if="subscriptions.length > 1"
                :class="{ 'active': index === 0 }"
                :data-target="'#'+id"
                :data-slide-to="index"
                @click="setSlide(index)">
            </li>
        </span>
        <div class="carousel-inner">
            <div v-for="(item, index) in subscriptions"
                 :class="{ 'active': index === 0 }"
                 class="carousel-item">
                <medium-renderer
                    :medium="item.medium"
                    :width="width"
                ></medium-renderer>
                <a v-if="$userId == item.medium.owner_id"
                   class="carousel-indicators-destroy pl-3 text-muted text-danger"
                   @click.prevent="unlinkMedium(item)">
                    <i class="fa fa-trash"></i>
                </a>
            </div>

        </div>
        <a class="carousel-control-prev "
           v-if="subscriptions.length > 1"
           :href="'#'+id"
           @click="prev()"
           :aria-label="trans('pagination.previous')"
           role="button"
           data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only ignore">Previous</span>
        </a>
        <a class="carousel-control-next"
           v-if="subscriptions.length > 1"
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
        'width': Number,
    },
    data() {
        return {
            id: null,
            currentSlide: 0,
            errors: {},
        }
    },
    mounted() {
        this.id = 'carousel_' + this._uid
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
        unlinkMedium(item) { //id of external reference and value in db
            axios.delete('/media/'+item.medium.id, {
                data: {
                    subscribable_type: item.subscribable_type,
                    subscribable_id: item.subscribable_id }
            })
                .then(res => {
                    //console.log(res);
                    this.$eventHub.$emit('reload_kanban_item', {id: item.subscribable_id });
                    this.subscriptions.splice(item, 1);
                    if (this.currentSlide == 0) {
                        $('#' + this.id).carousel('prev');
                    } else {
                        $('#' + this.id).carousel('next');
                    }
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
    },
    watch: {
        subscriptions(newSubscriptions, oldSubscriptions){
            if (newSubscriptions.length > oldSubscriptions.length){
                this.setSlide(newSubscriptions.length -1)
            }
        }
    },

    components: {
        mediumRenderer,
    }
}
</script>
