<template>
    <div class="col-12 px-0">

        <ul class="nav nav-tabs"
            role="tablist">
            <li v-permission="'medium_access'"
                class="btn btn-sm btn-outline-secondary m-2"
                v-bind:class="[(currentTab === 1) ? 'active' : '']"
                id="objective-media-internal-nav"
                data-toggle="pill"
                href="#objective-media-internal"
                role="tab"
                aria-controls="objective-media-internal"
                aria-selected="true"
                @click="setCurrentTab(1);loaderEvent();">
                <i class="fa fa-hdd"></i> lokal
            </li>
            <li v-permission="'external_medium_access'"
                class="btn btn-sm btn-outline-secondary m-2"
                v-bind:class="[(currentTab === 2) ? 'active' : '']"
                id="objective-media-external-nav"
                data-toggle="pill"
                href="#objective-media-external"
                role="tab"
                aria-controls="objective-media-external"
                aria-selected="true"
                @click="setCurrentTab(2)">
                <i class="fa fa-cloud"></i> Mediathek
            </li>
            <li v-permission="'artefact_access'"
                class="btn btn-sm btn-outline-secondary m-2 "
                v-bind:class="[(currentTab === 3) ? 'active' : '']"
                id="objective-media-artefacts-nav"
                data-toggle="pill"
                href="#objective-media-artefacts"
                role="tab"
                aria-controls="objective-media-artefacts"
                aria-selected="true"
                @click="setCurrentTab(3);loaderEvent()">
                <i class="fa fa-graduation-cap"></i> {{ trans('global.artefact.title') }}
            </li>
        </ul>

        <div class="tab-content"
             id="custom-content-below-tabContent">

            <div v-permission="'medium_access'"
                 class="tab-pane fade show "
                 v-bind:class="[(currentTab === 1) ? 'active' : '']"
                 id="objective-media-internal"
                 role="tabpanel"
                 aria-labelledby="curriculum-nav-tab">
                <media
                    ref="objectiveMedia"
                    :subscribable_type="model"
                    :subscribable_id="objective.id"
                    format="list">
                </media>
            </div>
            <div v-permission="'external_medium_access'"
                 class="tab-pane fade show "
                 v-bind:class="[(currentTab === 2) ? 'active' : '']"
                 id="objective-media-external"
                 role="tabpanel"
                 aria-labelledby="curriculum-nav-tab">
                <div class="row">
                    <repository
                        v-if="repository"
                        :repository="repository"
                        ref="repositoryPlugin"
                        :model="objective">
                    </repository>
                </div>
            </div>
            <div v-permission="'artefact_access'"
                 class="tab-pane fade show "
                 v-bind:class="[(currentTab === 3) ? 'active' : '']"
                 id="objective-media-artefacts"
                 role="tabpanel"
                 aria-labelledby="curriculum-nav-tab">
                <media
                    ref="artefactsMedia"
                    :url="'/artefacts'"
                    :subscribable_type="model"
                    :subscribable_id="objective.id"
                    format="list">
                </media>
            </div>
        </div>
        <Teleport to="body">
            <MediumModal
                :show="this.mediumStore.getShowMediumModal"
                @close="this.mediumStore.setShowMediumModal(false)"
            ></MediumModal>
        </Teleport>

    </div>
</template>
<script>

import Media from '../media/Media.vue'
import Repository from '../../../../app/Plugins/Repositories/resources/js/components/Media.vue'
import MediumModal from "../media/MediumModal.vue";
import {useMediumStore} from "../../store/media.js";

    export default {
        name: 'objectiveMedia',
        components: {
            MediumModal,
            Media,
            Repository
        },
        props: {
            objective: {},
            repository: {},
            type: {},
            model: {}
        },
        setup () { //use database store
            const mediumStore = useMediumStore();
            return {
                mediumStore
            }
        },
        data() {
            return {
                currentTab: 2,
            }
        },
        methods:{
            setCurrentTab(id){
                this.currentTab = id;
            },
            loaderEvent() {
                this.$refs.objectiveMedia.loader();
                this.$refs.artefactsMedia.loader();
            }
        },
        mounted() {
            this.$refs.repositoryPlugin.loader();
        }
    }
</script>
