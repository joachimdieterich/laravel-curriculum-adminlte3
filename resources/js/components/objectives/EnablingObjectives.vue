<template>
    <div>
        <div v-for="objective in objectives"
            :id="'enablingObjective_' + objective.id"
            class="box-objective"
        >
            <ObjectiveBox
                type="enabling"
                :objective="objective"
                :objective_type_id="terminalobjective.objective_type_id"
                :referenceable_id="referenceable_id"
                :referenceable_type="referenceable_type"
                :color="terminalobjective.color"
                :settings="settings"
                :editable="editable"
                :max_id="objectives[objectives.length - 1]?.id"
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
                :max_id="objectives[objectives.length - 1]?.id"
            />
        </div>
    </div>
</template>
<script>
import ObjectiveBox from './ObjectiveBox.vue';

export default {
    props: {
        objectives: {
            type: Array,
            default: null,
        },
        terminalobjective: {
            type: Object,
            default: null,
        },
        referenceable_id: {
            type: Number,
            default: null,
        },
        referenceable_type: {
            type: String,
            default: null,
        },
        settings: {
            type: Object,
            default: null,
        },
        editable: {
            type: Boolean,
            default: false,
        },
    },
    components: {
        ObjectiveBox,
    },
}
</script>