<template>
    <div id="show-achievements">
        <div id="header" class="d-flex align-items-center py-4">
            <div id="fixed-header" class="d-flex position-fixed w-100 px-3">    
                <span class="d-flex align-items-center">
                    Ziele / Namen
                    <i
                        class="fa fa-gear text-secondary ml-1 p-1 pointer"
                        style="font-size: 1rem;"
                        @click="$modal.show('plan-achievements-options-modal');"
                    ></i>
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
                    class="terminal pointer"
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
                        class="d-flex w-100 enabling"
                    >
                        <span
                            class="pl-2"
                            v-html="ena.title"
                        ></span>
                        <span v-for="(user, index) in users">
                            <span v-if="ena.achievements.length === 0"
                                class="d-flex justify-content-center align-items-center h-100 status-0"
                            >
                                <i class="fa fa-circle"></i>
                            </span>
                            <span v-else-if="ena.achievements.length === users.length"
                                class="d-flex justify-content-center align-items-center h-100"
                                :class="'status-' + ena.achievements[index].status[1]"
                            >
                                <i class="fa fa-circle"></i>
                            </span>
                            <span v-else
                                class="d-flex justify-content-center align-items-center h-100"
                                :class="'status-' + (ena.achievements.find(ach => ach.user_id === user.id)?.status[1] ?? '0')"
                            >
                                <i class="fa fa-circle"></i>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <plan-achievements-options-modal></plan-achievements-options-modal>
    </div>
</template>
<script>
export default {
    props: {
        terminal: [],
        enabling: [],
        users: [],
    },
    data() {
        return {
            objectives: [],
        }
    },
    mounted() {
        let obj = [];
        // preset needed attributes to terminal-objective placeholder
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
        toggleUnset(bool) {
            if (!bool) { // show competencies
                [...document.getElementsByClassName('enabling d-none')].forEach(elem => {
                    elem.classList.add('d-flex');
                    elem.classList.remove('d-none');
                });
            } else { // hide competencies
                let unsetElem = [...document.getElementsByClassName('status-0')].map(elem => elem.parentElement.parentElement);
                let parentElem = [];
                // if only one user is selected, the achievements-per-competence ratio is 1:1
                if (this.users.length === 1) {
                    parentElem = unsetElem;
                } else { // else it's n:1
                    let counter = 0;
                    // only get elements which are listed n times
                    while (counter < unsetElem.length) {
                        const first = unsetElem[counter];
                        let equal = true;
                        // if there are x users, check if the next (x-1) elements are the same
                        for (let i = 1; i < this.users.length; i++) {
                            counter++; // get next index
                            const next = unsetElem[counter];
                            // compare its first listed element with the next listed one
                            if (first != next) { // if they're not the same, stop the loop
                                equal = false;
                                break;
                            }
                        }
                        // if this element appears n times, it should be hidden
                        if (equal) {
                            parentElem.push(first);
                            counter++; // needs +1, since current counter-index would refer to this element
                        }
                    }
                }
                // hide all needed elements
                for (let i = 0; i < parentElem.length; i++) {
                    const element = parentElem[i];
                    element.classList.add('d-none');
                    element.classList.remove('d-flex');
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
}
</script>
<style>
#show-achievements {
    p { margin: 0px !important; }
    #header {
        margin: -12px -16px 0px;
        
        > #fixed-header {
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
    }
    #achievements {
        > div {
            > .terminal {
                padding: 8px 0px;
                font-size: 1.05rem;
                border-top: 3px solid #dee2e6;
                border-bottom: 3px solid #dee2e6;
    
                &:hover { background-color: #e9ecef; }
                .fa-angle-up { transition: 0.3s transform; }
                &:not(.collapsed) .fa-angle-up { transform: rotate(-180deg); }
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