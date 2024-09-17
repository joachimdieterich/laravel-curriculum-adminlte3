<template>
    <div class="card mb-0">

        <div v-for="(subscription, index) in subscriptions"
            class="card-body border-bottom">
            <div v-if="typeof (subscription.enabling_objective) == 'undefined'"
                class="row">  <!-- terminalObjective -->
                <div class="col-12">
                    <div class="card-tools pull-right">
                        <span v-if="is_owner()">
                            <a @click="destroy(subscription)" >
                                <i class="fas fa-trash text-danger pointer"></i>
                            </a>
                        </span>
                    </div>
                    <ObjectiveBox
                        type="terminal"
                        :objective="subscription.terminal_objective"
                        :settings="settings">
                    </ObjectiveBox>

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
        <div v-if="is_owner()"
             class="card-footer pointer"
             @click="open()">
            <i class="fas fa-add pr-1"></i>
            {{ trans('global.referenceable_types.link') }}
        </div>
    </div>

</template>

<script>
import ObjectiveBox from './ObjectiveBox.vue';
import EnablingObjectives from './EnablingObjectives.vue';

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
    data() {
        return {
            settings: {
                'last': null,
                'course' : true,
                'edit' : false,
                'achievements' : true,
            },
            subscriptions: [],
            enablingSubscriptions: [],
            terminalSubscriptions: [],
            errors: {},
        }
    },
    methods: {
        async loaderEvent() {
            //TODO: might need a loading indicator
            // since we need to wait for two sequentiell axios-calls, this might take a long time to load
            await axios.get('/terminalObjectiveSubscriptions?subscribable_type=' + this.referenceable_type + '&subscribable_id=' + this.referenceable_id)
                .then(response => {
                    this.terminalSubscriptions = response.data.subscriptions;
                })
                .catch(e => {
                    console.log(e);
                });
            await axios.get('/enablingObjectiveSubscriptions?subscribable_type=' + this.referenceable_type + '&subscribable_id=' + this.referenceable_id)
                .then(response => {
                    this.enablingSubscriptions = response.data.subscriptions;
                })
                .catch(e => {
                    console.log(e);
                });

            this.checkSubscriptions();
        },
        /**
         * combine enablingObjectives into their terminalObjective
         */
        checkSubscriptions() {
            let subObj = [];

            if (this.terminalSubscriptions.length > 0) {
                subObj = this.terminalSubscriptions;

                // reverse-loop, since removing an item while counting up messes with the index
                for (let i = this.enablingSubscriptions.length - 1; i >= 0; i--) {
                    const subscription = this.enablingSubscriptions[i];
                    const parent = this.terminalSubscriptions.find(
                        terminal => terminal.terminal_objective_id === subscription.enabling_objective.terminal_objective_id
                    );

                    // if a terminalObjective is already subscribed,
                    // enablingSubscriptions from this terminalObjective aren't needed
                    if (parent !== undefined) {
                        axios.post('/enablingObjectiveSubscriptions/destroy', subscription);
                        this.enablingSubscriptions.splice(i, 1);
                    }
                }
            }

            const newTerminals = {};
            this.enablingSubscriptions.forEach(sub => {
                const terminalID = sub.enabling_objective.terminal_objective_id;
                if (newTerminals[terminalID] === undefined) {
                    // create a new terminal object for this ID
                    //? might need more properties
                    newTerminals[terminalID] =
                    {
                        'owner_id': sub.owner_id,
                        'terminal_objective': sub.enabling_objective.terminal_objective,
                        'terminal_objective_id': terminalID,
                        'fake': true,  // needs an attribute to differentiate between an actual terminal-subscription
                    };

                    newTerminals[terminalID].terminal_objective.enabling_objectives = [sub.enabling_objective];
                } else {
                    newTerminals[terminalID].terminal_objective.enabling_objectives.push(sub.enabling_objective);
                }
            });

            // add new terminalObjectives through their ID
            Object.keys(newTerminals).forEach(key => {
                subObj.push(newTerminals[key]);
            });

            this.subscriptions = subObj;
        },
        is_owner() {
            return (this.$userId == this.owner_id) ?? false
        },
        open() {
            this.$modal.show('subscribe-objective-modal', {
                'referenceable_type': this.referenceable_type,
                'referenceable_id': this.referenceable_id
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
        this.$eventHub.on('subscriptions_added', id => {
            if (id === this.referenceable_id) this.loaderEvent();
        });
    },
    components: {
        ObjectiveBox,
        EnablingObjectives,
    }
}
</script>
