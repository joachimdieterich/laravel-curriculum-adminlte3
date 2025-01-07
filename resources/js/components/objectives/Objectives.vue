<template>
    <div class="card mb-0">
        <div v-for="(subscription, index) in subscriptions"
            class="card-body border-bottom"
        >
            <div v-if="typeof (subscription.enabling_objective) == 'undefined'"
                class="row"
            >  <!-- terminalObjective -->
                <div class="col-12">
                    <div class="card-tools pull-right">
                        <span v-if="$userId == owner_id">
                            <a @click="destroy(subscription)">
                                <i class="fas fa-trash text-danger pointer"></i>
                            </a>
                        </span>
                    </div>
                    <ObjectiveBox
                        type="terminal"
                        :objective="subscription.terminal_objective"
                        :settings="settings"
                    ></ObjectiveBox>

                    <div class="ml-auto">
                        <EnablingObjectives
                            :terminalobjective="subscription.terminal_objective"
                            :objectives="subscription.terminal_objective.enabling_objectives"
                            :settings="settings"
                            :editable="editable"
                        ></EnablingObjectives>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="$userId == owner_id"
            class="card-footer pointer"
            @click="openModal()"
        >
            <i class="fas fa-add pr-1"></i>
            {{ trans('global.referenceable_types.link') }}
        </div>
    </div>
</template>

<script>
import ObjectiveBox from './ObjectiveBox.vue';
import EnablingObjectives from './EnablingObjectives.vue';
import {useGlobalStore} from "../../store/global";

export default {
    props: {
        owner_id: {
            default: false
        },
        editable: {
            default: false
        },
        referenceable_type: {},
        referenceable_id: {},
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            settings: {
                'last': null,
                'course' : true,
                'edit' : false,
                'achievements' : true,
            },
            subscriptions: [],
            enablingCall: null,
            terminalCall: null,
            errors: {},
        }
    },
    computed: {
        callFinished() {
            return this.terminalCall !== null && this.enablingCall !== null;
        },
    },
    watch: {
        // function gets only called once, after both axios-calls are finished
        callFinished(finished) {
            if (finished) this.checkSubscriptions();
        },
    },
    methods: {
        loaderEvent() {
            const start = new Date().getTime();
            //TODO: might need a loading indicator
            // since we need to wait for two sequentiell axios-calls, this might take a long time to load
            axios.get('/terminalObjectiveSubscriptions?subscribable_type=' + this.referenceable_type + '&subscribable_id=' + this.referenceable_id)
                .then(response => {
                    console.log('terminal', new Date().getTime() - start);
                    this.terminalCall = response.data;
                })
                .catch(e => {
                    console.log(e);
                });
            axios.get('/enablingObjectiveSubscriptions?subscribable_type=' + this.referenceable_type + '&subscribable_id=' + this.referenceable_id)
                .then(response => {
                    console.log('enabling', new Date().getTime() - start);
                    this.enablingCall = response.data;
                })
                .catch(e => {
                    console.log(e);
                });
        },
        /**
         * combine enablingObjectives into their terminalObjective
         */
        checkSubscriptions() {
            let subscriptions = this.terminalCall;
            const map = subscriptions.map(sub => sub.id)

            this.enablingCall.forEach(terminal => {
                // delete the enablingSubscription, if the terminalObjective is already subscribed
                if (map.includes(terminal.id)) {
                    axios.post('/enablingObjectiveSubscriptions/destroy', {
                        enabling_objective_id: terminal.enabling_objectives.map(e => e.id),
                        subscribable_id: this.referenceable_id,
                        subscribable_type: this.referenceable_type,
                    });
                } else {
                    subscriptions.push(terminal);
                }
            });

            Object.assign(this.subscriptions, subscriptions);
        },
        openModal() {
            this.globalStore.showModal('subscribe-objective-modal', {
                'subscribable_type': this.referenceable_type,
                'subscribable_id': this.referenceable_id,
            });
        },
        destroy(subscription) {
            // enabling-subscriptions with the same parent are combined into terminal-subscriptions and marked with a 'fake'-tag
            if (subscription.fake) {
                const length = subscription.terminal_objective.enabling_objectives.length - 1;
                // reverse-loop because of explanation below
                for (let i = length; i >= 0; i--) {
                    const objective = subscription.terminal_objective.enabling_objectives[i];
                    const enablingSubscription = this.enablingSubscriptions.find(obj => obj.enabling_objective_id === objective.id);

                    /**
                     * for some reason the [0]-index objective references itself,
                     * which creates a JSON-loop, which throws an error when trying to do an axios-call.
                     * by removing the reference, the original value also gets removed
                     * INFO: the 'enablingSubscriptions'-data sent by the server in 'loaderEvent()'
                     *       doesn't have this reference when checking the response from the network-tab,
                     *       but for some magical reason, when outputing 'response.data' it's there in the [0]-index
                     */
                    if (i === 0) enablingSubscription.enabling_objective.terminal_objective.enabling_objectives = [];

                    axios.post('/enablingObjectiveSubscriptions/destroy', enablingSubscription)
                        .catch((error) => {
                            console.log(error);
                        });
                }

                this.subscriptions.splice(this.subscriptions.findIndex(sub => sub.terminal_objective_id === subscription.terminal_objective_id), 1);
            } else {
                axios.post('/terminalObjectiveSubscriptions/destroy', subscription)
                    .then((res) => {
                        this.subscriptions.splice(this.subscriptions.findIndex(sub => sub.terminal_objective_id === subscription.terminal_objective_id), 1);
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            }
        },

    },
    mounted() {
        this.loaderEvent();
        this.$eventHub.on('subscriptions-added', data => {
            if (data.id === this.referenceable_id) {

            }
        });
    },
    components: {
        ObjectiveBox,
        EnablingObjectives,
    }
}
</script>
