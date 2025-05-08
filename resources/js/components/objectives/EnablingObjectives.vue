<template>
    <ObjectiveBox v-for="objective in objectives"
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

    <ObjectiveBox v-if="settings.edit === true"
        v-permission="'curriculum_edit'"
        type="createenabling"
        :objective="{
            curriculum_id: terminalobjective.curriculum_id,
            terminal_objective_id: terminalobjective.id
        }"
        :settings="settings"
        :max_id="objectives[objectives.length - 1]?.id"
    />
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