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
                                <i class="fas fa-trash text-danger"></i>
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
                objectives: {},
                settings: {
                    'last': null,
                    'course' : true,
                    'edit' : false,
                    'achievements' : true,
                },
                subscriptions: [],
                errors: {},
            }
        },
        methods: {
            loaderEvent() {
                    axios.get('/terminalObjectiveSubscriptions?subscribable_type=' + this.referenceable_type + '&subscribable_id=' + this.referenceable_id)
                        .then(response => {
                            this.subscriptions = response.data.subscriptions;
                        })
                        .catch(e => {
                            console.log(e);
                        });
                axios.get('/enablingObjectiveSubscriptions?subscribable_type=' + this.referenceable_type + '&subscribable_id=' + this.referenceable_id)
                    .then(response => {
                        response.data.subscriptions.forEach(
                            (subscription) => this.subscriptions.push(subscription)
                        );
                    })
                    .catch(e => {
                        console.log(e);
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
            this.objectives = this.subscriptions;
        },

        components: {
            ObjectiveBox,
            EnablingObjectives,
        }
    }
</script>
