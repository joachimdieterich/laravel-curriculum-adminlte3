<template>
    <modal
        id="subscribe-modal"
        name="subscribe-modal"
        height="auto"
        :adaptive=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen"
        @opened="opened"
        @before-close="beforeClose"
        style="z-index: 1100">
        <div class="card"
             style="margin-bottom: 0px !important">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa fa-share-alt text-secondary mr-3"></i>{{ trans('global.share') }}
                </h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool draggable" >
                        <i class="fa fa-arrows-alt"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-widget="remove" @click="close()">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                <ul class="nav nav-pills">
                  <!-- User -->
                  <li class="nav-item" v-can="'kanban_create'">
                      <a class="nav-link active show" href="#user_subscription" data-toggle="tab">
                          <i class="fa fa-user mr-3"></i>{{ trans('global.user.title') }}
                      </a>
                  </li>
                  <!-- Group -->
                  <li class="nav-item" v-can="'kanban_create'">
                      <a class="nav-link" href="#group_subscription" data-toggle="tab">
                          <i class="fa fa-users mr-3"></i>{{ trans('global.group.title') }}
                      </a>
                  </li>
                  <!-- Organization -->
                  <li class="nav-item" v-can="'kanban_create'">
                      <a class="nav-link" href="#organization_subscription" data-toggle="tab" >
                          <i class="fa fa-university mr-3"></i>{{ trans('global.organization.title') }}
                      </a>
                  </li>
                  <!-- Global -->
<!--                  <li class="nav-item" v-can="'kanban_create'">
                      <a class="nav-link" href="#global_subscription" data-toggle="tab" >
                          <i class="sc-icon-dd icon-curriculum text-secondary mr-3"></i>{{ trans('global.all') }}
                      </a>
                  </li>-->
                </ul>

                <div class="tab-content pt-2">
                    <!-- User Tab -->
                    <div class="tab-pane active show" id="user_subscription" v-can="'kanban_create'">
                         <div class="form-group pt-2">
                            <select name="users"
                                    id="users"
                                    class="form-control select2 "
                                    style="width:100%;"
                                    >
                                 <option></option>
                                 <option v-for="(item,index) in subscribers.users" v-bind:value="item.id">{{ item.firstname }} {{ item.lastname }}</option>
                            </select>
                        </div>
                        <subscribers
                            :modelUrl="modelUrl"
                            :subscriptions="subscribers.subscriptions"
                            :subscribing_model="'App\\User'"/>

                    </div>


                    <!-- Group Tab -->
                    <div class="tab-pane" id="group_subscription" v-can="'kanban_create'">
                         <div class="form-group pt-2 ">
                            <select name="groups"
                                    id="groups"
                                    class="form-control select2 "
                                    style="width:100%;"
                                    >
                                 <option></option>
                                 <option v-for="(item,index) in subscribers.groups" v-bind:value="item.group_id">{{ item.title }}</option>
                            </select>
                        </div>
                        <subscribers
                            :modelUrl="modelUrl"
                            :subscriptions="subscribers.subscriptions"
                            :subscribing_model="'App\\Group'"/>

                    </div>

                    <!-- Organization Tab -->
                    <div class="tab-pane" id="organization_subscription" v-can="'kanban_create'">
                         <div class="form-group pt-2">
                            <select name="organizations"
                                    id="organizations"
                                    class="form-control select2 "
                                    style="width:100%;"
                                    >
                                 <option></option>
                                 <option v-for="(item,index) in subscribers.organizations" v-bind:value="item.organization_id">{{ item.title }}</option>
                            </select>
                        </div>



                        <subscribers
                            :modelUrl="modelUrl"
                            :subscriptions="subscribers.subscriptions"
                            :subscribing_model="'App\\Organization'"/>
                    </div>

                    <!-- Global Tab -->
<!--                    <div class="tab-pane" id="global_subscription" v-can="'kanban_create'">
                         <div class="form-group pt-2">
                            select global
                        </div>

                    </div>-->
                </div>

            </div>
            <div class="card-footer">
                <span class="pull-right">
                    <button type="button" class="btn btn-default" data-widget="remove" @click="close()">{{ trans('global.close') }}</button>
                </span>
            </div>
        </div>
    </modal>
</template>

<script>
    import subscribers from "./Subscribers";
    export default {

        data() {
            return {
                modelUrl: null,
                modelId: null,
                subscribable_id: null,
                subscribable_type: null,

                subscribers: Object,
                hover: false,
            };
        },
        methods: {
            beforeOpen(event) {

                this.modelUrl =  event.params.modelUrl;
                this.modelId  =  event.params.modelId;
                this.loadSubscribers();
            },
            beforeClose() {
            },

            opened(){
                this.initSelect2();
            },

            close(){
                this.$modal.hide('subscribe-modal');
            },
            async loadSubscribers() {
                try {
                    this.subscribers = (await axios.get('/' + this.modelUrl + 'Subscriptions?' + this.modelUrl + '_id='+this.modelId)).data.subscribers;
                } catch(error) {
                    this.errors = error.response.data.errors;
                }
            },
            initSelect2(){
                $("#users").select2({
                    dropdownParent: $("#users").parent(),
                    allowClear: false
                }).on('select2:select', function (e) {
                    this.subscribe('App\\User', e.params.data.id, this.modelId);
                }.bind(this));
                $("#groups").select2({
                    dropdownParent: $("#groups").parent(),
                    allowClear: false
                }).on('select2:select', function (e) {
                    this.subscribe('App\\Group', e.params.data.id, this.modelId);
                }.bind(this));
                $("#organizations").select2({
                    dropdownParent: $("#organizations").parent(),
                    allowClear: false
                }).on('select2:select', function (e) {
                    this.subscribe('App\\Organization', e.params.data.id, this.modelId);
                }.bind(this));
            },
            async subscribe(subscribable_type, subscribable_id, model_id) {
                try {
                    this.subscribers.subscriptions = (await axios.post('/' + this.modelUrl + 'Subscriptions', {
                        'model_id':          model_id,
                        'subscribable_type': subscribable_type,
                        'subscribable_id':   subscribable_id
                    })).data.subscription;

                } catch(error) {
                    //
                }
            }

        },
        components: {
            subscribers,
        }

    }
</script>
