<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @click.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa fa-share-alt text-secondary mr-3"></i>
                        {{ trans('global.share') }}
                    </h3>
                    <div class="card-tools">
                        <button
                            type="button"
                            class="btn btn-tool"
                            data-widget="remove"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>

                <div
                    class="modal-body"
                    style="overflow-y: unset;"
                >
                    <div
                        class="card"
                        style="max-height: inherit;"
                    >
                        <div class="card-body overflow-auto">
                            <ul class="nav nav-pills nav-fill">
                                <!-- User -->
                                <li v-if="shareWithUsers"
                                    class="nav-item"
                                >
                                    <a
                                        class="nav-link active show"
                                        href="#user_subscription"
                                        data-toggle="tab"
                                    >
                                        <i class="fa fa-user mr-2"></i>{{ trans('global.user.title') }}
                                    </a>
                                </li>
                                <!-- Group -->
                                <li v-if="shareWithGroups"
                                    class="nav-item"
                                >
                                    <a
                                        class="nav-link"
                                        href="#group_subscription"
                                        data-toggle="tab"
                                    >
                                        <i class="fa fa-users mr-2"></i>{{ trans('global.group.title') }}
                                    </a>
                                </li>
                                <!-- Organization -->
                                <li v-if="shareWithOrganizations"
                                    class="nav-item"
                                >
                                    <a
                                        class="nav-link"
                                        href="#organization_subscription"
                                        data-toggle="tab"
                                    >
                                        <i class="fa fa-university mr-2"></i>{{ trans('global.organization.title') }}
                                    </a>
                                </li>
                                <!-- Token -->
                                <li v-if="shareWithToken"
                                    class="nav-item"
                                >
                                    <a
                                        class="nav-link"
                                        href="#token_subscription"
                                        data-toggle="tab"
                                    >
                                        <i class="fa fa-key mr-2"></i>{{ trans('global.token') }}
                                    </a>
                                </li>
                            </ul>
    
                            <div class="tab-content pt-2">
                                <!-- User Tab -->
                                <div v-if="shareWithUsers"
                                    id="user_subscription"
                                    class="tab-pane active show"
                                >
                                    <Select2
                                        id="users_subscription_select"
                                        name="users_subscription_select"
                                        url="/users"
                                        model="user"
                                        @selectedValue="(id) => {
                                            this.subscribe('App\\User', id[0])
                                        }"
                                    />
                                    <Subscribers v-if="subscribers.subscriptions != undefined"
                                        :modelUrl="modelUrl"
                                        :subscriptions="subscribers.subscriptions.filter(s => s.subscribable_type === 'App\\User')"
                                        :subscribing_model="'App\\User'"
                                        :canEditLabel="canEditLabel"
                                        :canEditCheckbox="canEditCheckbox"
                                    />
                                </div>
    
                                <!-- Group Tab -->
                                <div v-if="shareWithGroups"
                                    id="group_subscription"
                                    class="tab-pane"
                                >
                                    <Select2
                                        id="group_subscription_select"
                                        name="group_subscription_select"
                                        url="/groups"
                                        model="group"
                                        @selectedValue="(id) => {
                                            this.subscribe('App\\Group', id[0])
                                        }"
                                    />
                                    <Subscribers v-if="subscribers.subscriptions != undefined"
                                        :modelUrl="modelUrl"
                                        :subscriptions="subscribers.subscriptions.filter(s => s.subscribable_type === 'App\\Group')"
                                        :subscribing_model="'App\\Group'"
                                        :canEditLabel="canEditLabel"
                                        :canEditCheckbox="canEditCheckbox"
                                    />
                                </div>
    
                                <!-- Organization Tab -->
                                <div v-if="shareWithOrganizations"
                                    id="organization_subscription"
                                    class="tab-pane"
                                >
                                    <Select2
                                        id="organization_subscription_select"
                                        name="organization_subscription_select"
                                        url="/organizations"
                                        model="organization"
                                        @selectedValue="(id) => {
                                            this.subscribe('App\\Organization', id[0])
                                        }"
                                    />
                                    <Subscribers v-if="subscribers.subscriptions != undefined"
                                        :modelUrl="modelUrl"
                                        :subscriptions="subscribers.subscriptions.filter(s => s.subscribable_type === 'App\\Organization')"
                                        :subscribing_model="'App\\Organization'"
                                        :canEditLabel="canEditLabel"
                                        :canEditCheckbox="canEditCheckbox"
                                    />
                                </div>
    
                                <!-- Token Tab -->
                                <div v-if="shareWithToken"
                                    id="token_subscription"
                                    class="tab-pane"
                                >
                                    <div class="form-group pt-2">
                                        <input
                                            v-model="nameToken"
                                            class="form-control w-100 mb-2"
                                            :placeholder="trans('global.token_title') + ' *'"
                                            required
                                        />
                                    </div>
                                    <VueDatePicker
                                        v-model="endDateToken"
                                        format="dd.MM.yyy HH:mm"
                                        :teleport="true"
                                        locale="de"
                                        :select-text="trans('global.ok')"
                                        :cancel-text="trans('global.close')"
                                        :placeholder="trans('global.valid_to')"
                                    />
    
        <!--                            <span v-if="canEditCheckbox"
                                        class="pull-right custom-control custom-switch custom-switch-on-green">
                                        <input v-model="canEditToken"
                                                type="checkbox"
                                                id="canEditToken"
                                                class="custom-control-input pt-1 "
                                                @click="changeCanEditTokenValue(canEditToken)">
                                        <label class="custom-control-label " for="canEditToken"></label>
                                    </span>-->
                                    <div>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-success pull-right my-2"
                                            :disabled="nameToken.trim() == ''"
                                            @click="createUserToken()"
                                        >
                                            {{ trans('global.create') }}
                                        </button>
                                    </div>
    
                                    <hr class="pt-1 clearfix">
    
                                    <Tokens v-if="subscribers.tokens != undefined"
                                        :modelUrl="modelUrl"
                                        :canEditLabel="canEditLabel"
                                        :canEditCheckbox="canEditCheckbox"
                                        :subscriptions="subscribers.tokens"
                                        @tokenDeleted="(item) => {
                                            let index = this.subscribers.tokens.indexOf(item);
                                            this.subscribers.tokens.splice(index, 1);
                                        }"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            type="button"
                            class="btn btn-default"
                            data-widget="remove"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.close') }}
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </Transition>
</template>
<script>
import Subscribers from "./Subscribers.vue";
import Tokens from "./Tokens.vue";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import Select2 from "../forms/Select2.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'subscribe-modal',
    props: {
        params: {
            type: Object,
            default: null,
        },
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            modelUrl:  null,
            modelId: null,
            subscribable_id: null,
            subscribable_type: null,
            endDateToken: null, //new Date().setFullYear(new Date().getFullYear() + 1),
            subscribers: Object,
            canEditToken: false,
            canEditCheckbox: true,
            shareWithUsers: true,
            shareWithGroups: true,
            shareWithOrganizations: true,
            shareWithToken:  false,
            nameToken: '',
            canEditLabel: window.trans.global.can_edit,
        };
    },
    methods: {
        resetComponent() {
            this.modelUrl = null;
            this.modelId = null;
            this.subscribable_id = null;
            this.subscribable_type = null;
            this.subscribers = Object;
            this.shareWithUsers = true;
            this.shareWithGroups = true;
            this.shareWithOrganizations = true;
        },
        loadSubscribers() {
            axios.get('/' + this.modelUrl + 'Subscriptions?' + this.modelUrl + '_id=' + this.modelId)
                .then(res => {
                    this.subscribers = res.data;
                })
                .catch(err => {
                    console.log(err);
                });
        },
        subscribe(subscribable_type, subscribable_id) {
            axios.post('/' + this.modelUrl + 'Subscriptions', {
                model_id:  this.modelId,
                subscribable_type: subscribable_type,
                subscribable_id: subscribable_id
            })
            .then(res => {
                this.subscribers.subscriptions.push(res.data);
            })
            .catch(err => {
                console.log(err);
            });
        },
        changeCanEditTokenValue(value) {
            this.canEditToken = !value;
        },
        createUserToken() {
            axios.post('/tokens', {
                model_id: this.modelId,
                title: this.nameToken,
                date: this.endDateToken,
                editable: this.canEditToken,
                model_url: this.modelUrl
            }).then((response) => this.subscribers.tokens.push(response.data));
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params;

                if (typeof (params) !== 'undefined') {
                    this.resetComponent();
                    this.modelUrl = params.modelUrl;
                    this.modelId = params.modelId;
                    this.shareWithUsers = params.shareWithUsers;
                    this.shareWithGroups = params.shareWithGroups;
                    this.shareWithOrganizations = params.shareWithOrganizations;
                    this.shareWithToken = params.shareWithToken;
                    this.canEditLabel = params.canEditLabel;
                    this.canEditCheckbox = params.canEditCheckbox;
                    this.loadSubscribers();
                }
            }
        });

        this.$eventHub.on('unsubscribe', (subscription) => {
            let index = this.subscribers.subscriptions.indexOf(subscription);
            this.subscribers.subscriptions.splice(index, 1);
        });
    },
    components: {
        Subscribers,
        Tokens,
        VueDatePicker,
        Select2,
    }
}
</script>