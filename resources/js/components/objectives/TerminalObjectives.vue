<template>
    <div>
        <ul
            id="terminalObjectivesTopNav"
            class="nav nav-pills bg-gray-light position-sticky"
            style="top: 56px; z-index: 100"
        >
           <draggable
                class="nav nav-pills"
                v-model="typetabs"
                :disabled="curriculum.owner_id != $userId"
                item-key="id"
                @start="drag=true"
                @end="handleTypeMoved"
            >
                <template #item="{ element: typetab }">
                    <li
                        class="nav-item pl-0 pr-2 pb-2 pt-2"
                    >
                        <a
                            :href="'#tab_' + typetab"
                            class="nav-link"
                            :class="(activetab == typetab) ? 'active' : ''"
                            data-toggle="tab"
                            @click="setActiveTab(typetab)"
                        >
                            {{ getTypeTitle(typetab)[0]['title'] }}
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
        <div v-for="typetab in typetabs"
            class="tab-content"
        >
            <div
                :id="'tab_' + typetab"
                class="tab-pane"
                :class="(activetab == typetab) ? 'active show' : ''"
            >
                <div v-for="objective in type_objectives[typetab]"
                    :id="'terminalObjective_' + objective.id"
                >
                    <div class="row">
                        <div class="col-12 terminal-row">
                            <ObjectiveBox
                                type="terminal"
                                :objective="objective"
                                :settings="settings"
                                :max_id="max_ids[activetab]"
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
                                    :terminalobjective="objective"
                                    :objectives="objective.enabling_objectives"
                                    :settings="settings"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.tab-pane -->
        </div> <!-- /.tab-content -->

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
                    :objective_type_id="activetab"
                    :settings="settings"
                    :max_id="max_ids[activetab]"
                />
            </div>
        </div>

        <Teleport to="body">
            <TerminalObjectiveModal/>
            <EnablingObjectiveModal/>
        </Teleport>
    </div>
</template>
<script>
import ObjectiveBox from './ObjectiveBox.vue';
import EnablingObjectives from './EnablingObjectives.vue';
import TerminalObjectiveModal from "./TerminalObjectiveModal.vue";
import EnablingObjectiveModal from "./EnablingObjectiveModal.vue";
import Select2 from '../forms/Select2.vue';
import draggable from "vuedraggable";
import {useGlobalStore} from "../../store/global";

export default {
    props: {
        curriculum: {
            type: Object,
        },
        objectivetypes: {
            type: Array,
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
            settings: {},
            max_ids: {},
            typetabs: [],
            activetab: null,
            currentCurriculaEnrolments: null,
            currentTerminalObjective: null,
            currentEnablingObjective: null,
            type_objectives: {},
        }
    },
    methods: {
        getTypeTitle(id) {
            return this.objectivetypes.filter(type => type.id === id);
        },
        setActiveTab(typetab) {
            this.activetab = typetab;
        },
        loaderEvent(objective_type_id = 0) {
            axios.get('/curricula/' + this.curriculum.id + '/objectives')
                .then(response => {
                    let terminal = {};
                    this.typetabs.forEach(type => terminal[type] = []);

                    response.data.forEach(t => {
                        terminal[t.objective_type_id].push(t);
                    });

                    this.typetabs.forEach(type => this.max_ids[type] = terminal[type][terminal[type].length - 1].id);

                    Object.assign(this.type_objectives, terminal);

                    if (this.type_objectives.length === 0) return;

                    if (this.curriculum.objective_type_order) {
                        if (this.curriculum.objective_type_order.length ===  this.typetabs.length) {
                            this.typetabs = this.curriculum.objective_type_order;
                        }
                    }
                    if (objective_type_id === 0) {
                        this.activetab = this.typetabs[0];
                    }
                })
                .catch(e => {
                    console.log(e);
                });
        },
        // TODO: needs rework
        // externalEvent: function(ids) {
        //     this.reloadEnablingObjectives(ids);
        // },
        // async reloadEnablingObjectives(ids) {
        //     try {
        //         this.type_objectives = (await axios.post('/curricula/' + this.curriculum.id + '/achievements', { 'user_ids' : ids })).data.curriculum.terminal_objectives;
        //     } catch(e) {
        //         console.log(e);
        //     }
        // },
        handleTypeMoved() {
            // Send the entire list of statuses to the server
            axios.put("/curricula/" + this.curriculum.id + "/syncObjectiveTypesOrder", { objective_type_order: this.typetabs })
                .catch(err => {
                    console.log(err.response);
                    alert(err.response.statusText);
                });
        },
    },
    mounted() {
        this.settings = this.$attrs.settings;
        this.typetabs = this.objectivetypes.map(type => type.id);
        this.loaderEvent();

        // terminal objectives
        this.$eventHub.on('terminal-objective-added', (terminal) => {
            const type = terminal.type;
            if (!this.type_objectives[type.id]) {
                this.objectivetypes.push(type);
                this.typetabs.push(type.id);
                this.type_objectives[type.id] = [];
            }

            this.max_ids[type.id] = terminal.id;
            this.type_objectives[type.id].push(terminal);
        });

        this.$eventHub.on('terminal-objective-updated', (updatedTerminal) => {
            let terminal = this.type_objectives[updatedTerminal.objective_type_id].find(t => t.id === updatedTerminal.id);

            Object.assign(terminal, updatedTerminal);
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
    components: {
        TerminalObjectiveModal,
        EnablingObjectiveModal,
        ObjectiveBox,
        EnablingObjectives,
        Select2,
        draggable,
    },
}
</script>