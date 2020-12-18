<template >
<div class="card">
    <div class="card-header px-3">
        <h3 class="card-title">
            <span data-target="#contentCarousel"
                  data-slide-to="0"
                  class="text-sm">
                <i class="fa fa-list"></i>
            </span>
            <span v-if="currentSlide == 0"
                  class="pl-2">Index</span>
            <span v-else
                  class="pl-2">{{subscriptions[currentSlide-1].content.title}}</span>
        </h3>
        <div class="card-tools">
            <button v-can="'content_create'"
                    type="button" class="btn btn-tool "
                    role="button"
                    @click="show('content-create-modal')">
                <i class="fa fa-plus"></i>
            </button>
            <button v-can="'content_create'"
                    type="button" class="btn btn-tool "
                    role="button"
                    @click="show('content-subscription-modal')">
                <i class="fa fa-paste"></i>
            </button>
            <button type="button" class="btn btn-tool "
                    href="#contentCarousel" role="button"
                    data-slide="prev"
                    @click="prev()">
                <i class="fa fa-arrow-left"></i>
            </button>
            <button type="button" class="btn btn-tool "
                    href="#contentCarousel" role="button"
                    data-slide="next"
                    @click="next()">
                <i class="fa fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <div class="card-content">
        <div id="contentCarousel" class="carousel slide" data-interval="false">
            <ol class="carousel-indicators">
                <li data-target="#contentCarousel"
                    data-slide-to="0"
                    class="active"
                    @click="setSlide(0)"></li>
                <li v-for="(item,index) in subscriptions"
                    tooldata-toggle="tooltip"
                    data-placement="top"
                    :title="item.content.title"
                    data-target="#contentCarousel"
                    :data-slide-to="index+1"
                    @click="setSlide(index+1)"
                ></li>

            </ol>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <ul class="list-unstyled p-3" title="Index">
                        <span v-for="(item,index) in subscriptions">
                             <li class="pb-2"
                                 >
                                 <span class="pointer">
                                     <span data-target="#contentCarousel"
                                           :data-slide-to="index+1"
                                           @click="setSlide(index+1)">
                                         {{item.content.title}}
                                     </span>
                                     <span v-can="'content_delete'"
                                           class="pull-right">
                                         <span
                                             class="btn-tool fa fa-trash text-danger"
                                             @click.prevent="deleteSubscription(item)"
                                             >
                                         </span>
                                    </span>
                                     <span v-can="'content_create'"
                                           class="pull-right"><!--Order_id: {{ item.order_id }}-->
                                         <span v-if="(item.order_id != 0)"
                                               class="btn-tool fa fa-arrow-up"
                                               @click.prevent="sortEvent(item,1)">
                                         </span>

                                        <span  v-if="( subscriptions.length-1 != item.order_id)"
                                               class="btn-tool fa fa-arrow-down"
                                               @click.prevent="sortEvent(item,-1)"
                                               >
                                        </span>
                                    </span>

                                     <br>
                                     <small class="text-muted"
                                            data-target="#contentCarousel"
                                            :data-slide-to="index+1"
                                            @click="setSlide(index+1)">
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
    <content-subscription-modal
        :subscribable_type="subscribable_type"
        :subscribable_id="subscribable_id"
    ></content-subscription-modal>
    </div>
</template>

<script>
    export default {
        props: {
            subscription: {},
            subscribable_type: '',
            subscribable_id: '',
            medium: {},
            format: ''
        },
        data() {
            return {
                subscriptions: {},
                errors: {},
                currentSlide: 0,
                currentContent: 'Index'
            }
        },
        methods: {
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
            show(modal){
                this.$modal.show(modal, { 'referenceable_type': this.subscribable_type, 'referenceable_id': this.subscribable_id /*, 'method': 'patch' */ });
            },
            async sortEvent(contentSubscription,amount) {
                let subscription = {
                    'subscribable_type': contentSubscription.subscribable_type,
                    'subscribable_id':   contentSubscription.subscribable_id,
                    'content_id':        contentSubscription.content_id,
                    'order_id':          contentSubscription.order_id + parseInt(amount)
                }
                //console.log(JSON.stringify(objective));
                try {
                    this.subscriptions = (await axios.patch('/contentSubscriptions/', subscription)).data.message;
                } catch(error) {
                    this.errors = error.response.data.errors;
                }
            },
            async deleteSubscription(contentSubscription){
                try {
                    await axios.post('/contents/'+contentSubscription.content_id+'/destroy',  { 'referenceable_type': contentSubscription.subscribable_type, 'referenceable_id': contentSubscription.subscribable_id } );
                }
                catch(error) {
                    this.errors = error.response.data.errors;
                }
                location.reload();
            },

        },


        beforeMount() {
             axios.get('/contentSubscriptions?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id)
                  .then(response => {
                      this.subscriptions = response.data.message;
                  })
                 .catch(e => {
                     this.errors = error.response.data.errors;
                });
        },

    }
</script>
