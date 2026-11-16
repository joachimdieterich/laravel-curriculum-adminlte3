<template>
    <div
        class="d-flex align-items-center"
        :style="{ 'color': textcolor }"
    >
        <span v-if="edit_settings"
            class="d-flex d-print-none me-auto"
        >
            <button v-if="(type == 'terminal' && objective.order_id != 0)"
                type="button"
                class="btn"
                :class="textcolor === '#000' ? 'btn-icon' : 'btn-icon-alt'"
                @click="changeOrder(false)"
            >
                <i class="fa fa-arrow-up"></i>
            </button>
            <button v-if="(type == 'terminal' && max_id != objective.id)"
                type="button"
                class="btn"
                :class="textcolor === '#000' ? 'btn-icon' : 'btn-icon-alt'"
                @click="changeOrder(true)"
            >
                <i class="fa fa-arrow-down"></i>
            </button>

            <button v-if="(type == 'enabling' && objective.order_id != 0)"
                type="button"
                class="btn btn-icon text-secondary"
                :aria-label="trans('global.enablingObjective.move_prev')"
                @click="changeOrder(false)"
            >
                <i class="fa fa-arrow-left"></i>
            </button>
            <button v-if="(type == 'enabling' && max_id != objective.id)"
                type="button"
                class="btn btn-icon text-secondary"
                :aria-label="trans('global.enablingObjective.move_next')"
                @click="changeOrder(true)"
            >
                <i class="fa fa-arrow-right"></i>
            </button>
        </span>

        <DropdownButton v-if="edit_settings && (type == 'terminal' || type == 'enabling')"
            :objective.sync="objective"
            :textcolor="textcolor"
        />
    </div>
</template>
<script>
import DropdownButton from './DropdownButton.vue';

export default {
    props: {
        objective: {
            type: Object,
            default: null,
        },
        objective_type_id: {
            type: Number,
            default: null,
        },
        type: {
            type: String,
            default: null,
        },
        settings: {
            type: Object,
            default: null,
        },
        textcolor: {
            type: String,
            default: '#000',
        },
        max_id: {
            type: Number,
            default: null,
        },
    },
    methods: {
        /**
         * increase or decrease the order-id of this and the adjacent objective
         * @param {Boolean} higher true to increase and false to decrease order-id
         */
        changeOrder(higher) {
            let url = '/' + this.type + 'Objectives/' + this.objective.id;
            url += higher ? '/higher' : '/lower';

            axios.patch(url)
                .then(response => {
                    this.$eventHub.emit(this.type + '-objectives-reordered', {
                        type_id: this.objective_type_id,
                        objectives: response.data,
                        higher: higher ? 1 : -1, // only used for terminal-objectives
                    });
                })
                .catch(e => {
                    this.toast.error(this.errorMessage(e));
                    console.log(e);
                });
        },
    },
    computed: {
        edit_settings: function() {
            return this.checkPermission('curriculum_edit') && (this.settings?.edit ?? false);
        },
    },
    components: { DropdownButton },
}
</script>