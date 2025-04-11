<template>
    <div>
        <ul
            id="terminalObjectivesTopNav"
            class="nav bg-gray-light position-sticky"
            style="top: 56px; z-index: 100"
        >
            <draggable
                class="nav nav-pills"
                v-model="type_order"
                v-bind="dragOptions"
                tag="ul"
                role="tablist"
                item-key="id"
                @start="drag=true"
                @end="handleTypeMoved"
            >
                <template #item="{ element: type_index }">
                    <li
                        class="nav-item pl-0 pr-2 pb-2 pt-2"
                        role="presentation"
                    >
                        <a
                            :id="objective_types[type_index].id + '-tab'"
                            :href="'#Type-' + objective_types[type_index].id"
                            class="nav-link"
                            type="button"
                            role="tab"
                            data-toggle="tab"
                            aria-selected="false"
                            @click="setActiveTab(type_index)"
                        >
                            {{ objective_types[type_index].title }}
                        </a>
                    </li>
               </template>
            </draggable>
            <li class="form-group py-2 mb-0 ml-auto">
                <Select2
                    id="references"
                    name="references"
                    css="mb-0"
                    url="/curricula/references"
                    model="curriculum"
                    :showLabel="false"
                    :placeholder="trans('global.curricula_cross_references')"
                />
            </li>
        </ul>
        <hr class="mt-0">
        <div class="tab-content">
            <div v-for="type in objective_types"
                :id="'Type-' + type.id"
                class="tab-pane fade"
                role="tabpanel"
                :aria-labelledby="type.title + '-tab'"
            >
                <div v-for="terminal in type.terminal_objectives"
                    :id="'terminalObjective_' + terminal.id"
                >
                    <div class="row">
                        <div class="col-12 terminal-row">
                            <ObjectiveBox
                                type="terminal"
                                :objective="terminal"
                                :settings="settings"
                                :max_id="max_ids[activeTab]"
                                @createTerminalObjective="(createObjective) => {
                                    this.createTerminalObjective(createObjective);
                                }"
                                @update="(objective) => {
                                    this.currentTerminalObjective = objective;
                                    this.globalStore?.closeModal('terminal-objective-modal');
                                }"
                            />

                            <div class="ml-auto">
                                <EnablingObjectives
                                    :terminalobjective="terminal"
                                    :objectives="terminal.enabling_objectives"
                                    :settings="settings"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="settings.edit"
            v-permission="'curriculum_edit'"
            class="row"
        >
            <div
                id="createTerminalRow"
                class="col-12"
            >
                <ObjectiveBox
                    type="createterminal"
                    :objective="{ curriculum_id: curriculum.id }"
                    :objective_type_id="activeTab"
                    :settings="settings"
                    :max_id="max_ids[activeTab]"
                />
            </div>
        </div>

        <Teleport to="body">
            <TerminalObjectiveModal/>
            <EnablingObjectiveModal/>
            <MoveTerminalObjectiveModal/>
        </Teleport>
    </div>
</template>
<script>
import ObjectiveBox from './ObjectiveBox.vue';
import EnablingObjectives from './EnablingObjectives.vue';
import TerminalObjectiveModal from "./TerminalObjectiveModal.vue";
import EnablingObjectiveModal from "./EnablingObjectiveModal.vue";
import MoveTerminalObjectiveModal from './MoveTerminalObjectiveModal.vue';
import Select2 from '../forms/Select2.vue';
import draggable from "vuedraggable";
import {useGlobalStore} from "../../store/global";
import {useToast} from "vue-toastification";

export default {
    props: {
        curriculum: {
            type: Object,
            default: null,
        },
    },
    setup() {
        const globalStore = useGlobalStore();
        const toast = useToast();
        return {
            globalStore,
            toast,
        }
    },
    data() {
        return {
            settings: {},
            max_ids: {},
            type_order: [],
            typetabs: [],
            activeTab: null,
            currentCurriculaEnrolments: null,
            currentTerminalObjective: null,
            currentEnablingObjective: null,
            type_objectives: [],
            objective_types: [],
        }
    },
    methods: {
        setActiveTab(index) {
            const type_id = this.objective_types[index].id;
            this.activeTab = type_id;
        },
        processObjectives(response, objective_type_id = 0) {
            let terminal = {};
            this.typetabs.forEach(type => terminal[type] = []);

            response.data.forEach(t => {
                terminal[t.objective_type_id].push(t);
            });

            this.typetabs.forEach(type => this.max_ids[type] = terminal[type][terminal[type].length - 1].id);

            Object.assign(this.type_objectives, terminal);

            if (this.type_objectives.length === 0) return;

            if (this.curriculum.objective_type_order) {
                if (this.curriculum.objective_type_order.length === this.typetabs.length) {
                    this.typetabs = this.curriculum.objective_type_order;
                }
            }
            if (objective_type_id === 0) {
                this.activeTab = this.typetabs[0];
            }
        },
        async loaderEvent() {
            await axios.get('/curricula/' + this.curriculum.id + '/objectives')
                .then(response => {
                    this.objective_types = response.data;
                    // get the order by their current index instead of their id
                    this.type_order = this.curriculum.objective_type_order.map(
                        type_id => this.objective_types.findIndex(type => type.id === type_id)
                    );
                })
                .catch(e => {
                    console.log(e);
                });
        },
        externalEvent: function(ids) {
            this.reloadEnablingObjectives(ids);
        },
        reloadEnablingObjectives(ids) {
            axios.post('/curricula/' + this.curriculum.id + '/achievements', { 'user_ids' : ids })
                .then(response => {
                    this.processObjectives(response);
                })
                .catch(e => {
                    console.log(e);
                });
        },
        handleTypeMoved() {
            // active-state needs to be reset, since it changes to a new index
            this.$el.querySelector('.nav-link.active').classList.remove('active');
            document.getElementById(this.activeTab + '-tab').classList.add('active');
            // send new order to the server
            axios.put("/curricula/" + this.curriculum.id + "/syncObjectiveTypesOrder", {
                objective_type_order: this.type_order.map(index => this.objective_types[index].id),
            })
                .catch(error => {
                    if (error.status === 403) {
                        this.toast.error(error.response.data.message);
                    } else {
                        this.toast.error(this.trans('global.error'));
                    }
                    console.log(error);
                });
        },
        addNewType(type) {
            // if new tab gets created, switch to this tab
            let newTab = this.typetabs.push(type.id);
            this.activeTab = this.typetabs[newTab - 1];
            this.type_objectives[type.id] = [];
        },
    },
    async mounted() {
        this.settings = this.$attrs.settings;
        await this.loaderEvent();

        // wait until data is loaded to show the first tab
        this.activeTab = this.curriculum.objective_type_order[0];
        let firstTab = this.objective_types[this.type_order[0]].id;
        // the 'active'-state does only need to be set programmatically for the initial tab
        // the rest will be handled by the default nav-tabs behaviour
        $('#' + firstTab + '-tab')[0].classList.add('active');
        $('#Type-' + firstTab).tab('show');

        // terminal objectives
        this.$eventHub.on('terminal-objective-added', (terminal) => {
            const type = terminal.type;
            if (!this.type_objectives[type.id]) {
                this.addNewType(type);
            }

            this.max_ids[type.id] = terminal.id;
            this.type_objectives[type.id].push(terminal);
        });

        this.$eventHub.on('terminal-objective-updated', (updatedTerminal) => {
            let terminal = this.type_objectives[updatedTerminal.objective_type_id]?.find(t => t.id === updatedTerminal.id);
            // objective-type got changed
            if (terminal === undefined) {
                // changed objective-type was not in use before
                if (this.type_objectives[updatedTerminal.objective_type_id] === undefined) {
                    this.addNewType(updatedTerminal.type);
                }
                // find original objective
                Object.values(this.type_objectives).forEach(type => {
                    let objective = type.find(obj => obj.id === updatedTerminal.id);
                    if (objective !== undefined) {
                        terminal = objective;
                        return;
                    }
                });
                
                // move the enabling-objectives from the old model to the new one
                updatedTerminal.enabling_objectives = terminal.enabling_objectives;
                // add updated objective to its new type
                this.type_objectives[updatedTerminal.objective_type_id].push(updatedTerminal);
                // remove old objective
                const index = this.type_objectives[terminal.objective_type_id].indexOf(terminal);
                this.type_objectives[terminal.objective_type_id].splice(index, 1);

                // update order-ids
                for (let i = index; i < this.type_objectives[terminal.objective_type_id].length; i++) {
                    this.type_objectives[terminal.objective_type_id][i].order_id--;
                }
                
                // update max-ids 
                this.max_ids[updatedTerminal.objective_type_id] = updatedTerminal.id;
                if (this.max_ids[terminal.objective_type_id] === terminal.id) {
                    let type = this.type_objectives[terminal.objective_type_id];
                    this.max_ids[terminal.objective_type_id] = type[type.length - 1]?.id ?? 0;
                }
            } else {
                Object.assign(terminal, updatedTerminal);
            }
        });

        // enabling objectives
        this.$eventHub.on('enabling-objective-added', (enabling) => {
            let terminal;
            for (const arr of Object.values(this.type_objectives)) {
                terminal = arr.find(terminal => terminal.id === enabling.terminal_objective_id);
                if (terminal) break;
            }

            terminal.enabling_objectives.push(enabling);
        });

        this.$eventHub.on('enabling-objective-updated', (updatedEnabling) => {
            let terminal;
            for (const arr of Object.values(this.type_objectives)) {
                terminal = arr.find(terminal => terminal.id === updatedEnabling.terminal_objective_id);
                if (terminal) break;
            }

            let enabling = terminal.enabling_objectives.find(e => e.id === updatedEnabling.id);

            Object.assign(enabling, updatedEnabling);
        });

        this.$eventHub.on('enabling-objectives-reordered', (data) => {
            const type_id = data.type_id;
            const terminal_id = data.objectives[0].terminal_objective_id;

            this.type_objectives[type_id].find(t => t.id === terminal_id).enabling_objectives = data.objectives;
        });

        this.$eventHub.on('objective-deleted', (deletedObjective) => {
            if (deletedObjective.terminal_objective_id === undefined) { // terminal
                let index = this.type_objectives[deletedObjective.objective_type_id].findIndex(t => t.id === deletedObjective.id);
                this.type_objectives[deletedObjective.objective_type_id].splice(index, 1);
            } else { // enabling
                let terminal;
                for (const arr of Object.values(this.type_objectives)) {
                    terminal = arr.find(t => t.id === deletedObjective.terminal_objective_id);
                    if (terminal) break;
                }

                let index = terminal.enabling_objectives.findIndex(e => e.id === deletedObjective.id);
                terminal.enabling_objectives.splice(index, 1);
            }
        });
    },
    computed: {
        dragOptions() {
            return {
                animation: 200,
                delay: 200,
                delayOnTouchOnly: true,
                group: 'objective-types',
                disabled: this.curriculum.owner_id != this.$userId,
            };
        },
    },
    components: {
        TerminalObjectiveModal,
        EnablingObjectiveModal,
        MoveTerminalObjectiveModal,
        ObjectiveBox,
        EnablingObjectives,
        Select2,
        draggable,
    },
}
</script>