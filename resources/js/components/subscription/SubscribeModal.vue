<template>
    <Transition name="modal">
        <div v-if="show"
             class="modal-mask"
            id="subscribe-modal"
            name="subscribe-modal"
        >
            <div class="modal-container">
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
                                    <i class="fa fa-user mr-2"></i>{{ trans('global.user.title') }}
                                </a>
                            </li>
                            <!-- Group -->
                            <li v-if="shareWithGroups"
                                class="nav-item">
                                <a class="nav-link" href="#group_subscription" data-toggle="tab">
                                    <i class="fa fa-users mr-2"></i>{{ trans('global.group.title') }}
                                </a>
                            </li>
                            <!-- Organization -->
                            <li v-if="shareWithOrganizations"
                                class="nav-item">
                                <a class="nav-link" href="#organization_subscription" data-toggle="tab">
                                    <i class="fa fa-university mr-2"></i>{{ trans('global.organization.title') }}
                                </a>
                            </li>
                            <li v-if="shareWithToken"
                                class="nav-item">
                                <a class="nav-link" href="#token_subscription" data-toggle="tab">
                                    <i class="fa fa-key mr-2"></i>{{ trans('global.token') }}
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
<!--                                    <Multiselect
                                        v-model="myValue"
                                        :options="myOptions"
                                        :settings="{ settingOption: value, settingOption: value }"
                                        @change="myChangeEvent($event)"
                                        @select="mySelectEvent($event)" />-->
                                </div>
                                <subscribers
                                    v-if="typeof subscribers.subscriptions != 'undefined'"
                                    :modelUrl="modelUrl"
                                    :canEditLabel="canEditLabel"
                                    :canEditCheckbox="canEditCheckbox"
                                    :subscriptions="subscribers.subscriptions"
                                    :subscribing_model="'App\\User'"/>

                            </div>

                            <!-- Group Tab -->
                            <div v-if="shareWithGroups"
                                 class="tab-pane" id="group_subscription">
                                <div class="form-group pt-2 ">
<!--                                    <select name="groups"
                                            id="groups"
                                            class="form-control select2 "
                                            style="width:100%;"
                                    >
                                    </select>-->
                                </div>
                                <subscribers
                                    v-if="typeof subscribers.subscriptions != 'undefined'"
                                    :modelUrl="modelUrl"
                                    :canEditLabel="canEditLabel"
                                    :canEditCheckbox="canEditCheckbox"
                                    :subscriptions="subscribers.subscriptions"
                                    :subscribing_model="'App\\Group'"/>
                            </div>

                            <!-- Organization Tab -->
                            <div v-if="shareWithOrganizations"
                                 class="tab-pane" id="organization_subscription">
                                <div class="form-group pt-2">
<!--                                    <select name="organizations"
                                            id="organizations"
                                            class="form-control select2 "
                                            style="width:100%;"
                                    >
                                    </select>-->
                                </div>
                                <subscribers
                                    v-if="typeof subscribers.subscriptions != 'undefined'"
                                    :modelUrl="modelUrl"
                                    :canEditLabel="canEditLabel"
                                    :canEditCheckbox="canEditCheckbox"
                                    :subscriptions="subscribers.subscriptions"
                                    :subscribing_model="'App\\Organization'"/>
                            </div>

                            <!-- Token Tab -->
                            <div v-if="shareWithToken"
                                 class="tab-pane" id="token_subscription">

                                <div class="form-group pt-2">
                                    <input v-model="nameToken" class="form-control mb-2" style="width: 100%;"
                                           placeholder="Freigabetitel">
                                    <date-picker v-model="endDateToken" style="width:100%;"
                                                 placeholder="Ablaufdatum"></date-picker>
                                </div>
                                <small>{{ canEditLabel }}</small>
                                <span v-if="canEditCheckbox"
                                      class="pull-right custom-control custom-switch custom-switch-on-green">
                                <input v-model="canEditToken"
                                       type="checkbox"
                                       id="canEditToken"
                                       class="custom-control-input pt-1 "
                                       @click="changeCanEditTokenValue(canEditToken)">
                                <label class="custom-control-label " for="canEditToken"></label>
                            </span>
                                <div>
                                    <button type="button" @click="createUserToken()" :disabled="nameToken.trim() == ''"
                                            class="btn btn-sm btn-outline-success pull-right my-2">
                                        Speichern
                                    </button>
                                </div>

                                <hr class="pt-1 clearfix">

                                <div>
                                    <tokens
                                        v-if="typeof subscribers.subscriptions != 'undefined'"
                                        :modelUrl="modelUrl"
                                        :canEditLabel="canEditLabel"
                                        :canEditCheckbox="canEditCheckbox"
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
            </div>

        </div>
    </Transition>
</template>

<script>
import subscribers from "./Subscribers";
import tokens from "./Tokens";
import DatePicker from 'vue3-datepicker';
/*import Multiselect from '@vueform/multiselect';*/

export default {
    props: {
        show: {
            type: Boolean
        },
        params: {
            type: Object
        }  //{ 'modelId': curriculum.id, 'modelUrl': 'curriculum' , 'shareWithToken': true, 'canEditCheckbox': false}
    },
    data() {
        return {
            modelUrl:  null,
            modelId: null,
            subscribable_id: null,
            subscribable_type: null,
            endDateToken: null,//new Date(),
            subscribers: Object,
            hover: false,
            canEditToken: false,
            canEditCheckbox: true,
            shareWithUsers: true,
            shareWithGroups: true,
            shareWithOrganizations: true,
            shareWithToken:  false,
            nameToken: '',
            canEditLabel: window.trans.global.can_edit,
            myValue: '',
            myOptions: ['op1', 'op2', 'op3'] // or [{id: key, text: value}, {id: key,

        };
    },
    watch: {
        params: function(newVal, oldVal) {
            //console.log(newVal);
            this.modelUrl = newVal.modelUrl;
            this.modelId = newVal.modelId;
            this.shareWithUsers = newVal.shareWithUsers;
            this.shareWithGroups = newVal.shareWithGroups;
            this.shareWithOrganizations = newVal.shareWithOrganizations;
            this.shareWithToken = newVal.shareWithToken;
            this.canEditLabel = newVal.canEditLabel;
            this.canEditCheckbox = newVal.canEditCheckbox;
        },
    },
    methods: {
        beforeOpen() {
            this.resetComponent();
            //this.modelUrl = params.modelUrl;
           // this.modelId = this.params.modelId;
           /* if (this.params.shareWithUsers == false) {
                this.shareWithUsers = this.params.shareWithUsers;
            }
            if (this.params.shareWithGroups == false) {
                this.shareWithGroups = this.params.shareWithGroups;
            }
            if (this.params.shareWithOrganizations == false) {
                this.shareWithOrganizations = this.params.shareWithOrganizations;
            }
            if (this.params.shareWithToken == true) {
                this.shareWithToken = this.params.shareWithToken;
            }
            if (this.params.shareWithToken == true) {
                this.shareWithToken = this.params.shareWithToken;
            }
            if (typeof (this.params.canEditLabel) !== 'undefined') {
                this.canEditLabel = this.params.canEditLabel;
            }
            if (typeof (this.params.canEditCheckbox) !== 'undefined') {
                this.canEditCheckbox = this.params.canEditCheckbox;
            }*/
            this.loadSubscribers();
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
        /*initSelect2() {
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
                }).on('select2:open', () => {
                    document.querySelector('.select2-search__field').focus();
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
                }).on('select2:open', () => {
                    document.querySelector('.select2-search__field').focus();
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
                }).on('select2:open', () => {
                    document.querySelector('.select2-search__field').focus();
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
        },*/
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
            axios.post('/tokens', {
                'model_id': this.modelId,
                'title': this.nameToken,
                'date': this.endDateToken,
                'editable': this.canEditToken,
                'model_url': this.modelUrl
            }).then( () => this.loadSubscribers())
        }
    },
    mounted() {
        this.beforeOpen();
        //this.endDateToken.setMonth(this.endDateToken.getMonth() + 1)
    },
    components: {
        subscribers,
        tokens,
        DatePicker,
        /*Multiselect*/
    }
}
</script>

<style>
.modal-mask {
    position: fixed;
    z-index: 9998;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    transition: opacity 0.3s ease;
}

.modal-container {
    max-width: 900px;
    margin: auto;
    padding: 0;
    background-color: #fff;
    border-radius: 2px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
    transition: all 0.3s ease;
}


.modal-default-button {
    float: right;
}

/*
 * The following styles are auto-applied to elements with
 * transition="modal" when their visibility is toggled
 * by Vue.js.
 *
 * You can easily play with the modal transition by editing
 * these styles.
 */

.modal-enter-from {
    opacity: 0;
}

.modal-leave-to {
    opacity: 0;
}

.modal-enter-from .modal-container,
.modal-leave-to .modal-container {
    -webkit-transform: scale(1.1);
    transform: scale(1.1);
}
</style>
