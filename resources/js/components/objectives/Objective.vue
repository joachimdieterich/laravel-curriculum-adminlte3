<template>
    <div>
        <div class="d-flex flex-column px-3 pb-3">
            <div class="p-3 border-bottom bg-white">
                <span v-html="currentObjective.title" class="p-margin-0"></span>
            </div>

            <div class="d-flex flex-column mt-2">
                <ul
                    class="nav nav-tabs align-items-center"
                    role="tablist"
                    :aria-label="trans('global.' + type + 'Objective.title_singular') + ' Tabs'"
                >
                    <!-- 1 Description -->
                    <li
                        class="nav-item"
                        role="presentation"
                    >
                        <button
                            id="description-nav-tab"
                            class="nav-link link-muted"
                            type="button"
                            role="tab"
                            data-bs-toggle="tab"
                            data-bs-target="#description-tab"
                            aria-controls="description-tab"
                            aria-selected="false"
                        >
                            <i class="fa fa-info"></i>
                            <span v-if="help" class="ps-2">{{ trans('global.description') }}</span>
                        </button>
                    </li>
                    <!-- 2 Objectives -->
                    <li
                        class="nav-item"
                        role="presentation"
                    >
                        <button
                            id="objectives-nav-tab"
                            class="nav-link link-muted"
                            type="button"
                            role="tab"
                            data-bs-toggle="tab"
                            data-bs-target="#objectives-tab"
                            aria-controls="objectives-tab"
                            aria-selected="false"
                        >
                            <i class="fa fa-sitemap"></i>
                            <span v-if="type === 'terminal'">
                                <span v-if="help" class="ps-2">{{ trans('global.subordinate_element') }}</span>
                            </span>
                            <span v-else>
                                <span v-if="help" class="ps-2">{{ trans('global.superordinate_element_singular') }}</span>
                            </span>
                        </button>
                    </li>
                    <!-- 3 Contents -->
                    <li
                        class="nav-item"
                        role="presentation"
                    >
                        <button
                            id="content-nav-tab"
                            class="nav-link link-muted"
                            type="button"
                            role="tab"
                            data-bs-toggle="tab"
                            data-bs-target="#content-tab"
                            aria-controls="content-tab"
                            aria-selected="false"
                            @click="loaderContents()"
                        >
                            <i class="fa fa-align-justify"></i>
                            <span v-if="help" class="ps-2">{{ trans('global.content.index_alt') }}</span>
                        </button>
                    </li>
                    <!-- 4 Media -->
                    <li
                        class="nav-item"
                        role="presentation"
                    >
                        <button
                            id="media-nav-tab"
                            class="nav-link link-muted active"
                            type="button"
                            role="tab"
                            data-bs-toggle="tab"
                            data-bs-target="#media-tab"
                            aria-controls="media-tab"
                            aria-selected="true"
                        >
                            <i class="fa fa-folder-open"></i>
                            <span v-if="help" class="ps-2">{{ trans('global.medium.title') }}</span>
                        </button>
                    </li>
                    <!-- 5 References -->
                    <li
                        class="nav-item"
                        role="presentation"
                    >
                        <button
                            id="references-nav-tab"
                            class="nav-link link-muted"
                            type="button"
                            role="tab"
                            data-bs-toggle="tab"
                            data-bs-target="#references-tab"
                            aria-controls="references-tab"
                            aria-selected="false"
                            @click="loadReferences()"
                        >
                            <i class="fa fa-project-diagram"></i>
                            <span v-if="help" class="ps-2">{{ trans('global.referenceable_types.objective') }}</span>
                        </button>
                    </li>
                    <!-- 6 Achievements -->
                    <li v-if="type === 'enabling'"
                        v-permission="'achievement_access'"
                        class="nav-item"
                        role="presentation"
                    >
                        <button
                            id="achievements-nav-tab"
                            class="nav-link link-muted"
                            type="button"
                            role="tab"
                            data-bs-toggle="tab"
                            data-bs-target="#achievements-tab"
                            aria-controls="achievements-tab"
                            aria-selected="false"
                            @click="loadAchievements()"
                        >
                            <i class="far fa-check-circle"></i>
                            <span v-if="help" class="ps-2">{{ trans('global.objective_tab') }}</span>
                        </button>
                    </li>
                    <!-- 7 Prerequisites -->
                    <li
                        v-permission="'prerequisite_access'"
                        class="nav-item"
                        role="presentation"
                    >
                        <button
                            id="prerequisites-nav-tab"
                            class="nav-link link-muted"
                            type="button"
                            role="tab"
                            data-bs-toggle="tab"
                            data-bs-target="#prerequisites-tab"
                            aria-controls="prerequisites-tab"
                            aria-selected="false"
                            @click="loadPrerequisites()"
                        >
                            <i class="fa fa-puzzle-piece"></i>
                            <span v-if="help" class="ps-2">{{ trans('global.prerequisite.title') }}</span>
                        </button>
                    </li>
                    <!-- 8 Eventmanagement -->
                    <li
                        class="nav-item"
                        role="presentation"
                    >
                        <button
                            id="events-nav-tab"
                            class="nav-link link-muted"
                            type="button"
                            role="tab"
                            data-bs-toggle="tab"
                            data-bs-target="#events-tab"
                            aria-controls="events-tab"
                            aria-selected="false"
                        >
                            <i class="fa fa-user-graduate"></i>
                            <span v-if="help" class="ps-2">{{ trans('global.eventSubscription.title_alt') }}</span>
                        </button>
                    </li>
                    <!-- 9 LMS -->
                    <li
                        v-permission="'lms_access'"
                        class="nav-item"
                        role="presentation"
                    >
                        <button
                            id="lms-nav-tab"
                            class="nav-link link-muted"
                            type="button"
                            role="tab"
                            data-bs-toggle="tab"
                            data-bs-target="#lms-tab"
                            aria-controls="lms-tab"
                            aria-selected="false"
                            @click="loadLmsPlugin()"
                        >
                            <i class="fa fa-graduation-cap"></i>
                            <span v-if="help" class="ps-2">{{ trans('global.lms.title_singular') }}</span>
                        </button>
                    </li>
                    <!-- Help-icon -->
                    <button
                        type="button"
                        class="d-print-none btn btn-icon text-secondary ms-auto"
                        data-bs-toggle="tooltip"
                        :data-bs-title="trans('global.toggle_navigation')"
                        @click="help = !help"
                    >
                        <i class="fa fa-question"></i>
                    </button>
                </ul>

                <div class="tab-content bg-white">
                    <!-- 1 Description -->
                    <div
                        id="description-tab"
                        class="tab-pane fade p-3 border border-top-0 rounded-bottom-2"
                        role="tabpanel"
                        aria-labelledby="description-nav-tab"
                    >
                        <Variants v-if="objective.curriculum.variants?.length > 0"
                            :model="currentObjective"
                            :referenceable_type="model"
                            :referenceable_id="objective.id"
                            :variant_order="variant_order"
                        />
                        <div v-else style="margin-top: -10px;">
                            <div class="d-flex flex-column flex-sm-row justify-content-between pb-1">
                                <small>{{ trans('global.enablingObjective.fields.time_approach') }}: {{ currentObjective.time_approach ?? '-/-' }}</small>
                                <small>{{ trans('global.updated_at') }}: {{ currentObjective.updated_at }}</small>
                            </div>
                            <span
                                class="p-margin-0"
                                v-html="currentObjective.description?.length > 0 ? currentObjective.description : trans('global.no_description')"
                            ></span>
                        </div>
                    </div>
                    <!-- 2 Objectives -->
                    <div
                        id="objectives-tab"
                        class="tab-pane fade p-2 border border-top-0 rounded-bottom-2"
                        role="tabpanel"
                        aria-labelledby="objectives-nav-tab"
                    >
                        <div class="objectives">
                            <ObjectiveBox v-if="type === 'enabling'"
                                type="terminal"
                                :objective="objective.terminal_objective"
                            />
                            <ObjectiveBox v-for="enablingObjective in enablingObjectives"
                                type="enabling"
                                :objective="enablingObjective"
                                :color="objective.color ?? objective.terminal_objective.color"
                            />
                        </div>
                    </div>
                    <!-- 3 Contents-->
                    <div
                        id="content-tab"
                        class="tab-pane fade"
                        role="tabpanel"
                        aria-labelledby="content-nav-tab"
                    >
                        <span>
                            <Contents v-if="type === 'enabling'"
                                ref="Contents"
                                subscribable_type="App\EnablingObjective"
                                :subscribable_id="objective.id"
                            />
                            <Contents v-else-if="type === 'terminal'"
                                ref="Contents"
                                subscribable_type="App\TerminalObjective"
                                :subscribable_id="objective.id"
                            />
                        </span>
                    </div>
                    <!-- 4 Media -->
                    <div
                        id="media-tab"
                        class="tab-pane fade show active border border-top-0 rounded-bottom-2"
                        role="tabpanel"
                        aria-labelledby="media-nav-tab"
                    >
                        <Media :model="objective"/>
                    </div>
                    <!-- 5 References-->
                    <div
                        id="references-tab"
                        class="tab-pane fade p-2 border border-top-0 rounded-bottom-2"
                        role="tabpanel"
                        aria-labelledby="references-nav-tab"
                    >
                        <div
                            v-permission="'objective_edit'"
                            class="card-tools"
                        >
                            <button
                                class="dropdown-item"
                                @click="openReferencesModal()"
                            >
                                <i class="fa fa-plus pull-right"></i>
                            </button>
                        </div>

                        <References
                            ref="References"
                            :objective="objective"
                            :type="type"
                        />
                        <Quotes
                            ref="Quotes"
                            :objective="objective"
                            :type="type"
                        />
                    </div>
                    <!-- 6 Achievements-->
                    <div v-if="type === 'enabling'"
                        v-permission="'achievement_access'"
                        id="achievements-tab"
                        class="tab-pane fade p-2 border border-top-0 rounded-bottom-2"
                        role="tabpanel"
                        aria-labelledby="achievements-nav-tab"
                    >
                        <Achievements
                            ref="Achievements"
                            :objective="objective"
                            :type="type"
                        />
                    </div>
                    <!-- 7 Prerequisites -->
                    <div
                        v-permission="'prerequisite_create, objective_edit'"
                        id="prerequisites-tab"
                        class="tab-pane fade p-3 border border-top-0 rounded-bottom-2"
                        role="tabpanel"
                        aria-labelledby="prerequisites-nav-tab"
                    >
                        <div class="card-tools">
                            <button
                                class="dropdown-item"
                                @click="openPrerequisitesModal"
                            >
                                <i class="fa fa-plus pull-right"></i>
                            </button>
                        </div>
                        <Prerequisites
                            ref="Prerequisites"
                            :successor_type="model"
                            :successor_id="objective.id"
                        />
                    </div>
                    <!-- 8 Eventmanagement -->
                    <div
                        id="events-tab"
                        class="tab-pane fade p-3 border border-top-0 rounded-bottom-2"
                        role="tabpanel"
                        aria-labelledby="events-nav-tab"
                    >
                        <Eventmanagement
                            ref="eventPlugin"
                            :model="objective"
                            :curriculum="objective.curriculum"
                        />
                    </div>
                    <!-- 9 LMS -->
                    <div
                        v-permission="'lms_access'"
                        id="lms-tab"
                        class="tab-pane fade p-2 border border-top-0 rounded-bottom-2"
                        role="tabpanel"
                        aria-labelledby="lms-nav-tab"
                    >
                        <Lms
                            ref="LmsPlugin"
                            :editable="editable"
                            :referenceable_type="model"
                            :referenceable_id="objective.id"
                        />
                    </div>
                </div>
            </div>
        </div>            

        <Teleport to="body">
            <ContentModal/>
            <ReferenceObjectiveModal/>
            <PrerequisiteObjectiveModal
                :params="{
                    successor_type: model,
                    successor_id: objective.id,
                    url: '/prerequisites',
                }"
            />
            <LmsModal
                :params="{
                    referenceable_type: model,
                    referenceable_id: objective.id,
                    url: '/lmsReferences'
                }"
            />
            <SubscribeModal/>
            <MediumModal/>
            <TerminalObjectiveModal/>
            <EnablingObjectiveModal/>
        </Teleport>
        <Teleport to="#customTitle">
            <div class="d-flex">
                <small>{{ trans('global.details') }}</small>
                <button v-if="editable"
                    type="button"
                    class="d-print-none btn btn-icon text-secondary mx-1"
                    data-bs-toggle="tooltip"
                    :data-bs-title="trans('global.' + type + 'Objective.edit')"
                    @click="editObjective()"
                >
                    <i class="fa fa-pencil-alt"></i>
                </button>
            </div>
        </Teleport>
    </div>
</template>
<script>
import Contents from '../content/Contents.vue';
import ContentModal from "../content/ContentModal.vue";
import TerminalObjectiveModal from "./TerminalObjectiveModal.vue";
import EnablingObjectiveModal from "./EnablingObjectiveModal.vue";
import Achievements from "./Achievements.vue";
import Prerequisites from "../prerequisites/Prerequisites.vue";
import Eventmanagement from "../../../../app/Plugins/Eventmanagement/resources/js/components/Events.vue";
import ObjectiveBox from "./ObjectiveBox.vue";
import Variants from "./Variants.vue";
import Media from '../../../../app/Plugins/Repositories/edusharing/resources/js/components/Media.vue';
import Lms from "../lms/Lms.vue";
import LmsModal from "../lms/LmsModal.vue";
import References from "../reference/References.vue";
import Quotes from "../quote/Quotes.vue";
import MediumModal from "../media/MediumModal.vue";
import ReferenceObjectiveModal from "../reference/ReferenceObjectiveModal.vue";
import PrerequisiteObjectiveModal from "../prerequisites/PrerequisiteObjectiveModal.vue";
import SubscribeModal from "../subscription/SubscribeModal.vue";

export default {
    name: "Objective",
    components: {
        SubscribeModal,
        PrerequisiteObjectiveModal,
        ReferenceObjectiveModal,
        MediumModal,
        Media,
        Quotes,
        References,
        Lms,
        LmsModal,
        Variants,
        ObjectiveBox,
        Eventmanagement,
        Prerequisites,
        Achievements,
        EnablingObjectiveModal,
        TerminalObjectiveModal,
        Contents,
        ContentModal,
    },
    props: {
        objective: {
            type: Object,
            default: null,
        },
        editable: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            componentId: this.$.uid,
            currentObjective: {},
            type: null,
            media_subscriptions: [],
            categories: [],
            help: true,
            variant_order: {},
            variant: {},
            setting: {
                last: null,
            },
            model: '',
            search: '',
        }
    },
    async mounted() {
        this.currentObjective = this.objective;

        if (typeof this.objective.terminal_objective === 'object') {
            this.type = 'enabling';
            this.model = 'App\\EnablingObjective';
        } else {
            this.type = 'terminal';
            this.model = 'App\\TerminalObjective';
        }

        //event listener
        this.$eventHub.on(this.type + '-objective-updated', (updatedObjective) => {
            Object.assign(this.currentObjective, updatedObjective);
        });

        this.$eventHub.on('reference-added', () => {
            this.loadReferences();
        });
        this.$eventHub.on('reference-deleted', () => {
            this.loadReferences();
        });
        this.$eventHub.on('prerequisite-added', () => {
            this.loadPrerequisites();
        });

        this.$eventHub.on('lms-added', () => {
            this.globalStore?.closeModal('lms-modal');
            this.loadLmsPlugin();
        });
        this.$eventHub.on('lms-updated', (lms) => {
            this.globalStore?.closeModal('lms-modal');
            this.loadLmsPlugin()
        });

        this.$nextTick(() => this.enableTooltips());
    },
    methods: {
        openReferencesModal() {
            this.globalStore.showModal('reference-objective-modal', {
                subscribable_type: this.model,
                subscribable_id: this.objective.id,
                url: '/referenceSubscriptions',
            });
        },
        openPrerequisitesModal() {
            this.globalStore.showModal('prerequisite-objective-modal', {
                successor_type: this.model,
                successor_id: this.objective.id,
            });
        },
        editObjective() {
            this.globalStore.showModal(this.type + '-objective-modal', this.objective);
        },
        //Loader
        loaderContents: function() {
            this.$refs.Contents.loaderEvent();
        },
        loadReferences() {
            this.$refs.References.loaderEvent();
            this.$refs.Quotes.loaderEvent();
        },
        loadAchievements() {
            this.$refs.Achievements.loaderEvent();
        },
        loadPrerequisites() {
            this.$refs.Prerequisites.loaderEvent();
        },
        loadLmsPlugin() {
            this.$refs.LmsPlugin.loaderEvent();
        },
    },
    computed: {
        enablingObjectives() {
            return this.type === 'enabling'
                ? this.objective.terminal_objective.enabling_objectives
                : this.objective.enabling_objectives;
        },
    },
}
</script>