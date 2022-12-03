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
        style="z-index: 1100"
    >
        <div class="card"
             style="margin-bottom: 0px !important">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa fa-share-alt text-secondary mr-3"></i>{{ trans('global.share') }}
                </h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool draggable">
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
                    <li v-if="shareWithUsers"
                        class="nav-item">
                        <a class="nav-link active show" href="#user_subscription" data-toggle="tab">
                            <i class="fa fa-user mr-3"></i>{{ trans('global.user.title') }}
                        </a>
                    </li>
                    <!-- Group -->
                    <li v-if="shareWithGroups"
                        class="nav-item">
                        <a class="nav-link" href="#group_subscription" data-toggle="tab">
                            <i class="fa fa-users mr-3"></i>{{ trans('global.group.title') }}
                        </a>
                    </li>
                    <!-- Organization -->
                    <li v-if="shareWithOrganizations"
                        class="nav-item">
                        <a class="nav-link" href="#organization_subscription" data-toggle="tab">
                            <i class="fa fa-university mr-3"></i>{{ trans('global.organization.title') }}
                        </a>
                    </li>
                    <li v-if="shareWithToken"
                        class="nav-item">
                        <a class="nav-link" href="#token_subscription" data-toggle="tab">
                            <i class="fa fa-key mr-3"></i>{{ trans('global.token') }}
                        </a>
                    </li>
                    <!-- Global -->
                    <!--                  <li class="nav-item" >
                                          <a class="nav-link" href="#global_subscription" data-toggle="tab" >
                                              <i class="sc-icon-dd icon-curriculum text-secondary mr-3"></i>{{ trans('global.all') }}
                                          </a>
                                      </li>-->
                </ul>

                <div class="tab-content pt-2">
                    <!-- User Tab -->
                    <div v-if="shareWithUsers"
                         class="tab-pane active show" id="user_subscription">
                        <div class="form-group pt-2">
                            <select name="users"
                                    id="users"
                                    class="form-control select2 "
                                    style="width:100%;"
                            ></select>
                        </div>
                        <subscribers
                            v-if="typeof subscribers.subscriptions != 'undefined'"
                            :modelUrl="modelUrl"
                            :subscriptions="subscribers.subscriptions"
                            :subscribing_model="'App\\User'"/>

                    </div>

                    <!-- Group Tab -->
                    <div v-if="shareWithGroups"
                         class="tab-pane" id="group_subscription">
                        <div class="form-group pt-2 ">
                            <select name="groups"
                                    id="groups"
                                    class="form-control select2 "
                                    style="width:100%;"
                            >
                            </select>
                        </div>
                        <subscribers
                            v-if="typeof subscribers.subscriptions != 'undefined'"
                            :modelUrl="modelUrl"
                            :subscriptions="subscribers.subscriptions"
                            :subscribing_model="'App\\Group'"/>
                    </div>

                    <!-- Organization Tab -->
                    <div v-if="shareWithOrganizations"
                         class="tab-pane" id="organization_subscription">
                        <div class="form-group pt-2">
                            <select name="organizations"
                                    id="organizations"
                                    class="form-control select2 "
                                    style="width:100%;"
                            >
                            </select>
                        </div>
                        <subscribers
                            v-if="typeof subscribers.subscriptions != 'undefined'"
                            :modelUrl="modelUrl"
                            :subscriptions="subscribers.subscriptions"
                            :subscribing_model="'App\\Organization'"/>
                    </div>

                    <!-- Token Tab -->
                    <div v-if="shareWithToken"
                         class="tab-pane" id="token_subscription">
                        <div class="form">
                            <div class="form-group pt-2">
                                <input v-model="nameToken" class="form-control mb-2" style="width: 100%;"
                                       placeholder="Name">
                                <date-picker v-model="endDateToken" style="width:100%;"
                                             placeholder="Ablaufdatum"></date-picker>
                            </div>
                            <small>darf bearbeiten</small>
                            <span class="pull-right custom-control custom-switch custom-switch-on-green">
                                <input v-model="canEditToken"
                                       type="checkbox"
                                       id="canEditToken"
                                       class="custom-control-input pt-1 "
                                       @click="changeCanEditTokenValue(canEditToken)">
                                <label class="custom-control-label " for="canEditToken"></label>
                            </span>
                            <div>
                                <button type="button" @click="createUserToken()" :disabled="nameToken == ''"
                                        class="btn btn-sm btn-outline-success pull-right mt-3">
                                    Speichern
                                </button>
                            </div>
                        </div>
                        <div>
                        <tokens
                            v-if="typeof subscribers.subscriptions != 'undefined'"
                            :modelUrl="modelUrl"
                            :subscriptions="subscribers.tokens"/>
                        </div>

                    </div>

                    <!-- Global Tab -->
                    <!--                    <div class="tab-pane" id="global_subscription" >
                                             <div class="form-group pt-2">
                                                select global
                                            </div>

                                        </div>-->
                </div>

            </div>
            <div class="card-footer">
                <span class="pull-right">
                    <button type="button" class="btn btn-default" data-widget="remove"
                            @click="close()">{{ trans('global.close') }}</button>
                </span>
            </div>
        </div>
    </modal>
</template>

<script>
import subscribers from "./Subscribers";
import tokens from "./Tokens";
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

export default {
    data() {
        return {
            modelUrl: null,
            modelId: null,
            subscribable_id: null,
            subscribable_type: null,
            endDateToken: null,
            subscribers: Object,
            hover: false,
            canEditToken: false,
            shareWithUsers: true,
            shareWithGroups: true,
            shareWithOrganizations: true,
            shareWithToken: false,
            nameToken: ''
        };
    },
    methods: {
        beforeOpen(event) {
            this.resetComponent();
            this.modelUrl = event.params.modelUrl;
            this.modelId = event.params.modelId;
            if (event.params.shareWithUsers == false) {
                this.shareWithUsers = event.params.shareWithUsers;
            }
            if (event.params.shareWithGroups == false) {
                this.shareWithGroups = event.params.shareWithGroups;
            }
            if (event.params.shareWithOrganizations == false) {
                this.shareWithOrganizations = event.params.shareWithOrganizations;
            }
            if (event.params.shareWithToken == true) {
                this.shareWithToken = event.params.shareWithToken;
            }
            this.loadSubscribers();
        },
        beforeClose() {
        },
        opened() {
            this.initSelect2();
        },
        changeCanEditTokenValue(value) {
            this.canEditToken = !value;
        },
        close() {
            this.$modal.hide('subscribe-modal');
        },
        async loadSubscribers() {
            try {
                this.subscribers = (await axios.get('/' + this.modelUrl + 'Subscriptions?' + this.modelUrl + '_id=' + this.modelId)).data.subscribers;
            } catch (error) {
                this.errors = error.response.data.errors;
            }
        },
        initSelect2() {
            $("#users").select2({
                dropdownParent: $(".v--modal-overlay"),
                allowClear: false,
                    ajax: {
                        url: "/users",
                        dataType: 'json',
                        data: function(params) {
                            return {
                                term: params.term || '',
                                page: params.page || 1
                            }
                        },
                        cache: true
                    },
                }).on('select2:select', function (e) {
                    this.subscribe('App\\User', e.params.data.id, this.modelId);
                }.bind(this));
                $("#groups").select2({
                    dropdownParent: $(".v--modal-overlay"),
                    allowClear: false,
                    ajax: {
                        url: "/groups",
                        dataType: 'json',
                        data: function(params) {
                            return {
                                term: params.term || '',
                                page: params.page || 1
                            }
                        },
                        cache: true
                    },
                }).on('select2:select', function (e) {
                    this.subscribe('App\\Group', e.params.data.id, this.modelId);
                }.bind(this));
                $("#organizations").select2({
                    dropdownParent: $(".v--modal-overlay"),
                    allowClear: false,
                    ajax: {
                        url: "/organizations",
                        dataType: 'json',
                        data: function(params) {
                            return {
                                term: params.term || '',
                                page: params.page || 1
                            }
                        },
                        cache: true
                    },
                }).on('select2:select', function (e) {
                    this.subscribe('App\\Organization', e.params.data.id, this.modelId);
                }.bind(this));
            },
            async subscribe(subscribable_type, subscribable_id, model_id) {
                try {
                    this.subscribers.subscriptions = (await axios.post('/' + this.modelUrl + 'Subscriptions', {
                        'model_id': model_id,
                        'subscribable_type': subscribable_type,
                        'subscribable_id': subscribable_id
                    })).data.subscription;
                } catch (error) {
                    console.log(error);
            }
        },
        resetComponent() {
            this.modelUrl = null;
            this.modelId = null;
            this.subscribable_id = null;
            this.subscribable_type = null;

            this.subscribers = Object;
            this.hover = false;

            this.shareWithUsers = true;
            this.shareWithGroups = true;
            this.shareWithOrganizations = true;
        },
        createUserToken() {
            axios.post('/' + this.modelUrl + '/token', {
                'model_id': this.modelId,
                'name': this.nameToken,
                'date': this.endDateToken,
                'can_change': this.canEditToken
            }).then( () => this.loadSubscribers())
        }
    },
    components: {
        subscribers,
        tokens,
        DatePicker
    }
}
</script>
