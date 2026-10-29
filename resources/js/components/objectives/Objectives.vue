<template>
    <div class="d-flex flex-column gap-2">
        <div v-for="terminal in terminal_objectives"
            class="d-flex"
        >
            <div class="objectives">
                <div class="d-flex flex-column">
                    <ObjectiveBox
                        type="terminal"
                        :objective="terminal"
                        :settings="settings"
                    />
                    <button
                        type="button"
                        class="d-sm-none btn collapse-objectives collapsed border-0 shadow-none w-100"
                        :title="trans('global.enablingObjective.toggle_objectives')"
                        data-bs-toggle="collapse"
                        :data-bs-target="'#enabling-objectives-' + terminal.id"
                        :aria-controls="'enabling-objectives-' + terminal.id"
                        aria-expanded="false"
                    >
                        <i class="fa fa-angles-down"></i>
                    </button>
                </div>

                <div
                    :id="'enabling-objectives-' + terminal.id"
                    class="d-sm-contents w-100 collapse"
                >
                    <EnablingObjectives
                        :terminalobjective="terminal"
                        :objectives="terminal.enabling_objectives"
                        :referenceable_id="referenceable_id"
                        :referenceable_type="referenceable_type"
                        :settings="settings"
                        :editable="editable"
                    />
                </div>
            </div>
            <span v-if="editable && showTools"
                style="margin-right: -0.5rem;"
            >
                <button
                    type="button"
                    class="btn btn-icon text-danger"
                    :title="trans('global.terminalObjective.remove')"
                    @click="destroy(terminal)"
                >
                    <i class="fa fa-trash"></i>
                </button>
            </span>
        </div>

        <div v-if="editable && showTools">
            <button
                type="button"
                class="btn btn-default border-0 mb-1 rounded-pill"
                style="padding: 0.75rem 1.25rem;"
                @click="openModal()"
            >
                <i class="fa fa-add pe-1"></i>
                {{ trans('global.referenceable_types.objective') }}
            </button>
        </div>
    </div>
</template>
<script>
import ObjectiveBox from './ObjectiveBox.vue';
import EnablingObjectives from './EnablingObjectives.vue';

export default {
    components: {
        ObjectiveBox,
        EnablingObjectives,
    },
    props: {
        owner_id: {
            type: Number,
            default: null,
        },
        editable: {
            type: Boolean,
            default: false,
        },
        showTools: {
            type: Boolean,
            default: true,
        },
        referenceable_id: {
            type: Number,
            default: null,
        },
        referenceable_type: {
            type: String,
            default: null,
        },
    },
    data() {
        return {
            settings: {
                last: null,
                course: false,
                edit: false,
                achievements: true,
            },
            terminal_objectives: [],
            enablingCall: null,
            terminalCall: null,
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
            let terminal_objectives = [...this.terminalCall];
            const map = terminal_objectives.map(sub => sub.id)

            this.enablingCall.forEach(terminal => {
                // delete the enablingSubscription, if the terminalObjective is already subscribed
                if (map.includes(terminal.id)) {
                    axios.post('/enablingObjectiveSubscriptions/destroy', {
                        enabling_objective_id: terminal.enabling_objectives.map(e => e.id),
                        subscribable_id: this.referenceable_id,
                        subscribable_type: this.referenceable_type,
                    });
                } else {
                    terminal.enabling_subscriptions = true; // to identify enabling-terminal_objectives for deletion
                    terminal_objectives.push(terminal);
                }
            });

            Object.assign(this.terminal_objectives, terminal_objectives);
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
                .then(res => {
                    this.terminal_objectives.splice(this.terminal_objectives.findIndex(sub => sub.id === subscription.id), 1);
                })
                .catch(e => {
                    console.log(e);
                    this.toast.error(this.errorMessage(e));
                });
        },
        handleAchievementsEvent(data) {
            let achievements;

            for (let i = 0; i < this.terminal_objectives.length; i++) {
                const enabling = this.terminal_objectives[i].enabling_objectives.find(e => e.id === data.objective_id);

                if (enabling !== undefined) {
                    achievements = enabling.achievements;
                    break;
                }
            }

            data.achievements.forEach(newAchievement => {
                const old = achievements.find(a => a.id === newAchievement.id);

                if (old === undefined) {
                    achievements.push(newAchievement);
                } else {
                    Object.assign(old, newAchievement);
                }
            });
        },
    },
    mounted() {
        this.loaderEvent();

        this.$eventHub.on('subscriptions-added', data => {
            if (data.id === this.referenceable_id) {
                this.terminal_objectives.push(data.terminal_objectives);
            }

        });

        this.$eventHub.on('achievements-set', data => {
            if (this.referenceable_id === data.referenceable_id && this.referenceable_type == data.referenceable_type) {
                this.handleAchievementsEvent(data);
            }
        });
    },
}
</script>