<template >
<div class="card">
    <div class="card-header px-3">
        <h3 class="card-title">
            <span data-target="#glossarCarousel"
                  data-slide-to="0"
                  class="text-sm">
                <i class="fa fa-list"></i>
            </span>
            <span v-if="currentSlide == 0"
                  class="pl-2">Glossar-Index</span>
            <span v-else
                  class="pl-2">{{subscriptions[currentSlide-1].content.title}}</span>
        </h3>
        <div class="card-tools">
            <button v-can="'glossar_delete'"
                    type="button" role="button"
                    class="btn btn-tool text-danger"
                    @click.prevent="deleteGlossar()">
                <i class="fa fa-trash "></i>
            </button>
            <button v-permission="'content_create, ' + subscribable_type + '_content_create'"
                    type="button" class="btn btn-tool "
                    role="button"
                    @click="show('content-create-modal')">
                <i class="fa fa-plus"></i>
            </button>
            <button type="button" class="btn btn-tool draggable"
                    href="#glossarCarousel" role="button"
                    data-slide="prev"
                    @click="prev()">
                <i class="fa fa-arrow-left"></i>
            </button>
            <button type="button" class="btn btn-tool draggable"
                    href="#glossarCarousel" role="button"
                    data-slide="next"
                    @click="next()"
                    >
                <i class="fa fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <div class="card-glossar">
        <div id="glossarCarousel" class="carousel slide" data-interval="false">
            <ol class="carousel-indicators pt-3">
                <li data-target="#glossarCarousel"
                    data-slide-to="0"
                    class="active"
                    @click="setSlide(0)"></li>
                <li v-for="(item,index) in subscriptions"
                    tooldata-toggle="tooltip"
                    data-placement="top"
                    :title="item.content.title"
                    data-target="#glossarCarousel"
                    :data-slide-to="index+1"
                    @click="setSlide(index+1)"
                ></li>
            </ol>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <ul class="list-unstyled p-3" title="Index">
                        <span v-for="(item,index) in subscriptions">
                             <li class="pb-2">
                                 <span class="pointer">
                                     <span data-target="#glossarCarousel"
                                           :data-slide-to="index+1"
                                           @click="setSlide(index+1)">
                                         {{item.content.title}}
                                     </span>
                                    <span v-permission="'content_delete, ' + subscribable_type + '_content_delete'"
                                          class="pull-right">
                                         <span
                                             class="btn-tool fa fa-trash text-danger"
                                             @click.prevent="deleteSubscription(item)"
                                         >
                                         </span>
                                     </span>
                                     <br>
                                     <small class="text-muted">
                                        {{item.content.content | truncate(200, '...')}}
                                     </small>
                                 </span>
                             </li>
                        </span>
                    </ul>
                </div>

                <div v-for="(item,index) in subscriptions"
                     class="carousel-item" :title="item.content.title">
                    <div class="p-3"
                         v-html="item.content.content"></div>
                </div>

            </div>
        </div>
    </div>
    <content-create-modal></content-create-modal>
</div>
</template>

<script>
    export default {
        props: {
            subscribable_type: String,
            subscription: {},
            glossar: {},
        },
        data() {
            return {
                subscriptions: {},
                errors: {},
                currentSlide: 0,
            }
        },
        methods: {
            show(modal){
                this.$modal.show(modal, { 'referenceable_type': 'App\\Glossar', 'referenceable_id': this.glossar.id/*, 'method': 'patch' */ });
            },
            setSlide(id){
                this.currentSlide = id;
            },
            prev(){
                if (this.currentSlide === 0){
                    this.currentSlide = this.subscriptions.length;
                } else {
                    this.currentSlide--;
                }
            },
            next(){
                if (this.currentSlide == this.subscriptions.length){
                    this.currentSlide = 0;
                } else {
                    this.currentSlide++;
                }
            },
            async deleteSubscription(contentSubscription){
                try {
                    await axios.post('/contents/' + contentSubscription.content_id + '/destroy', {
                        'referenceable_type': contentSubscription.subscribable_type,
                        'referenceable_id': contentSubscription.subscribable_id
                    });
                    let index = this.subscriptions.indexOf(contentSubscription);
                    this.subscriptions.splice(index, 1);
                }
                catch(error) {
                    this.errors = error.response.data.errors;
                }
            },
            async deleteGlossar(){
                if (confirm(this.trans('global.areYouSure'))) {
                    try {
                        await axios.delete('/glossar/' + this.glossar.id);
                    } catch (error) {
                        this.errors = error.response.data.errors;
                    }
                    location.reload();
                }
            },
            loaderEvent() {
                axios.get('/glossar/' + this.glossar.id)
                    .then(response => {
                        this.subscriptions = response.data.message;
                    })
                    .catch(e => {
                        this.errors = error.response.data.errors;
                    });
            },
        },

        beforeMount() {
            this.loaderEvent();
        },
        mounted() {
            this.currentSlide = 0;
            this.$on('addContent', function (newContent) {
                this.loaderEvent();
            });
        }
    }
</script>
