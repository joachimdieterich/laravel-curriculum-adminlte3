<template>
    <div :id="component_id" v-if="show">
        <h5 v-if="heading">{{ trans('global.kanban.contributor') }}</h5>
        <div class="contributor-container d-inline-flex align-items-center">
            <div @mouseenter="contributorDetailsEntered(contributor.id)"
                 @mouseleave="contributorDetailsLeft()"
                 @mousemove="contributorDetailsMovement"
                 class="d-flex  rounded-circle align-items-center"
                 v-for="contributor of contributors"
                 :style="this.contributorStyles[contributor.id]"
            >
                <avatar data-toggle="tooltip"
                        :title="contributor.firstname + ' ' + contributor.lastname"
                        :username="contributor.firstname + ' ' + contributor.lastname"
                        :firstname="contributor.firstname"
                        :lastname="contributor.lastname"
                        :size="35"
                />
                <div v-show="contributorDetails.show && contributorDetails.key === contributor.id"
                     class="rounded-sm contributor-details"
                     :style="{top: contributorDetailsTopStyle + 'px', left: contributorDetailsLeftStyle + 'px'}"
                >
                    {{ contributor.firstname }} {{ contributor.lastname }}
                </div>
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
</style>
<script>

import Avatar from "./Avatar.vue";

export default {
    components: {Avatar},
    props: {
        contributors: {
            type: Object
        },
        heading: {
            type: Boolean,
            default: false
        }
    },
    methods: {
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
        show() {
            return Object.values(this.contributors).length > 1;
        }
    },
}
</script>
