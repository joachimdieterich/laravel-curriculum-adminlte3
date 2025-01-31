<template>
    <div class="card mb-0">
        <div v-for="terminal in subscriptions"
            class="card-body border-bottom"
        >
            <div class="row">
                <div class="col-12">
                    <div
                        class="card-tools position-absolute"
                        style="right: -10px;"    
                    >
                        <span v-if="$userId == owner_id">
                            <a @click="destroy(terminal)">
                                <i class="fas fa-trash text-danger pointer"></i>
                            </a>
                        </span>
                    </div>
                    <ObjectiveBox
                        type="terminal"
                        :objective="terminal"
                        :settings="settings"
                    />

                    <div class="ml-auto">
                        <EnablingObjectives
                            :terminalobjective="terminal"
                            :objectives="terminal.enabling_objectives"
                            :settings="settings"
                            :editable="editable"
                        />
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
            default: false,
        },
        editable: {
            default: false,
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
                last: null,
                course : true,
                edit : false,
                achievements : true,
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
            axios.get('/terminalObjectiveSubscriptions?subscribable_type=' + this.referenceable_type + '&subscribable_id=' + this.referenceable_id)
                .then(response => {
                    this.terminalCall = response.data;
                })
                .catch(e => {
                    console.log(e);
                });
            axios.get('/enablingObjectiveSubscriptions?subscribable_type=' + this.referenceable_type + '&subscribable_id=' + this.referenceable_id)
                .then(response => {
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
            let subscriptions = [...this.terminalCall];
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
                    terminal.enabling_subscriptions = true; // to identify enabling-subscriptions for deletion
                    subscriptions.push(terminal);
                }
            });

            Object.assign(this.subscriptions, subscriptions);
        },
        openModal() {
            this.globalStore.showModal('subscribe-objective-modal', {
                subscribable_type: this.referenceable_type,
                subscribable_id: this.referenceable_id,
            });
        },
        destroy(subscription) {
            const type = subscription.enabling_subscriptions ? 'enabling' : 'terminal';

            axios.post('/' + type + 'ObjectiveSubscriptions/destroy', { 
                subscribable_id: this.referenceable_id,
                subscribable_type: this.referenceable_type,
                terminal_objective_id: subscription.id,
                enabling_objective_id: subscription.enabling_objectives.map(e => e.id),
            })
            .then((res) => {
                this.subscriptions.splice(this.subscriptions.findIndex(sub => sub.id === subscription.id), 1);
            })
            .catch((error) => {
                console.log(error);
            });
        },

    },
    mounted() {
        this.loaderEvent();
        this.$eventHub.on('subscriptions-added', data => {
            if (data.id === this.referenceable_id) {
                this.subscriptions.push(data.terminal_objectives);
            }
        });
    },
    components: {
        ObjectiveBox,
        EnablingObjectives,
    }
}
</script>
