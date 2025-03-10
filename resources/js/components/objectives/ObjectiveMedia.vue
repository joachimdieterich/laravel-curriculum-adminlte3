<template>
    <div class="col-12 px-0">
        <div
            id="custom-content-below-tabContent"
            class="tab-content"
        >
            <div
                v-permission="'external_medium_access'"
                id="objective-media-external"
                class="tab-pane fade show"
                :class="[(currentTab === 2) ? 'active' : '']"
                role="tabpanel"
                aria-labelledby="curriculum-nav-tab"
            >
                <div class="row">
                    <Repository v-if="repository"
                        :repository="repository"
                        ref="repositoryPlugin"
                        :model="objective"
                    />
                </div>
            </div>
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
    data() {
        return {
            currentTab: 2,
        }
    },
    methods:{
        setCurrentTab(id) {
            this.currentTab = id;
        },
        loaderEvent() {
            this.$refs.objectiveMedia.loader();
            this.$refs.artefactsMedia.loader();
        },
    },
    mounted() {
        this.$refs.repositoryPlugin.loader();
    },
}
</script>