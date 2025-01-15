<template>
    <div>
        <div v-for="objective in objectives"
            :id="'enablingObjective_' + objective.id"
            class="box-objective"
        >
            <ObjectiveBox
                type="enabling"
                :objective="objective"
                :referenceable_id="referenceable_id"
                :referenceable_type="referenceable_type"
                :color="terminalobjective.color"
                :settings="settings"
                :editable="editable"
                :max_id="localSettings.last"
            />
        </div>

        <div v-if="settings.edit === true"
            v-permission="'curriculum_edit'"
            id="createEnablingRow"
        >
            <ObjectiveBox
                type="createenabling"
                :objective="{
                    curriculum_id: terminalobjective.curriculum_id,
                    terminal_objective_id: terminalobjective.id
                }"
                :color="terminalobjective.color"
                :settings="settings"
                :max_id="localSettings.last"
            />
        </div>
    </div>
</template>
<script>
import ObjectiveBox from './ObjectiveBox.vue';

export default {
    props: {
        objectives: Array,
        terminalobjective: Object,
        referenceable_id: null,
        referenceable_type: null,
        settings: Object,
        editable: {
            default: false,
        },
    },
    data() {
        return {
            localSettings: {
                last: null,
            },
        }
    },
    mounted() {
        if (this.objectives.length != 0) {
            this.localSettings.last = this.objectives[this.objectives.length-1].id;
        }
    },
    components: {
        ObjectiveBox,
    }
}
</script>
