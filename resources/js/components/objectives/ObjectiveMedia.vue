<template>
    <div class="col-12 px-0">
        <ul
            class="nav nav-tabs"
            role="tablist"
        >
            <li
                v-permission="'medium_access'"
                id="objective-media-internal-nav"
                class="btn btn-sm btn-outline-secondary m-2"
                :class="[(currentTab === 1) ? 'active' : '']"
                data-toggle="pill"
                href="#objective-media-internal"
                role="tab"
                aria-controls="objective-media-internal"
                aria-selected="true"
                @click="setCurrentTab(1);loaderEvent();"
            >
                <i class="fa fa-hdd"></i>
                lokal
            </li>
            <li
                v-permission="'external_medium_access'"
                id="objective-media-external-nav"
                class="btn btn-sm btn-outline-secondary m-2"
                :class="[(currentTab === 2) ? 'active' : '']"
                data-toggle="pill"
                href="#objective-media-external"
                role="tab"
                aria-controls="objective-media-external"
                aria-selected="true"
                @click="setCurrentTab(2)"
            >
                <i class="fa fa-cloud"></i>
                Mediathek
            </li>
            <li
                v-permission="'artefact_access'"
                id="objective-media-artefacts-nav"
                class="btn btn-sm btn-outline-secondary m-2"
                :class="[(currentTab === 3) ? 'active' : '']"
                data-toggle="pill"
                href="#objective-media-artefacts"
                role="tab"
                aria-controls="objective-media-artefacts"
                aria-selected="true"
                @click="setCurrentTab(3);loaderEvent()"
            >
                <i class="fa fa-graduation-cap"></i>
                {{ trans('global.artefact.title') }}
            </li>
        </ul>

        <div
            id="custom-content-below-tabContent"
            class="tab-content"
        >
            <div
                v-permission="'medium_access'"
                id="objective-media-internal"
                class="tab-pane fade show"
                :class="[(currentTab === 1) ? 'active' : '']"
                role="tabpanel"
                aria-labelledby="curriculum-nav-tab"
            >
                <media
                    ref="objectiveMedia"
                    :subscribable_type="model"
                    :subscribable_id="objective.id"
                    format="list"
                />
            </div>
            <div
                v-permission="'external_medium_access'"
                id="objective-media-external"
                class="tab-pane fade show"
                :class="[(currentTab === 2) ? 'active' : '']"
                role="tabpanel"
                aria-labelledby="curriculum-nav-tab"
            >
                <div class="row">
                    <repository v-if="repository"
                        :repository="repository"
                        ref="repositoryPlugin"
                        :model="objective"
                    />
                </div>
            </div>
            <div
                v-permission="'artefact_access'"
                id="objective-media-artefacts"
                class="tab-pane fade show"
                :class="[(currentTab === 3) ? 'active' : '']"
                role="tabpanel"
                aria-labelledby="curriculum-nav-tab"
            >
                <media
                    ref="artefactsMedia"
                    :url="'/artefacts'"
                    :subscribable_type="model"
                    :subscribable_id="objective.id"
                    format="list"
                />
            </div>
        </div>
    </div>
</template>
<script>
import Media from '../media/Media.vue'
import Repository from '../../../../app/Plugins/Repositories/resources/js/components/Media.vue'

export default {
    name: 'objectiveMedia',
    components: {
        Media,
        Repository,
    },
    props: {
        objective: {},
        repository: {},
        type: {},
        model: {},
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