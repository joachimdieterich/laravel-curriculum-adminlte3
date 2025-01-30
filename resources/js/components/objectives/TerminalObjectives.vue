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
                <div v-for="objective in filterTerminalObjectives(typetab)"
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
            settings: {
                last: null,
            },
            max_ids: {},
            typetabs: [],
            activetab: null,
            currentCurriculaEnrolments: null,
            currentTerminalObjective: null,
            currentEnablingObjective: null,
            terminal_objectives: {},
        }
    },
    methods: {
        filterTerminalObjectives(typetab) {
            let filteredTerminalObjectives = this.terminal_objectives;
            filteredTerminalObjectives = filteredTerminalObjectives.filter(
                t => t.objective_type_id === typetab
            );
            this.max_ids[typetab] = filteredTerminalObjectives[filteredTerminalObjectives.length-1].id;
            return filteredTerminalObjectives;
        },
        getTypeTitle(id) {
            return this.objectivetypes.filter(
                t => t.id === id
            );
        },
        setActiveTab(typetab) {
            this.activetab = typetab;
        },
        loadObjectives(objective_type_id = 0) {
            axios.get('/curricula/' + this.curriculum.id + '/objectives')
                .then(response => {
                    this.terminal_objectives = response.data.terminal_objectives;

                    if (this.terminal_objectives.length !== 0) {
                        this.settings.last = this.terminal_objectives[this.terminal_objectives.length-1].id;
                        this.typetabs = [...new Set(this.terminal_objectives.map(t => t.objective_type_id))];
                        if (this.curriculum.objective_type_order) {
                            if (this.curriculum.objective_type_order.length ===  this.typetabs.length) {
                                this.typetabs = this.curriculum.objective_type_order;
                            }
                        }
                        if (objective_type_id === 0) {
                            this.activetab = this.typetabs[0];
                        }
                    }
                })
                .catch(e => {
                    console.log(e);
                });
        },
        externalEvent: function(ids) {
            this.reloadEnablingObjectives(ids);
        },
        async reloadEnablingObjectives(ids) {
            try {
                this.terminal_objectives = (await axios.post('/curricula/'+this.curriculum.id+'/achievements', {'user_ids' : ids})).data.curriculum.terminal_objectives;
            } catch(e) {
                console.log(e);
            }
        },
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
        this.loadObjectives();

        //terminal Objectives
        this.$eventHub.on('terminal-objective-added', function(newTerminalObjective) {
            this.loadObjectives(this.activetab);
        }.bind(this));

        this.$eventHub.on('editTerminalObjectives', (objective) => {
            this.globalStore?.showModal('terminal-objective-modal', objective);
        });

        this.$eventHub.on('terminal-objective-updated', () => {
            this.loadObjectives(this.activetab);
        });

        //enabling Objectives
        this.$eventHub.on('enablingObjective-added', function(newEnablingObjective) {
            this.globalStore?.closeModal('enabling-objective-modal');
            this.loadObjectives(this.activetab);
        }.bind(this));
        this.$eventHub.on('editEnablingObjectives', (objective) => {
            this.globalStore?.showModal('enabling-objective-modal', objective);
        });
        this.$eventHub.on('enablingObjective-updated', () => {
            this.globalStore?.closeModal('enabling-objective-modal');
            this.loadObjectives(this.activetab);
        });

        this.$eventHub.on('objective-deleted', function(deletedObjective) {
            console.log(deletedObjective);
            this.activetab = (deletedObjective.type === 'terminal') ? deletedObjective.objective.objective_type_id : deletedObjective.objective.terminal_objective.objective_type_id;
            this.loadObjectives(this.activetab);
        }.bind(this));
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