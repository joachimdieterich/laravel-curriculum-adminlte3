<template>
    <div>
        <div class="row">
<!--            <div class="col-12">
                <div class="form-group "
                     :class="errors.title ? 'has-error' : ''"
                >
                    <label for="title">{{ trans('global.externalRepositorySubscription.search') }}</label>
                    <input
                        type="text" id="search"
                        name="search"
                        class="form-control"
                        v-model="search"
                        required
                        @keyup.enter="getSearch()"
                    />

                    <p class="help-block" v-if="errors.searc" v-text="errors.search[0]"></p>
                </div>
                <div >
                    <ul class="nav flex-column">
                        <li v-if="searchResults !== ''">{{ searchResults }} {{ trans('global.entries') }}
                            <span class="pull-right custom-control custom-switch custom-switch-on-green">
                                <input  :checked="allSubscribed"
                                        type="checkbox"
                                        class="custom-control-input pt-1 "
                                        id="subscription_input_all"
                                        @click="subscribeAll">
                                <label class="custom-control-label " for="subscription_input_all" ></label>
                            </span>
                        </li>
                        <li v-for="medium in media"
                            :id="medium.ref.id"
                            class="nav-item" >
                            <a :href="medium.contentUrl"
                               class="link-muted"
                               target="_blank">
                                {{medium.name}}
                                <span class="pull-right custom-control custom-switch custom-switch-on-green">
                                    <input  :checked="getSubscriptionStatus(medium.ref.id)"
                                            type="checkbox"
                                            class="custom-control-input pt-1 "
                                            :id="'subscription_input'+medium.ref.id"
                                            @click="setSubscription(medium.ref.id)">
                                    <label class="custom-control-label " :for="'subscription_input'+medium.ref.id" ></label>
                                </span>

                            </a>
                        </li>
                    </ul>
                    <div v-if="media !== null" class="row pt-1" style="width:100% !important;">
                        <span class="col-6">
                           <button type="button"
                                   class="btn btn-block btn-primary"
                                   :class="page > 0 ? '' : 'disabled'"
                                   @click="lastPage()"><i class="fa fa-arrow-left"></i></button>
                       </span>

                        <span class="col-6">
                           <button type="button"
                                   class="btn btn-block btn-primary"
                                   :class="media[0].length == maxItems ? '' : 'disabled'"
                                   @click="nextPage()"><i class="fa fa-arrow-right"></i></button>
                       </span>

                    </div>
                </div>
            </div>-->
            <div class="col-12">
                <iframe
                    id="eduSharingFrame"
                    :src="`https://cloud.schulcampus-rlp.de/edu-sharing/components/upload?reurl=IFRAME`"
                    width="100%"
                    height="650"
                    frameborder="0">
                </iframe>
            </div>
        </div>
    </div>
</template>


<script>

export default {
    props: {
        'model': {}
    },
    data() {
        return {
            media: null,
            errors: {},
            search: '',
            subscriptions: Object,
            allSubscribed: false,
            page:    0,
            maxItems: 10,
        };
    },
    methods: {
        linkMedium(id) {
            axios.post('/repositorySubscriptions', {
                value: id,
                subscribable_id: this.model.subscribable_id,
                subscribable_type: this.model.subscribable_type,
                repository: 'edusharing'
            })
                .then(res => {
                    this.subscriptions.push(res.data.subscription);
                })
                .catch(error => { // Handle the error returned from our request
                    console.log(error.response);
                });
        },
        unlinkMedium(id) {
            axios.post('/repositorySubscriptions/destroySubscription', {
                value: id,
                subscribable_id: this.model.subscribable_id,
                subscribable_type: this.model.subscribable_type,
                repository: 'edusharing'
            })
                .then(res => {
                    let index = this.subscriptions.indexOf(id);

                    this.subscriptions.splice(index, 1);
                })
                .catch(error => { // Handle the error returned from our request
                    console.log(error.response);
                });
        },
        getSubscriptionStatus(id){
            return this.subscriptions.filter(subscription => subscription.value === id).length;
        },
        setSubscription(id) {
            if(this.getSubscriptionStatus(id) === 0){
                this.linkMedium(id);
                return true;
            } else {
                this.unlinkMedium(id);
                return false;
            }
        },

        subscribeAll(){
            var index;
            for (index = 0; index < this.media.length; ++index) {
                this.allSubscribed = this.setSubscription(this.media[index].ref.id);
            }
        },
        getSearch() {
            axios.post('/repositorySubscriptions/searchRepository', {
                value:this.search,
                page: this.page,
                maxItems: this.maxItems,
                repository: 'edusharing'
            })
                .then(res => {
                    this.media = res.data.nodes;
                    var index, status;
                    status = true;
                    for (index = 0; index < this.media.length; ++index) {
                        if (this.getSubscriptionStatus(this.media[index].ref.id) < 1){
                            status = false;
                            break;
                        }
                    }
                    this.allSubscribed = status;
                })
                .catch(error => { // Handle the error returned from our request
                    console.log(error.response);
                });
        },

        subscribable_type() {
            var reference_class = 'App\\TerminalObjective';
            if (typeof this.model.terminal_objective === 'object'){
                reference_class = 'App\\EnablingObjective';
            }
            return reference_class;
        },

        show(medium) {
            window.open(medium.path, '_blank');
        },
        href(medium) {
            return medium.thumb;
        },
        lastPage() {
            this.page = this.page - 1
            if (this.page == -1){
                this.page = 0;
            } else{
                this.getSearch();
            }

        },
        nextPage() {
            this.page = this.page + 1;
            this.getSearch  ();
        },
        receiveMessage(event) {
            if(event.data.event === 'APPLY_NODE') {
                console.log(event.data.data); // this will look similar to an API response structure of an object
                this.linkMedium(event.data.data.ref.id);
            }
        }

    },

    computed: {
        searchResults: function () {
            return (this.media ? this.media.length : '');
        }
    },

    mounted() {
        axios.get('/repositorySubscriptions', {
            params: {
                subscribable_type: this.model.subscribable_type,
                subscribable_id:   this.model.subscribable_id,
                repository: 'edusharing'
            }
        })
            .then(response => {
                this.subscriptions = response.data.subscriptions;
            })
            .catch(e => {
                this.errors = error.response.data.errors;
            });

        // add eventListener
        window.addEventListener("message", this.receiveMessage, false);
    }

}
</script>
