<template >
    <div
        class="d-flex my-1"
        :style="{ 'color': textcolor }"
    >
        <AchievementIndicator
            v-permission="'achievement_create'"
            :objective="objective"
            :type="type"
            :settings="settings"
        />
    </div>
</template>
<script>
import AchievementIndicator from './AchievementIndicator.vue';

export default {
    props: {
        objective: {
            type: Object,
            default: null,
        },
        textcolor: {
            type: String,
            default: '#000',
        },
        type: {
            type: String,
            default: null,
        },
        settings: {
            type: Object,
            default: null,
        },
    },
    components: {
        AchievementIndicator,
    },
}
</script>