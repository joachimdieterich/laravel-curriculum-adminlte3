<template>
    <span v-if="type === 'enabling' && settings.edit === false"
        class="d-flex align-items-center"
        style="gap: 4px;"
    >
        <i
            class="t-18"
            :class="[green_css, fabadge, disabled ? 'text-gray' : 'text-green pointer']"
            :data-count="[green_count]"
            @click.prevent="achieve('1')"
        ></i>
        <i
            class="t-18"
            :class="[orange_css, fabadge, disabled ? 'text-gray' : 'text-orange pointer']"
            :data-count="[orange_count]"
            @click.prevent="achieve('2')"
        ></i>
        <i
            class="t-18"
            :class="[red_css, fabadge, disabled ? 'text-gray' : 'text-red pointer']"
            :data-count="[red_count]"
            @click.prevent="achieve('3')"
        ></i>
        <i
            class="t-18 text-gray"
            :class="[white_css, fabadge, disabled ? '' : 'pointer']"
            :data-count="[white_count]"
            @click.prevent="achieve('0')"
        ></i>
        <span v-if="objective.achievements?.length === 1
                && !settings.achievements"
            style="line-height: 1; white-space: nowrap;"
        >
            {{ objective.achievements[0].updated_at.substring(0, 10) }}
        </span>
    </span>
</template>
<script>
import {useDatatableStore} from "../../store/datatables";

export default {
    props: {
        objective: {},
        type: {},
        settings: {},
        disabled: false,
        users: {
            type: Array,
            default: () => [],
        },
    },
    setup() {
        const store = useDatatableStore();
        return {
            store,
        }
    },
    data() {
        return {
            status: '00',
            green: 'far fa-circle',
            orange: 'far fa-circle',
            red: 'far fa-circle',
            white: 'far fa-circle',
            green_student_count: 0,
            green_teacher_count: 0,
            orange_student_count: 0,
            orange_teacher_count: 0,
            red_student_count: 0,
            red_teacher_count: 0,
            white_student_count: 0,
            white_teacher_count: 0,
        };
    },
    methods: {
        async achieve(status) {
            if (this.disabled) return;

            let selected = this.users;
            if (selected.length === 0) {
                selected = this.store.getSelectedIds('curriculum-user-datatable');
            }

            const achievement = {
                referenceable_id: this.objective.id,
                referenceable_type: (this.type === 'terminal' ? 'App\\TerminalObjective' : 'App\\EnablingObjective'),
                user_id: selected,
                status: status
            }
            try {
                await axios.post('/achievements', achievement)
                    .then(response => {
                        if (this.users.length > 1) {
                            this.status = '0' + response.data[0].status.toString().charAt(1);
                        } else {
                            this.status = response.data[0].status.toString();
                        }

                        if (this.settings.referenceable_id && this.settings.referenceable_type) {
                            this.$eventHub.emit('achievements-set', {
                                objective_id: this.objective.id,
                                referenceable_id: this.settings.referenceable_id,
                                referenceable_type: this.settings.referenceable_type,
                                user_id: selected,
                                achievements: response.data,
                            });
                        }
                    });
//                    calculateProgress(); //todo?
            } catch(error) {
                alert(error);
            }
        },
        calculate_css(number) {
            let status = "far fa-circle";

            if (this.status.charAt(0) === number &&
                this.status.charAt(1) === number) {
                status = "fa fa-check-circle";
            } else if (this.status.charAt(0) === number) {
                status = "fa fa-circle";
            } else if (this.status.charAt(1) === number) {
                status = "far fa-check-circle";
            }

            return status;
        },
        calculate_count(number) {
            let student = 0, teacher = 0;
            if (typeof this.objective.achievements !== 'undefined') {
                if (typeof this.objective.achievements[0] === 'object' && this.settings.achievements === true) {
                    for (let i = 0; i < (this.objective.achievements).length; i++) {
                        //console.log('ena:'+ this.objective.id +' lenght:'+ this.objective.achievements.length+' i:'+i+' student: '+this.objective.achievements[i].status.charAt(0)+' teacher: '+ this.objective.achievements[i].status.charAt(1));
                        student += this.objective.achievements[i].status.charAt(0) === number;
                        teacher += this.objective.achievements[i].status.charAt(1) === number;
                    }
                    return teacher; //todo option to show students self achievement status
                } else {
                    if (typeof this.objective.achievements !== 'undefined') {
                        if (typeof this.objective.achievements[0] === 'object') {
                            this.status = this.objective.achievements[0].status;
                        }
                    } else {
                        this.status = '00';
                    }
                }
            } else {
                return;
            }
        }
    },
    computed: {
        green_css: function() {
            return this.calculate_css('1');
        },
        orange_css: function() {
            return this.calculate_css('2');
        },
        red_css: function() {
            return this.calculate_css('3');
        },
        white_css: function() {
            return this.calculate_css('0');
        },
        //counts
        green_count: function() {
            return this.calculate_count('1');
        },
        orange_count: function() {
            return this.calculate_count('2');
        },
        red_count: function() {
            return this.calculate_count('3');
        },
        white_count: function() {
            return this.calculate_count('0');
        },
        fabadge: function() {
            if (window.Laravel.permissions.indexOf('achievement_access') !== -1) {
                return "fabadge";
            }
        }
    },
    created() {
        if (typeof this.objective.achievements !== 'undefined') {
            if (typeof this.objective.achievements[0] === 'object') {
                this.status = this.objective.achievements[0].status;
            }
        }
    },
}
</script>
