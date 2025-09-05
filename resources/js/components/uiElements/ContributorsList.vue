<template>
    <div class="bg-light contributor-hover-box" :id="component_id" v-if="show">
        <div class="contributor-container d-inline-flex align-items-center">
            <avatar data-toggle="tooltip"
                    :title="contributor.firstname + ' ' + contributor.lastname"
                    :username="contributor.firstname + ' ' + contributor.lastname"
                    :firstname="contributor.firstname"
                    :lastname="contributor.lastname"
                    class="contacts-list-img"
                    :size="35"
                    v-for="contributor of contributors"
            />
        </div>
    </div>
</template>
<style scoped>
    .contributor-container {
        gap: 4px;
    }

    .contributor-hover-box {
        position: fixed;
        bottom: 3em !important;
        right: 3em  !important;
        z-index: 1020;

        padding: 0.25rem 0 0 0.25rem;
        border-radius: 0.5rem;

        opacity: 50%;
    }

    .contributor-hover-box:hover {
        transition: all 0.5s ease;
        opacity: 100%;
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
        show() {
            return Object.values(this.contributors).length > 1;
        }
    },
}
</script>
