<template>
    <div
        class="d-flex align-items-center mt-1"
        style="min-height: 20px;"
        :style="{ 'color': textcolor }"
    >
        <span v-if="edit_settings"
            v-permission="'curriculum_edit'"
            class="mr-auto"
        >
            <a v-if="(type == 'terminal' && objective.order_id != 0)"
                class="pointer mr-2"
                style="color: inherit;"
                role="button"
                @click="changeOrder(false)"
            >
                <i class="fa fa-arrow-up"></i>
            </a>
            <a v-if="(type == 'terminal' && max_id != objective.id)"
                class="link-muted pointer"
                style="color: inherit;"
                role="button"
                @click="changeOrder(true)"
            >
                <i class="fa fa-arrow-down"></i>
            </a>

            <a v-if="(type == 'enabling' && objective.order_id != 0)"
                class="btn btn-icon btn-sm px-1 py-0 text-secondary mr-2"
                role="button"
                @click="changeOrder(false)"
            >
                <i class="fa fa-arrow-left"></i>
            </a>
            <a v-if="(type == 'enabling' && max_id != objective.id)"
                class="btn btn-icon btn-sm px-1 py-0 text-secondary"
                role="button"
                @click="changeOrder(true)"
            >
                <i class="fa fa-arrow-right"></i>
            </a>
        </span>

        <span v-if="(type == 'enabling' && objective.level != null)"
            class="mx-auto"
        >
            <button
                type="button"
                class="btn btn-block btn-xs"
                :class="objective.level.css_color"
            >
                {{ objective.level.title }}
            </button>
        </span>

        <span v-if="edit_settings"
            v-permission="'curriculum_edit'"
            class="ml-auto"
        >
            <DropdownButton v-if="type == 'terminal'"
                :menuEntries="menuEntries"
                :objective.sync="objective"
                :textcolor="textcolor"
            />
            <DropdownButton v-else-if="type == 'enabling'"
                :menuEntries="menuEntries"
                :objective.sync="objective"
                :textcolor="textcolor"
            />
        </span>
    </div>
</template>
<script>
import DropdownButton from './DropdownButton.vue';
import {useToast} from "vue-toastification";

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
        menuEntries: {
            type: Array,
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
    setup() {
        const toast = useToast();
        return {
            toast,
        }
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
                .catch(error => {
                    this.toast.error(this.trans('global.error'));
                    console.log(error);
                });
        },
    },
    computed: {
        edit_settings: function() {
            return this.settings?.edit ?? false;
        },
    },
    components: {
        DropdownButton,
    },
}
</script>