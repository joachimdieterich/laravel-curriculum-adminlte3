<template>
    <div class="dropdown d-print-none">
        <button
            type="button"
            class="btn"
            :class="textcolor === '#000' ? 'btn-icon' : 'btn-icon-alt'"
            data-bs-toggle="dropdown"
            aria-label="Dropdown menu"
            aria-expanded="false"
        >
            <i class="fa fa-caret-down"></i>
        </button>

        <div class="dropdown-menu dropdown-menu-end">
            <button
                type="button"
                class="dropdown-item"
                @click="editObjective()"
            >
                <i class="fa fa-pencil"></i>
                {{ trans('global.' + type + 'Objective.edit') }}
            </button>
            <button v-if="type === 'terminal'"
                type="button"
                class="dropdown-item"
                @click="moveObjective()"
            >
                <i class="fa fa-repeat"></i>
                {{ trans('global.terminalObjective.move_to_curriculum') }}
            </button>

            <hr class="my-1">

            <button
                type="button"
                class="dropdown-item text-danger"
                @click="emitDeleteEvent()"
            >
                <i class="fa fa-trash"></i>
                {{ trans('global.' + type + 'Objective.delete') }}
            </button>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        menuEntries: {
            type: Object,
            default: null,
        },
        objective: {
            type: Object,
            default: null,
        },
        textcolor: {
            type: String,
            default: '#000',
        },
    },
    methods: {
        editObjective() {
            this.globalStore.showModal(this.type + '-objective-modal', this.objective);
        },
        moveObjective() {
            this.globalStore.showModal(this.type + '-objective-modal', this.objective);
        },
        emitDeleteEvent(entry) {
            this.$eventHub.emit('confirm-objective-delete', {
                objective: this.objective,
                model: this.type + 'Objective',
            });
        },
    },
    computed: {
        type() {
            return this.objective.terminal_objective_id ? 'enabling' : 'terminal';
        },
    },
}
</script>