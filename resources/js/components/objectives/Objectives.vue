<template>
    <div class="card mb-0">

        <div v-for="(subscription, index) in subscriptions"
            class="card-body border-bottom">
            <div v-if="typeof (subscription.enabling_objective) == 'undefined'"
                class="row">  <!-- terminalObjective -->
                <div class="col-12">
                    <div class="card-tools pull-right">
                        <span v-if="is_owner()">
                            <a @click="destroy('terminal', subscription)" >
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
                            :settings="settings">
                        </EnablingObjectives>
                    </div>
                </div>
            </div>
            <div v-else
                 class="row"> <!-- enablingObjective -->
                <div class="col-12">
                    <div class="card-tools pull-right">
                        <span v-if="is_owner()">
                            <a @click="destroy('enabling', subscription)" >
                                <i class="fas fa-trash text-danger"></i>
                            </a>
                        </span>
                    </div>
                    <ObjectiveBox
                          type="terminal"
                          :objective="subscription.enabling_objective.terminal_objective"
                          :settings="settings">
                    </ObjectiveBox>

                    <div class="ml-auto">
                        <ObjectiveBox
                            type="enabling"
                            :objective="subscription.enabling_objective"
                            :settings="settings">
                        </ObjectiveBox>
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
const ObjectiveBox =
    () => import('./ObjectiveBox');
const EnablingObjectives =
    () => import('./EnablingObjectives');

    export default {
        props: {
            'owner_id': {
                default: false
            },
            'referenceable_type': {},
            'referenceable_id': {},
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
            loaderEvent() {
                axios.get('/terminalObjectiveSubscriptions?subscribable_type=' + this.referenceable_type + '&subscribable_id=' + this.referenceable_id)
                    .then(response => {
                        this.terminalSubscriptions = response.data.subscriptions;
                    })
                    .catch(e => {
                        console.log(e);
                    });
                axios.get('/enablingObjectiveSubscriptions?subscribable_type=' + this.referenceable_type + '&subscribable_id=' + this.referenceable_id)
                    .then(response => {
                        this.enablingSubscriptions = response.data.subscriptions;
                        //? if function is called outside axios-call, it gets called too early
                        // putting await before both axios-calls might increase waiting time too much
                        this.checkSubscriptions();
                    })
                    .catch(e => {
                        console.log(e);
                    });      
            },
            /**
             * combine enablingObjectives into their terminalObjective
             */
            checkSubscriptions() {
                if (this.terminalSubscriptions.length > 0) {
                    this.subscriptions = this.terminalSubscriptions;
                    
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
                            'terminal_objective_id': terminalID
                        };
    
                        newTerminals[terminalID].terminal_objective.enabling_objectives = [sub.enabling_objective];
                    } else {
                        newTerminals[terminalID].terminal_objective.enabling_objectives.push(sub.enabling_objective);
                    }
                });

                Object.keys(newTerminals).forEach(key => {
                    this.subscriptions.push(newTerminals[key]);
                });
            },
            is_owner(){
                return (this.$userId == this.owner_id) ?? false
            },
            open() {
                this.$modal.show('subscribe-objective-modal', {
                    'referenceable_type': this.referenceable_type,
                    'referenceable_id': this.referenceable_id
                });
            },
            destroy(type, subscription){
                axios.post('/' + type + 'ObjectiveSubscriptions/destroy', subscription)
                    .then((res) => {
                        this.loaderEvent();
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            },

        },
        mounted() {
            this.loaderEvent();
        },
        components: {
            ObjectiveBox,
            EnablingObjectives,
        }
    }
</script>
