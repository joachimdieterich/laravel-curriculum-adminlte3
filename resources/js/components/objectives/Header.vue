<template>
    <div
        class="boxheader row"
        :style="{ 'color': textcolor }"
    >
        <span v-if="edit_settings"
            v-permission="'curriculum_edit'"
            class="mr-auto"
        >
            <span v-if="(type == 'terminal' && objective.order_id != 0)"
                class="fa fa-arrow-up mr-1"
                @click="$emit('eventSort','-1')"
            ></span>
            <span v-if="(type == 'terminal' && max_id != objective.id)"
                class="fa fa-arrow-down"
                @click="$emit('eventSort','1')"
            ></span>

            <span v-if="(type == 'enabling'  && objective.order_id != 0)"
                class="fa fa-arrow-left mr-1"
                @click="$emit('eventSort','-1')"
            ></span>
            <span v-if="(type == 'enabling' && max_id != objective.id)"
                class="fa fa-arrow-right"
                @click="$emit('eventSort','1')"
            ></span>
        </span>

        <span v-if="(type == 'enabling' && objective.level != null)">
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
export default {
    props: {
        objective: {
            type: Object,
        },
        type: {
            type: String,
        },
        menuEntries: {
            type: Array,
        },
        settings: {
            type: Object,
        },
        textcolor: {
            type: String,
            default: '#000'
        },
        max_id: {
            type: Number,
        },
    },
    methods: {},
    computed: {
        edit_settings: function() {
            if (typeof this.settings !== "undefined"){
                return this.settings.edit;
            } else {
                return false;
            }
        }
    },
    mounted() {},
    components: {
        DropdownButton,
    },
}
</script>