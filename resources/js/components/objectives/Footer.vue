<template >
    <div
        class="boxfooter row"
        :style="{ 'color': textcolor }"
    >
        <div class="p-0 col-12">
            <AchievementIndicator v-if="(typeof this.objective.achievements !== 'undefined')"
                v-permission="'achievement_create'"
                :objective="objective"
                :type="type"
                :settings="settings"
            />
        </div>
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