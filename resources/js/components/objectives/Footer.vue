<template >
    <div
        class="boxfooter row"
        :style="{ 'color': textcolor }"
    >
        <div class="p-0 col-12">
            <AchievementIndicator v-if="(typeof this.objective.achievements !== 'undefined')"
                v-can="'achievement_create'"
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
        objective: {},
        textcolor: '#000',
        type: {},
        settings: {},
    },
    methods: {},
    components: {
        AchievementIndicator,
    },
}
</script>