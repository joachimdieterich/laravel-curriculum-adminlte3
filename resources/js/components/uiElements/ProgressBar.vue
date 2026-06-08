<template>
    <div class="progress-bar-wrapper position-relative text-center bg-light rounded-pill">
        <span>{{ achievements.length }}/{{ maxEntries }}</span>
        <div class="progress-bar-container position-absolute d-flex h-100 w-100">
            <div v-if="progress[3]"
                class="bg-red position-relative"
                :style="{ width: (progress[3] / maxEntries * 100) + '%' }"
            ></div>
            <div v-if="progress[2]"
                class="bg-yellow position-relative"
                :style="{ width: (progress[2] / maxEntries * 100) + '%' }"
            ></div>
            <div v-if="progress[1]"
                class="bg-green position-relative"
                :style="{ width: (progress[1] / maxEntries * 100) + '%' }"
            ></div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'ProgressBar',
    props: {
        achievements: {
            type: Array,
            required: true,
        },
        maxEntries: {
            type: Number,
            required: true,
        },
    },
    data() {
        return {
            progress: {
                type: Object,
                default: {},
            },
        }
    },
    mounted() {
        this.calculateProgress();
    },
    methods: {
        calculateProgress() {
            this.progress = {
                1: 0, // green
                2: 0, // yellow
                3: 0, // red
            };
            this.achievements.forEach(achievement => {
                // prioritise teacher feedback (index 0) over self-assessment (index 1)
                const status = achievement.status[0] !== '0'
                    ? achievement.status[0]
                    : achievement.status[1];
                this.progress[status]++;
            });
        },
    },
}
</script>
<style scoped>
.progress-bar-wrapper {
    background-color: #e5e5e5 !important;

    & > span {
        position: relative;
        z-index: 1;
    }
}
.progress-bar-container {
    padding: 0px 0.75rem;
    top: 0;

    & > .bg-red { box-shadow: 0px 0px 5px #dc354580; }
    & > .bg-yellow { box-shadow: 0px 0px 5px #ffc10780; }
    & > .bg-green { box-shadow: 0px 0px 5px #28a74580; }
    & > :first-child::before {
        content: '';
        position: absolute;
        background-color: inherit;
        right: 100%;
        width: 0.75rem;
        height: 100%;
        border-radius: 0.75rem 0 0 0.75rem;
        box-shadow: inherit;
    }
    & > :last-child::after {
        content: '';
        position: absolute;
        background-color: inherit;
        left: 100%;
        width: 0.75rem;
        height: 100%;
        border-radius: 0 0.75rem 0.75rem 0;
        box-shadow: inherit;
    }
}
</style>