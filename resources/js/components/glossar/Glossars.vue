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
                             <li class="pb-2"
                                 data-target="#glossarCarousel"
                                 :data-slide-to="index+1"
                                 @click="setSlide(index+1)">
                                 <span class="pointer">
                                     {{item.content.title}}<br>
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

    </div>
</template>

<script>
    export default {
        props: {
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
            }
        },
        beforeMount() {
             axios.get('/glossar/'+this.glossar.id)
                  .then(response => {
                      this.subscriptions = response.data.message;
                  })
                 .catch(e => {
                     this.errors = error.response.data.errors;
                });
        },
    }
</script>
