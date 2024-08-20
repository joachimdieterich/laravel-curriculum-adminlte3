<template>
    <div id="show-achievements">
        <h2 id="user-name"></h2>
        <span class="custom-control custom-switch custom-switch-on-green">
            <input id="toggleOnlySuccessful" class="custom-control-input" type="checkbox" v-model="toggleOnlySuccessful"/>
            <label for="toggleOnlySuccessful" class="custom-control-label">Nur erfolgreiche anzeigen</label>
        </span>
        <span class="custom-control custom-switch custom-switch-on-green">
            <input id="toggleUnassessed" class="custom-control-input" type="checkbox" v-model="toggleUnassessed"/>
            <label for="toggleUnassessed" class="custom-control-label">Nicht eingesch&auml;tzte ausblenden</label>
        </span>
        <br/>
        <table class="table">
            <tbody></tbody>
        </table>
    </div>
</template>
<script>
export default {
    props: {
        terminal: {},
        enabling: {},
        user: {},
    },
    data() {
        return {
            toggleOnlySuccessful: false,
            toggleUnassessed: false,
        }
    },
    async mounted() {
        const achievements = {
            terminal: this.terminal,
            enabling: this.enabling,
        };

        const table = document.querySelector('#show-achievements tbody');
        document.getElementById('user-name').innerText = `${this.user.firstname} ${this.user.lastname}`;

        // create a new row for each terminal-objective
        achievements.terminal.forEach(terminal => {
            const ter_obj = terminal.terminal_objective;
            const tr = document.createElement('tr');

            // the first cell should be the title of the terminal-objective
            this.addCell(tr, ter_obj.title);

            // every following cell in this row is an enabling-objective
            ter_obj.enabling_objectives.forEach(enabling => {
                this.addCell(tr, enabling.title, (enabling.achievements[0]?.status[1] ?? '0'));
            });

            table.appendChild(tr);
        });

        let objectives = {};
        // first connect enabling-objectives with the same terminal-objective into the same category
        achievements.enabling.forEach((enabling, index) => {
            const terminal_id = enabling.enabling_objective.terminal_objective.id;

            if (objectives[terminal_id] === undefined) {
                objectives[terminal_id] = [index];
            } else {
                objectives[terminal_id].push(index);
            }
        });

        for (const [terminal_id, enabling_indices] of Object.entries(objectives)) {
            const ter_obj = achievements.enabling[enabling_indices[0]].enabling_objective.terminal_objective;
            const tr = document.createElement('tr');

            // the first cell should be the title of the terminal-objective
            this.addCell(tr, ter_obj.title);

            // add enabling-objectives from the response-data, based of the corresponding indices
            enabling_indices.forEach(index => {
                const enabling = achievements.enabling[index].enabling_objective;
                this.addCell(tr, enabling.title, (enabling.achievements[0]?.status[1] ?? '0'));
            });

            table.appendChild(tr);
        }
    },
    methods: {
        /**
         * creates a td-element and appends it to the given tr-element
         * @param {HTMLTableRowElement} tr tr-element of which the td-cell should be appended to
         * @param {String} title text for innerHTML
         * @param {String} status status-number of the achievement
         */
        addCell(tr, title, status = false) {
            const td = document.createElement('td');
            td.innerHTML = title; // title is wrapped around a p-tag

            if (status !== false) {
                // the background should be colored based on the teacher-side of the achievement
                td.classList.add('status-' + status); // classname => status-0/1/2/3
            }

            tr.appendChild(td);
        },
    },
    watch: {
        toggleOnlySuccessful(bool) {
            if (bool) {
                $('#show-achievements tr td:not(:first-child):not(.status-1)').hide();
            } else {
                let param = '#show-achievements td';
                if (this.toggleUnassessed) param += ':not(.status-0)';
                
                $(param).show();
            }
        },
        toggleUnassessed(bool) {
            if (bool) {
                $('#show-achievements td.status-0').hide();
            } else if ($('#toggleOnlySuccessful')[0].checked === false) {
                $('#show-achievements td.status-0').show();
            }
        },
    },
}
</script>
<style scoped>
#show-achievements {
    print-color-adjust: exact;

    & >>> p { margin: 0px; color: black; }
    & >>> td { padding: 10px; }
    & >>> tr:hover :not(.status) { background-color: unset; }
    & >>> tr:first-child,
    & >>> tr:first-child td { border-top: 0px; }
    & >>> .status-0 { background-color: #d2d6de !important; }
    & >>> .status-1 { background-color: #00a65a !important; }
    & >>> .status-2 { background-color: #fd7e14 !important; }
    & >>> .status-3 { background-color: #dd4b39 !important; }
}
</style>