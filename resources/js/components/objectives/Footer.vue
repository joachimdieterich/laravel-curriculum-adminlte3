<template >
    <div class="boxfooter row" v-bind:style="{ 'color': textcolor }">
        <div class="p-0 col-12 ">
             <AchievementIndicator
                 v-can="'achievement_create'"
                 v-if="(typeof this.objective.achievements !== 'undefined')"
                :objective="objective"
                :type="type"
                :settings="settings">
             </AchievementIndicator>
        </div>
    </div>
</template>

<script>
import AchievementIndicator from './AchievementIndicator.vue';
    export default {
        props: {
                objective: {},
                textcolor: { default: '#000' },
                type:{},
                settings:{}
            },
        methods: {},
        components: {
            AchievementIndicator,
        },
    }
</script>
