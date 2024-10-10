<template>
    <div id="show-achievements">
        <div id="header" class="d-flex w-100">
            <span class="flex-fill">Ziele / Namen</span>
            <span v-for="user in users"
                class="flex-fill text-center"
            >
                {{ user.firstname }} {{ user.lastname }}
            </span>
        </div>
        <div id="achievements">
            <div v-for="ter in objectives">
                <div
                    class="terminal pointer collapsed"
                    data-toggle="collapse"
                    :data-target="'#terminal_' + ter.terminal_objective.id"
                    aria-expanded="true"
                >
                    <span class="float-left mr-1" v-html="ter.terminal_objective.title"></span>
                    <span class="fa fa-angle-up"></span>
                </div>
                <div :id="'terminal_' + ter.terminal_objective.id" class="collapse show">
                    <div v-for="ena in ter.terminal_objective.enabling_objectives"
                        class="d-flex w-100 enabling"
                    >
                        <span
                            class="flex-fill pl-2"
                            v-html="ena.title"
                        ></span>
                        <span v-for="(user, index) in users"
                            class="flex-fill"
                        >
                            <span v-if="ena.achievements.length === users.length || ena.achievements.length === 0"
                                class="d-flex justify-content-center align-items-center h-100"
                                :class="'status-' + (ena.achievements[index]?.status[1] ?? '0')"
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
}
</script>
<style scoped>
#show-achievements {
    & #header {
        padding: 8px 0px;

        & > span {
            font-size: 1.25rem;
            font-weight: 700;
            min-width: 17%;
    
            &:first-child { min-width: 15%; max-width: 25%; }
        }
    }
    & #achievements {
        & .terminal {
            padding: 8px 0px;
            font-size: 1.05rem;
            border-top: 3px solid #dee2e6;
            border-bottom: 3px solid #dee2e6;

            &:hover { background-color: #e9ecef; }
            & > .fa { transition: 0.3s transform; }
            &:not(.collapsed) > .fa { transform: rotate(-180deg); }
        }
        & .enabling {
            padding: 10px 0px;

            &:not(:first-child) { border-top: 1px solid #dee2e6; }
            & > :first-child { min-width: 15%; max-width: 25%; }
            & .fa { font-size: 1.5rem; }
            & .status-0 { color: #d2d6de !important; }
            & .status-1 { color: #00a65a !important; }
            & .status-2 { color: #fd7e14 !important; }
            & .status-3 { color: #dd4b39 !important; }
        }
    }

}
</style>