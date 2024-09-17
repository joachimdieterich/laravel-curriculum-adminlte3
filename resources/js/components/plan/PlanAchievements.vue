<template>
    <div id="show-achievements">
        <table class="table">
            <thead>
                <tr>
                    <th>Ziele / Namen</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</template>
<script>
export default {
    props: {
        terminal: {},
        enabling: {},
        users: [],
    },
    mounted() {
        const achievements = {
            terminal: this.terminal,
            enabling: this.enabling,
        };

        const table = document.querySelector('#show-achievements .table');
        const thead = table.firstChild;
        const tbody = table.lastChild;

        // list all names in the table-header
        this.users.forEach(user => {
            const th = document.createElement('th');
            th.innerText = user.firstname + ' ' + user.lastname;
            th.style.width = 'calc(100% / ' + (this.users.length + 1) + ')';
            thead.firstChild.appendChild(th);
        });
        // list each terminal-objective-title in its own row
        achievements.terminal.forEach(ter => {
            const terminal_tr = document.createElement('tr');
            const terminal_td = document.createElement('td');

            terminal_tr.id = 'terminal_' + ter.terminal_objective_id;
            terminal_tr.classList.add('thick-line');
            terminal_td.innerHTML = ter.terminal_objective.title;

            terminal_tr.appendChild(terminal_td);
            tbody.appendChild(terminal_tr);

            // list each enabling-objective with its given achievement status for the corresponding user
            ter.terminal_objective.enabling_objectives.forEach(ena => {
                const enabling_tr = document.createElement('tr');
                const enabling_td = document.createElement('td');

                enabling_td.innerHTML = ena.title;
                enabling_tr.appendChild(enabling_td);

                this.addAchievementCells(enabling_tr, ena.achievements);

                tbody.appendChild(enabling_tr);
            })
        });

        achievements.enabling.forEach(ena => {
            let terminal_tr = document.getElementById('terminal_' + ena.enabling_objective.terminal_objective_id);
            const enabling_tr = document.createElement('tr');
            const enabling_td = document.createElement('td');

            // if terminal-objective doesn't exist, create a new terminal-row
            if (terminal_tr === null) {
                const tr = document.createElement('tr');
                const td = document.createElement('td');

                tr.id = 'terminal_' + ena.enabling_objective.terminal_objective_id;
                tr.classList.add('thick-line');
                td.innerHTML = ena.enabling_objective.terminal_objective.title;

                tr.appendChild(td);
                tbody.appendChild(tr);

                terminal_tr = tr; // save newly created terminal-tr to append the enabling-tr
            }
            
            // create an enabling-row
            enabling_td.innerHTML = ena.enabling_objective.title;
            enabling_tr.appendChild(enabling_td);

            this.addAchievementCells(enabling_tr, ena.enabling_objective.achievements);

            // find its last enabling-objective tr
            const last_enabling =
                $(terminal_tr).closest('tr').nextAll('.thick-line')[0]?.previousElementSibling // search next terminal-tr
                ?? terminal_tr.parentElement.lastElementChild; // if there's no next terminal-tr, get last tr of table
            // and add the new enabling-row
            last_enabling.after(enabling_tr);
        });
    },
    methods: {
        addAchievementCells(tr, achievements) {
            this.users.forEach(user => {
                const td = document.createElement('td');
                const i = document.createElement('i');
                let status = '0';

                if (achievements.length > 0) {
                    // find out if there's a set achievement and get the teacher's value | if not => 0
                    status = achievements.find(achievement => achievement.user_id === user.id)?.status[1] ?? '0';
                }

                td.classList.add('status-' + status);
                i.classList.add('fa', 'fa-circle');

                td.appendChild(i);
                tr.appendChild(td);
            });
        },
    },
}
</script>
<style scoped>
#show-achievements {
    & >>> p { margin: 0px; color: black; }
    & >>> td { padding: 10px; vertical-align: middle; }
    & >>> td:not(:first-child) { text-align: center; font-size: 1.5rem; }
    & >>> tr:hover :not(.status) { background-color: unset; }
    & >>> tr:first-child,
    & >>> tr:first-child th { border-top: 0px; }
    & >>> th {
        font-size: 1.25rem;
        text-align: center;

        &:first-child { min-width: 15%; max-width: 25%; text-align: left; }
    }
    & >>> .thick-line {
        border-top: 3px solid #dee2e6;
        border-bottom: 3px solid #dee2e6;
    }
    & >>> .status-0 { color: #d2d6de !important; }
    & >>> .status-1 { color: #00a65a !important; }
    & >>> .status-2 { color: #fd7e14 !important; }
    & >>> .status-3 { color: #dd4b39 !important; }
}
</style>