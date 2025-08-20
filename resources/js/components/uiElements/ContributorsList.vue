<template>
    <div class="contributor-container d-inline-flex align-items-center" :id="component_id">
        <div @mouseenter="contributorDetailsEntered(contributor.id)"
             @mouseleave="contributorDetailsLeft()"
             @mousemove="contributorDetailsMovement"
             class="d-flex contributor-icon rounded-circle align-items-center px-1"
             v-for="contributor in contributors"
             :style="this.contributorStyles[contributor.id]"
        >
            <span class="contributor-initials">{{ contributor.initials }}</span>
            <div v-show="contributorDetails.show && contributorDetails.key === contributor.id"
                 class="rounded-sm contributor-details"
                 :style="{top: contributorDetailsTopStyle + 'px', left: contributorDetailsLeftStyle + 'px'}"
            >
                {{ contributor.firstname }} {{ contributor.lastname }}
            </div>
        </div>
    </div>
</template>
<style scoped>
    .contributor-container {
        gap: 4px;
    }

    .contributor-details {
        cursor: default;
        position: absolute;
        font-weight: normal;
        padding: 0.5rem;
        z-index: 9999;
        color: black !important;
        border-radius: 0.3rem;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        background:white;
    }

    .contributor-icon {
        cursor: default;
        font-weight: bold;
        text-align:center;
        height: 35px;
        width: 35px;
        vertical-align: middle;
        border: black solid 2px;
    }

    .contributor-initials {
        margin: auto;
    }
</style>
<script>

export default {
    props: {
        contributors: {
            type: Array
        },
        bgColors: {
            type: Array,
            default: [
                '#3c8dbc',
                '#3d9970',
                '#CBD900',
                '#f012be',
                '#d81b60',
                '#007bff',
                '#6610f2',
                '#6f42c1',
                '#e83e8c',
                '#dc3545',
                '#fd7e14',
                '#ffc107',
                '#28a745',
                '#20c997',
                '#17a2b8',
                '#6c757d',
                '#343a40',
            ]
        }
    },
    watch: {
        contributors:{
            handler(newValue) {
                for (let item of newValue) {
                    if (this.contributorStyles[item.id] !== undefined) {
                        return;
                    }

                    this.contributorStyles[item.id] = this.getStyleForContributor();
                }
            }
        }
    },
    methods: {
        getStyleForContributor: function() {
            let randomBgColor = this.bgColors[Math.floor(Math.random() * this.bgColors.length)];
            let fontColor = this.$style.fontColorByBackgroundBgColor(randomBgColor);

            return "color:" + fontColor + "; background-color:" + randomBgColor + ";";
        },
        contributorDetailsEntered: function (contributorKey) {
            this.contributorDetails.key = contributorKey;
            this.contributorDetails.show = true;
        },
        contributorDetailsLeft: function () {
            this.contributorDetails.key = 0;
            this.contributorDetails.show = false;
        },
        contributorDetailsMovement: function (e) {
            this.contributorDetails.posX = e.x + 20;
            this.contributorDetails.posY = e.y - 80;
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            contributorDetails: {
                key: 0,
                show: false,
                posX: 0,
                posY: 0
            },
            contributorStyles: {}
        }
    },
    computed: {
        contributorDetailsLeftStyle() {
            return this.contributorDetails.posX
        },
        contributorDetailsTopStyle() {
            return this.contributorDetails.posY
        },
    },
}
</script>
