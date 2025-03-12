<template>
    <div id="custom-content-below-tabContent">
        <div
            v-permission="'external_medium_access'"
            id="objective-media-external"
        >
            <Repository v-if="repository"
                :repository="repository"
                ref="repositoryPlugin"
                :model="objective"
            />
        </div>
    </div>
</template>
<script>
import Repository from '../../../../app/Plugins/Repositories/resources/js/components/Media.vue'

export default {
    name: 'ObjectiveMedia',
    components: {
        Repository,
    },
    props: {
        objective: {
            type: Object,
            default: null,
        },
        repository: {
            type: Object,
            default: null,
        },
    },
    mounted() {
        this.$refs.repositoryPlugin.loader();
    },
}
</script>