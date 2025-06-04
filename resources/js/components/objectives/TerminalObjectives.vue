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
                :aria-labelledby="type.id + '-tab'"
            >
                <div
                    class="d-flex flex-column"
                    style="gap: 10px;"
                >
                    <div v-for="terminal in type.terminal_objectives"
                        :id="'terminalObjective_' + terminal.id"
                        class="objectives"
                    >
                        <ObjectiveBox
                            type="terminal"
                            :objective="terminal"
                            :settings="settings"
                            :max_id="max_ids[type.id]"
                            @createTerminalObjective="(createObjective) => {
                                this.createTerminalObjective(createObjective);
                            }"
                            @update="(objective) => {
                                this.currentTerminalObjective = objective;
                                this.globalStore?.closeModal('terminal-objective-modal');
                            }"
                        />

                        <EnablingObjectives
                            :terminalobjective="terminal"
                            :objectives="terminal.enabling_objectives"
                            :settings="settings"
                        />
                    </div>
                </div>
            </div>

            <div v-if="settings.edit"
                v-permission="'curriculum_edit'"
                class="objectives"
                style="margin: 10px 0px;"
            >
                <ObjectiveBox
                    type="createterminal"
                    :objective="{ curriculum_id: curriculum.id }"
                    :objective_type_id="activeTypeId"
                />
            </div>
        </div>

        <Teleport to="body">
            <TerminalObjectiveModal/>
            <EnablingObjectiveModal/>
            <MoveTerminalObjectiveModal/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="delete_title"
                :description="delete_description"
                @close="() => {
                    this.showConfirm = false;
                }"
                @confirm="() => {
                    this.showConfirm = false;
                    this.delete();
                }"
            />
        </Teleport>
    </div>
</template>
<script>
import ObjectiveBox from './ObjectiveBox.vue';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
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
            showConfirm: false,
            delete_objective: null,
            delete_title: null,
            delete_description: null,
            max_ids: {},
            type_order: [],
            activeTypeId: null,
            currentCurriculaEnrolments: null,
            currentTerminalObjective: null,
            currentEnablingObjective: null,
            objective_types: [],
        }
    },
    methods: {
        setActiveTab(index) {
            this.activeTypeId = this.objective_types[index].id;
        },
        async loaderEvent() {
            await axios.get('/curricula/' + this.curriculum.id + '/objectives')
                .then(response => {
                    this.objective_types = response.data;

                    if (this.objective_types.length > 0) {
                        // get the order by their current index instead of their id
                        this.type_order = this.curriculum.objective_type_order?.map(
                            type_id => this.objective_types.findIndex(type => type.id === type_id)
                        ) ?? this.objective_types.map((t, index) => index);
                    }
                })
                .catch(e => {
                    this.toast.error(this.trans('global.error'));
                    console.log(e);
                });
        },
        delete() {
            this.removeObjective(this.delete_objective);
        },
        externalEvent: function(ids) {
            this.reloadEnablingObjectives(ids);
        },
        reloadEnablingObjectives(ids) {
            axios.post('/curricula/' + this.curriculum.id + '/achievements', { 'user_ids' : ids })
                .then(response => {
                    Object.assign(this.objective_types, response.data);
                })
                .catch(e => {
                    this.toast.error(this.trans('global.error'));
                    console.log(e);
                });
        },
        handleTypeMoved() {
            // active-state needs to be reset, since it changes to a new index
            this.$el.querySelector('.nav-link.active').classList.remove('active');
            document.getElementById(this.activeTypeId + '-tab').classList.add('active');
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
            this.objective_types.push(type);
            this.type_order.push(this.type_order.length); // index-based
            this.activeTypeId = type.id;
            this.$nextTick(() => { // wait for DOM to be updated
                $('#' + type.id + '-tab')[0].click(); // switch to new tab
                this.handleTypeMoved(); // send new order to the server
            });
        },
        removeType(type, changeTab = true) {
            // switch tab first, if the current tab is the one to be removed
            if (changeTab && this.activeTypeId === type.id) {
                this.activeTypeId = this.objective_types[this.type_order[0]].id;
                $('#' + this.activeTypeId + '-tab')[0].click();
            }

            let index = this.objective_types.findIndex(t => t.id === type.id);
            this.objective_types.splice(index, 1);
            this.type_order.splice(index, 1);

            this.handleTypeMoved(); // send new order to the server
        },
        removeObjective(deletedObjective) {
            if (deletedObjective.terminal_objective_id === undefined) { // terminal
                let type = this.objective_types.find(type => type.id === deletedObjective.objective_type_id);
                let index = type.terminal_objectives.findIndex(terminal => terminal.id === deletedObjective.id);
                type.terminal_objectives.splice(index, 1);
                // if no objective remains in this type, remove its tab and its ID from the order
                if (type.terminal_objectives.length === 0) this.removeType(type);
                else if (this.max_ids[type.id] === deletedObjective.id) {
                    this.max_ids[type.id] = type.terminal_objectives[type.terminal_objectives.length - 1].id;
                }
            } else { // enabling
                let terminal;
                for (const type of this.objective_types) {
                    terminal = type.terminal_objectives.find(t => t.id === deletedObjective.terminal_objective_id);
                    if (terminal) break;
                }

                let index = terminal.enabling_objectives.findIndex(e => e.id === deletedObjective.id);
                terminal.enabling_objectives.splice(index, 1);
            }
        },
    },
    async mounted() {
        this.settings = this.$attrs.settings;
        await this.loaderEvent();

        // wait until data is loaded to show the first tab
        if (this.objective_types.length > 0) { // if Curriculum is not empty
            // max-ids are structured with the objective-type-id as key
            this.max_ids = this.objective_types.reduce((acc, type) => {
                acc[type.id] = type.terminal_objectives[type.terminal_objectives.length - 1].id;
                return acc;
            }, {});

            this.activeTypeId = (
                this.curriculum.objective_type_order
                ?? [this.objective_types[0].id] // type-order unset => only one type exists, so get its ID
            )[0];

            let firstTab = this.objective_types[this.type_order[0]].id;
            // the 'active'-state does only need to be set programmatically for the initial tab
            // the rest will be handled by the default nav-tabs behaviour
            $('#' + firstTab + '-tab')[0].classList.add('active');
            $('#Type-' + firstTab).tab('show'); // doing a click() would also work, but the transition seems to be smoother this way
        }

        // terminal objectives
        this.$eventHub.on('terminal-objective-added', (terminal) => {
            const type = terminal.type;
            let obj_type = this.objective_types.find(t => t.id === type.id);
            if (obj_type === undefined) {
                this.addNewType(type);
                this.objective_types[this.objective_types.length - 1].terminal_objectives = [terminal];
            } else {
                this.max_ids[type.id] = terminal.id;
                obj_type.terminal_objectives.push(terminal);
            }
        });

        this.$eventHub.on('terminal-objective-updated', (updatedTerminal) => {
            let type = this.objective_types.find(type => type.id === updatedTerminal.objective_type_id);
            // objective-type was changed and the new type was not in use before
            if (type === undefined) {
                this.addNewType(updatedTerminal.type);
                type = this.objective_types[this.objective_types.length - 1];
                // don't add the updated objective to the new type yet
            }

            let terminal = type.terminal_objectives.find(terminal => terminal.id === updatedTerminal.id);
            // objective-type was changed, so we need to find where the old objective is
            if (terminal === undefined) {
                let old_type;
                // find original objective
                for (const obj_type of this.objective_types) {
                    terminal = obj_type.terminal_objectives.find(t => t.id === updatedTerminal.id);
                    if (terminal !== undefined) {
                        old_type = obj_type;
                        break;
                    }
                }
                
                // move the enabling-objectives from the old model to the new one
                updatedTerminal.enabling_objectives = terminal.enabling_objectives;
                // add updated objective to its new type
                type.terminal_objectives.push(updatedTerminal);
                // remove old objective
                const index = old_type.terminal_objectives.indexOf(terminal);
                old_type.terminal_objectives.splice(index, 1);

                // update order-ids of the old type
                for (let i = index; i < old_type.terminal_objectives.length; i++) {
                    old_type.terminal_objectives[i].order_id--;
                }
                
                // update max-ids 
                this.max_ids[updatedTerminal.objective_type_id] = updatedTerminal.id;
                if (this.max_ids[terminal.objective_type_id] === terminal.id) {
                    let last_terminal = old_type.terminal_objectives[old_type.terminal_objectives.length - 1];
                    this.max_ids[terminal.objective_type_id] = last_terminal?.id ?? 0;
                }

                // remove the old type if its last terminal-objective was moved to another type
                if (old_type.terminal_objectives.length === 0) {
                    this.removeType(old_type, false);
                    this.activeTypeId = type.id;
                    $('#' + this.activeTypeId + '-tab')[0].click();
                }
            } else {
                Object.assign(terminal, updatedTerminal);
            }
        });

        this.$eventHub.on('terminal-objectives-reordered', (data) => {
            const type_id = data.objectives.objective_type_id;
            let objectives = this.objective_types.find(type => type.id === type_id).terminal_objectives;
            let movingIndex = objectives.findIndex(t => t.id === data.objectives.id);
            // objective which was clicked on to be moved
            let movingTerminal = objectives[movingIndex];
            // objective with which it was swapped
            let swappedTerminal = objectives[movingIndex + data.higher]; // higher = 1 or -1

            movingTerminal.order_id = swappedTerminal.order_id;
            swappedTerminal.order_id -= data.higher;

            if (data.higher === 1) { // e.g. [item, move, swap, item] => [item, swap, move, item]
                objectives.splice(movingIndex, 2, swappedTerminal, movingTerminal);
                if (this.max_ids[type_id] === swappedTerminal.id) {
                    this.max_ids[type_id] = movingTerminal.id;
                }
            } else { // e.g. [swap, move, item, item] => [move, swap, item, item]
                objectives.splice(movingIndex - 1, 2, movingTerminal, swappedTerminal);
                if (this.max_ids[type_id] === movingTerminal.id) {
                    this.max_ids[type_id] = swappedTerminal.id;
                }
            }
        });

        // enabling objectives
        this.$eventHub.on('enabling-objective-added', (enabling) => {
            let terminal;
            for (const type of this.objective_types) {
                terminal = type.terminal_objectives.find(terminal => terminal.id === enabling.terminal_objective_id);
                if (terminal) break;
            }

            terminal.enabling_objectives.push(enabling);
        });

        this.$eventHub.on('enabling-objective-updated', (updatedEnabling) => {
            let terminal;
            for (const type of this.objective_types) {
                terminal = type.terminal_objectives.find(terminal => terminal.id === updatedEnabling.terminal_objective_id);
                if (terminal) break;
            }

            let enabling = terminal.enabling_objectives.find(e => e.id === updatedEnabling.id);

            Object.assign(enabling, updatedEnabling);
        });

        this.$eventHub.on('enabling-objectives-reordered', (data) => {
            const terminal_id = data.objectives[0].terminal_objective_id;

            this.objective_types
                .find(type => type.id === data.type_id).terminal_objectives
                .find(t => t.id === terminal_id)
                .enabling_objectives = data.objectives;
        });

        this.$eventHub.on('confirm-objective-delete', (data) => {
            this.delete_objective = data.objective;
            this.delete_title = this.trans('global.' + data.model + '.delete');
            this.delete_description = this.trans('global.' + data.model + '.delete_helper');
            this.showConfirm = true;
        });

        this.$eventHub.on('objective-deleted', (deletedObjective) => {
            this.removeObjective(deletedObjective);
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
        ConfirmModal,
        EnablingObjectives,
        Select2,
        draggable,
    },
}
</script>