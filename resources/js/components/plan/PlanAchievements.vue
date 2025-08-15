<template>
    <div id="show-achievements">
        <div id="header" class="d-flex align-items-center py-4">
            <div id="fixed-header" class="d-flex position-fixed w-100 px-3">    
                <span class="d-flex align-items-center">
                    Ziele / Namen
                    <button
                        class="btn btn-icon link-muted ml-1"
                        :title="trans('global.open_settings')"
                        @click="globalStore.showModal('plan-achievements-options-modal');"
                    >
                        <i class="fa fa-gear"></i>
                    </button>
                </span>
                <span v-for="user in users"
                    class="text-center"
                >
                    {{ user.firstname }} {{ user.lastname }}
                </span>
            </div>
        </div>
        <div id="achievements">
            <div v-for="ter in objectives">
                <div
                    class="terminal pointer px-3"
                    data-toggle="collapse"
                    :data-target="'#terminal_' + ter.terminal_objective.id"
                    aria-expanded="true"
                >
                    <span>
                        {{ htmlToText(ter.terminal_objective.title) }}
                        <span class="fa fa-angle-up"></span>
                    </span>
                </div>
                <div :id="'terminal_' + ter.terminal_objective.id" class="collapse show">
                    <div v-for="ena in ter.terminal_objective.enabling_objectives"
                        class="d-flex enabling px-3 w-100"
                    >
                        <span
                            class="pl-2"
                            v-html="ena.title"
                        ></span>
                        <span v-for="(user, index) in users">
                            <span v-if="ena.achievements.length === 0"
                                class="d-flex justify-content-center align-items-center h-100 status-0"
                                data-date="0"
                            >
                                <i class="fa fa-circle"></i>
                            </span>
                            <span v-else-if="ena.achievements.length === users.length"
                                class="d-flex justify-content-center align-items-center h-100"
                                :class="'status-' + ena.achievements[index].status[1]"
                                :data-date="Date.parse(ena.achievements[index].updated_at)"
                            >
                                <i class="fa fa-circle"></i>
                            </span>
                            <span v-else
                                class="d-flex justify-content-center align-items-center h-100"
                                :class="'status-' + (ena.achievements.find(ach => ach.user_id === user.id)?.status[1] ?? '0')"
                                :data-date="Date.parse(ena.achievements.find(ach => ach.user_id === user.id)?.updated_at ?? 1970)"
                            >
                                <i class="fa fa-circle"></i>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <Teleport to="body">
            <PlanAchievementsOptionsModal/>
        </Teleport>
    </div>
</template>
<script>
import PlanAchievementsOptionsModal from './PlanAchievementsOptionsModal.vue';
import {useGlobalStore} from "../../store/global";

export default {
    props: {
        terminal: {
            type: Array,
            default: null,
        },
        enabling: {
            type: Array,
            default: null,
        },
        users: {
            type: Array,
            default: null,
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
            objectives: [],
        }
    },
    mounted() {
        let obj = [];
        // set needed attributes to terminal-objective as placeholder
        let ter = { terminal_objective: this.enabling[0]?.enabling_objective.terminal_objective };
        let objectives = [];

        this.enabling.forEach(ena => {
            const ena_obj = ena.enabling_objective;
            
            // if current enabling-objective has the same terminal-objective as previous
            if (ena_obj.terminal_objective_id === ter.terminal_objective.id) {
                objectives.push(ena_obj);
            } else { // push placeholder to obj and preset attributes again
                ter.terminal_objective.enabling_objectives = objectives;
                obj.push(structuredClone(ter));
                ter.terminal_objective = ena_obj.terminal_objective;
                objectives = [ena_obj];
            }
        });
        // if there's no enabling-objective, this value will be undefined
        if (ter.terminal_objective !== undefined) {
            ter.terminal_objective.enabling_objectives = objectives;
            obj.push(ter);
        }
        this.objectives = this.terminal.concat(obj);
    },
    methods: {
        filterByTimespan(date) {
            const elements = $('[data-date]');
            // if timespan got cleared, show all objectives again
            if (date == null) {
                for (const element of elements) {
                    element.parentElement.parentElement.classList.add('d-flex');
                    element.parentElement.parentElement.classList.remove('d-none');
                }

                return;
            }

            const begin = Date.parse(date[0]); // begins at 00:00:00
            const end = Date.parse(date[1]); // ends at 23:59:00
            
            if (this.users.length === 1) { // only 1 user is listed
                for (const element of elements) {
                    const updated = element.dataset.date;
                    // check if achievement's last updated date is inside the timespan
                    if (begin > updated || end < updated) {
                        element.parentElement.parentElement.classList.add('d-none');
                        element.parentElement.parentElement.classList.remove('d-flex');
                    }
                }
            } else { // when multiple users are listed
                for (let i = 0; i < elements.length; i += this.users.length) {
                    let hide = true;
                    // check each objective's achievements
                    for (let j = 0; j < this.users.length; j++) {
                        const element = elements[i + j];
                        const updated = element.dataset.date;
                        // if at least one achievement was last updated inside the timespan
                        if (begin <= updated && end >= updated) {
                            hide = false; // don't hide the parent-element
                            break;
                        }
                    }
                    // if all achievements are outside of the timespan, hide the parent-element
                    if (hide) {
                        elements[i].parentElement.parentElement.classList.add('d-none');
                        elements[i].parentElement.parentElement.classList.remove('d-flex');
                    }
                }
            }
        },
        toggleUnset(hide) {
            if (this.users.length === 1) {
                const elements = document.getElementsByClassName('status-0');
                const add = hide ? 'd-none' : 'd-flex';
                const remove = hide ? 'd-flex' : 'd-none';

                for (const element of elements) {
                    element.parentElement.parentElement.classList.add(add);
                    element.parentElement.parentElement.classList.remove(remove);
                }
            } else {
                this.toggleMultipleUnset(hide);
            }
        },
        toggleMultipleUnset(hide) {
            const elements = document.getElementsByClassName('status-0');
            const users_amount = this.users.length; // => n
            const add = hide ? 'd-none' : 'd-flex';
            const remove = hide ? 'd-flex' : 'd-none';

            let counter = 0;

            while (counter < elements.length) {
                const parent = elements[counter].parentElement.parentElement;

                // only hide elements which have n amount of 'status-0'-childs
                if (parent.getElementsByClassName('status-0').length === users_amount) {
                    parent.classList.add(add);
                    parent.classList.remove(remove);
                    counter += users_amount; // skip the next n amount of elements
                } else {
                    counter++; // check next element
                }
            }
        },
        toggleObjectives(bool) {
            if (bool) { // collapse all unfolded objectives
                $('.terminal:not(.collapsed)').trigger('click');
            } else { // unfold all collapsed objectives
                $('.terminal.collapsed').trigger('click');
            }
        }
    },
    components: {
        PlanAchievementsOptionsModal,
    },
}
</script>
<style>
#show-achievements {
    margin: -15px -16px 0px;

    p { margin: 0px !important; }
    #header > #fixed-header {
        padding: 9px 0px;
        background-color: white;
        z-index: 1;
        border-bottom: 3px solid #dee2e6;

        > span {
            font-size: 1.25rem;
            font-weight: 700;
            min-width: 25%;
            flex: 1 1 0px;
        }
    }
    #achievements {
        > div {
            > .terminal {
                padding: 8px 0px;
                font-size: 1.05rem;
                border-top: 3px solid #dee2e6;
                border-bottom: 3px solid #dee2e6;
    
                &:hover { background-color: #e9ecef; }
            }
            &:first-child > .terminal { border-top: none !important; }
        }
        .enabling {
            padding: 10px 0px;

            &:not(:first-child) { border-top: 1px solid #dee2e6; }
            > span {
                min-width: 25%;
                flex: 1 1 0px;
            }
            .fa { font-size: 1.5rem; }
            .status-0 { color: #d2d6de !important; }
            .status-1 { color: #00a65a !important; }
            .status-2 { color: #fd7e14 !important; }
            .status-3 { color: #dd4b39 !important; }
        }
    }
}
</style>