<template >
<div class="card"
     style="border: 0 !important">
    <div class="card-header px-3"
    >
        <h3 class="card-title"
            v-if="subscriptions.length !== 0">
            <span :data-target="'#contentCarousel_'+uid"
                  data-slide-to="0"
                  class="text-sm">
                <i class="fa fa-list"></i>
            </span>
            <span v-if="currentSlide === 0"
                  class="pl-2">Index</span>
            <span v-else
                  class="pl-2">{{subscriptions[currentSlide-1].content.title}}
            </span>
        </h3>
        <h3 v-else class="card-title">
            <span class="pl-2">
                {{ trans('global.content.no_content') }}
            </span>
        </h3>
        <div class="card-tools">
            <button v-permission="'content_create, ' + subscribable_type + '_content_create'"
                    type="button" class="btn btn-tool "
                    role="button"
                    :aria-label="trans('global.add')"
                    @click="show('content-create-modal')">
                <i class="fa fa-plus"></i>
            </button>
            <button v-permission="'content_create, ' + subscribable_type + '_content_create'"
                    v-if="subscribable_type === 'App\\Curriculum'"
                    type="button" class="btn btn-tool "
                    role="button"
                    :aria-label="trans('global.paste')"
                    @click="show('content-subscription-modal')">
                <i class="fa fa-paste"></i>
            </button>
            <button v-permission="'content_create, ' + subscribable_type + '_content_create'"
                    type="button" class="btn btn-tool "
                    role="button"
                    :aria-label="trans('global.resetOrder')"
                    data-toggle="tooltip" data-container="body" :title="trans('global.resetOrder')"
                    @click.prevent="fixOrderIds()">
                <i class="fa fa-wrench"></i>
            </button>
            <button
                v-if="subscriptions.length !== 0"
                type="button" class="btn btn-tool "
                :href="'#contentCarousel_'+uid" role="button"
                data-slide="prev"
                :aria-label="trans('pagination.previous')"
                @click="prev()">
                <i class="fa fa-arrow-left"></i>
            </button>
            <button
                v-if="subscriptions.length !== 0"
                type="button" class="btn btn-tool "
                :href="'#contentCarousel_'+uid" role="button"
                data-slide="next"
                :aria-label="trans('pagination.next')"
                @click="next()">
                <i class="fa fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <div class="card-content"  v-if="subscriptions.length !== 0">
        <div :id="'contentCarousel_'+uid" class="carousel slide" data-interval="false">
            <ol class="carousel-indicators">
                <li :data-target="'#contentCarousel_'+uid"
                    data-slide-to="0"
                    class="active"
                    @click="setSlide(0)"></li>
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
                                        @click="setSlide(index+1)">
                                    {{item.content.content | truncate(200, '...')}}
                                 </small>
                             </span>
                         </li>
                    </ul>
                </div>

                <div v-for="item in subscriptions"
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
    <content-create-modal></content-create-modal>
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
                uid: null,
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
                if (this.currentSlide === this.subscriptions.length){
                    this.currentSlide = 0;
                } else {
                    this.currentSlide++;
                }
            },
            show(modal){
                this.$modal.show(modal, {
                    'referenceable_type': this.subscribable_type,
                    'referenceable_id': this.subscribable_id,
                    'method': 'post'
                });
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
            async fixOrderIds() {
                let subscription = {
                    'subscribable_type': this.subscribable_type,
                    'subscribable_id':   this.subscribable_id,
                }

                try {
                    this.subscriptions = (await axios.patch('/contentSubscriptions/reset', subscription)).data.message;
                } catch(error) {
                    this.errors = error.response.data.errors;
                }
            },
            edit(contentSubscription){
                this.$modal.show('content-create-modal', {
                    'id': contentSubscription.content_id,
                    'method': 'patch',
                    'referenceable_type': contentSubscription.subscribable_type,
                    'referenceable_id': contentSubscription.subscribable_id
                });
            },
            async deleteSubscription(contentSubscription){
                try {
                    await axios.post('/contents/'+contentSubscription.content_id+'/destroy',  { 'referenceable_type': contentSubscription.subscribable_type, 'referenceable_id': contentSubscription.subscribable_id } );
                    // remove on page
                    let index = this.subscriptions.indexOf(contentSubscription);
                    this.subscriptions.splice(index, 1);
                }
                catch(error) {
                    this.errors = error.response.data.errors;
                }
            },
            loaderEvent(){
                axios.get('/contentSubscriptions?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id)
                    .then(response => {
                        this.subscriptions = response.data.message;
                    })
                    .catch(e => {
                        this.errors = e.data.errors;
                    });
            }
        },
        mounted() {
            this.uid = this._uid;
            this.currentSlide = 0;
            this.$on('addContent', function(newContent) {
                this.loaderEvent();
            });
            MathJax.startup.defaultReady();
        }

    }
</script>
