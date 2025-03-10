<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <span v-html="objective.title" class="p-margin-0"></span>
                </div>
                <div class="card-body">
                    <span>
                        <Variants
                            :model="objective"
                            :referenceable_type="model"
                            :referenceable_id="objective.id"
                            :variant_order="variant_order"
                        />
                    </span>
                </div>
                <div class="card-footer">
                    <div class="float-left">
                        <small>
                            {{ trans('global.enablingObjective.fields.time_approach') }}: {{ objective.time_approach }}
                        </small>
                    </div>
                    <small class="float-right">
                        {{ trans('global.updated_at') }}: {{ objective.updated_at }}
                    </small>
                </div>
            </div>

            <ul
                class="nav nav-tabs"
                role="tablist"
            >
                <!-- 1 Objectives -->
                <li
                    class="nav-item"
                    role="presentation"
                    @click="setGlobalStorage('#objective_view_' + objective.id, '#objective_view_related_objectives_' + objective.id)"
                >
                    <button
                        id="objectives-tab"
                        class="nav-link small link-muted"
                        data-toggle="tab"
                        data-target="#objectives"
                        type="button"
                        role="tab"
                        aria-controls="objectives"
                        aria-selected="false"
                    >
                        <i class="fa fa-sitemap pr-1"></i>
                        <span v-if="type === 'terminal'">
                            <span v-if="help">{{ trans('global.subordinate_element') }}</span>
                        </span>
                        <span v-else>
                            <span v-if="help">{{ trans('global.superordinate_element_singular') }}</span>
                        </span>
                    </button>
                </li>
                <!-- 2 Contents -->
                <li
                    class="nav-item"
                    role="presentation"
                    @click="setGlobalStorage('#objective_view_' + objective.id, '#objective_view_content_tab_' + objective.id)"
                >
                    <button
                        id="content-tab"
                        class="nav-link small link-muted"
                        data-toggle="tab"
                        data-target="#content"
                        type="button"
                        role="tab"
                        aria-controls="content"
                        aria-selected="false"
                        @click="loaderContents()"
                    >
                        <i class="fa fa-align-justify pr-2"></i>
                        <span v-if="help">{{trans('global.content.index_alt')}}</span>
                    </button>
                </li>
                <!-- 3 Media -->
                <li
                    class="nav-item"
                    role="presentation"
                    @click="setGlobalStorage('#objective_view_' + objective.id, '#objective_view_tab_media_' + objective.id)"
                >
                    <button
                        id="media-tab"
                        class="nav-link small link-muted active"
                        data-toggle="tab"
                        data-target="#media"
                        type="button"
                        role="tab"
                        aria-controls="media"
                        aria-selected="true"
                        @click="loadMedia()"
                    >
                        <i class="fa fa-folder-open pr-2"></i>
                        <span v-if="help">{{ trans('global.medium.title') }}</span>
                    </button>
                </li>
                <!-- 4 References -->
                <li
                    class="nav-item"
                    role="presentation"
                    @click="setGlobalStorage('#objective_view_' + objective.id, '#objective_view_references_' + objective.id)"
                >
                    <button
                        id="references-tab"
                        class="nav-link small link-muted"
                        data-toggle="tab"
                        data-target="#references"
                        type="button"
                        role="tab"
                        aria-controls="references"
                        aria-selected="false"
                        @click="loadReferences()"
                    >
                        <i class="fa fa-project-diagram pr-1"></i>
                        <span v-if="help">{{trans('global.referenceable_types.objective')}}</span>
                    </button>
                </li>
                <!-- 5 Achievements -->
                <li v-if="type === 'enabling'"
                    v-permission="'achievement_access'"
                    class="nav-item"
                    role="presentation"
                    @click="setGlobalStorage('#objective_view_' + objective.id, '#objective_view_achievements_' + objective.id)"
                >
                    <button
                        id="achievements-tab"
                        class="nav-link small link-muted"
                        data-toggle="tab"
                        data-target="#achievements"
                        type="button"
                        role="tab"
                        aria-controls="achievements"
                        aria-selected="false"
                        @click="loadAchievements()"
                    >
                        <i class="far fa-check-circle pr-1"></i>
                        <span v-if="help">{{trans('global.objective_tab')}}</span>
                    </button>
                </li>
                <!-- 6 Prerequisites -->
                <li
                    v-permission="'prerequisite_access'"
                    class="nav-item"
                    role="presentation"
                    @click="setGlobalStorage('#objective_view_' + objective.id, '#objective_view_prerequisites_' + objective.id)"
                >
                    <button
                        id="prerequisites-tab"
                        class="nav-link small link-muted"
                        data-toggle="tab"
                        data-target="#prerequisites"
                        type="button"
                        role="tab"
                        aria-controls="prerequisites"
                        aria-selected="false"
                        @click="loadPrerequisites()"
                    >
                        <i class="fa fa-puzzle-piece pr-1"></i>
                        <span v-if="help">{{trans('global.prerequisite.title')}}</span>
                    </button>
                </li>
                <!-- 7 Eventmanagement -->
                <li
                    class="nav-item"
                    role="presentation"
                    @click="setGlobalStorage('#objective_view_'+objective.id, '#objective_view_events_'+objective.id)"
                >
                    <button
                        id="events-tab"
                        class="nav-link small link-muted"
                        data-toggle="tab"
                        data-target="#events"
                        type="button"
                        role="tab"
                        aria-controls="events"
                        aria-selected="false"
                    >
                        <i class="fa fa-user-graduate pr-1"></i>
                        <span v-if="help">{{ trans('global.eventSubscription.title_alt') }}</span>
                    </button>
                </li>
                <!-- 8 LMS -->
                <li
                    v-permission="'lms_access'"
                    class="nav-item"
                    role="presentation"
                    @click="setGlobalStorage('#objective_view_'+objective.id, '#objective_view_lms_'+objective.id)"
                >
                    <button
                        id="lms-tab"
                        class="nav-link small link-muted"
                        data-toggle="tab"
                        data-target="#lms"
                        type="button"
                        role="tab"
                        aria-controls="lms"
                        aria-selected="false"
                        @click="loadLmsPlugin()"
                    >
                        <i class="fa fa-graduation-cap pr-1"></i>
                        <span v-if="help">{{ trans('global.lms.title_singular') }}</span>
                    </button>
                </li>
                <!-- Help-icon -->
                <li class="nav-item ml-auto pull-right">
                    <a
                        class="nav-link small link-muted pointer"
                        @click="help = !help"
                    >
                        <i class="fa fa-question pr-1"></i>
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- 1 Objectives -->
                <div
                    id="objectives"
                    class="tab-pane fade pt-2"
                    role="tabpanel"
                    aria-labelledby="objectives-tab"
                >
                    <span v-if="type === 'enabling'">
                        <ObjectiveBox
                            type="terminal"
                            :objective="objective.terminal_objective"
                        />
                    </span>
                    <span v-else>
                        <ObjectiveBox v-for="enablingObjective in objective.enabling_objectives"
                            type="enabling"
                            :objective="enablingObjective"
                        />
                    </span>
                </div>
                <!-- 2 Contents-->
                <div
                    id="content"
                    class="tab-pane fade pt-2"
                    role="tabpanel"
                    aria-labelledby="content-tab"
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
                <!-- 3 Media -->
                <div
                    id="media"
                    class="tab-pane fade show active"
                    role="tabpanel"
                    aria-labelledby="media-tab"
                >
                    <div class="row">
                        <div class="col-12">
                            <div id="sub_medium">
                                <ObjectiveMedia
                                    ref="Media"
                                    :model="model"
                                    :objective="objective"
                                    :repository="repository"
                                    :type="type"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 4 References-->
                <div
                    id="references"
                    class="tab-pane fade pt-2"
                    role="tabpanel"
                    aria-labelledby="references-tab"
                >
                    <div
                        v-permission="'objective_edit'"
                        class="card-tools"
                    >
                        <button
                            class="dropdown-item"
                            @click.prevent="show()"
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
                <!-- 5 Achievements-->
                <div
                    v-permission="'achievement_access'"
                    id="achievements"
                    class="tab-pane fade pt-2 box"
                    role="tabpanel"
                    aria-labelledby="achievements-tab"
                >
                    <Achievements v-if="type === 'enabling'"
                        ref="Achievements"
                        :objective="objective"
                        :type="type"
                        :settings="setting"
                    />
                </div>
                <!-- 6 Prerequisites -->
                <div
                    v-permission="'prerequisite_create, objective_edit'"
                    id="prerequisites"
                    class="tab-pane fade pt-2"
                    role="tabpanel"
                    aria-labelledby="prerequisites-tab"
                >
                    <div class="card-tools">
                        <button
                            class="dropdown-item"
                            @click.prevent="this.globalStore?.showModal('prerequisite-objective-modal');"
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
                <!-- 7 Eventmanagement -->
                <div
                    id="events"
                    class="tab-pane fade pt-2"
                    role="tabpanel"
                    aria-labelledby="events-tab"
                >
                    <Eventmanagement
                        ref="eventPlugin"
                        :model="objective"
                        :curriculum="objective.curriculum"
                    />
                </div>
                <!-- 8 LMS -->
                <div
                    v-permission="'lms_access'"
                    id="lms"
                    class="tab-pane fade pt-0"
                    role="tabpanel"
                    aria-labelledby="lms-tab"
                >
                    <Lms
                        ref="LmsPlugin"
                        :referenceable_type="model"
                        :referenceable_id="objective.id"
                    />
                </div>
            </div>

            <Teleport to="body">
                <ReferenceObjectiveModal/>
                <PrerequisiteObjectiveModal
                    :params="{
                        successor_type: this.model,
                        successor_id: this.objective.id,
                        url: '/prerequisites',
                    }"
                />
                <LmsModal
                    :params="{
                        referenceable_type: this.model,
                        referenceable_id: this.objective.id,
                        url: '/lmsReferences'
                    }"
                />
                <SubscribeModal/>
                <MediumModal/>
                <TerminalObjectiveModal/>
                <EnablingObjectiveModal/>
            </Teleport>
            <Teleport to="#customTitle">
                <small>
                    <span v-if="type === 'enabling'">
                        {{ trans('global.enablingObjective.title_singular') }}
                    </span>
                    <span v-else>
                        {{ trans('global.terminalObjective.title_singular') }}
                    </span>
                </small>
                <a v-if="editable"
                    class="btn btn-flat text-secondary px-2 mx-1"
                    @click="editObjective()"
                >
                    <i class="fa fa-pencil-alt"></i>
                </a>
            </Teleport>
        </div>
    </div>
</template>
<script>
import Media from "../media/Media.vue";
import Contents from '../content/Contents.vue';
import TerminalObjectiveModal from "./TerminalObjectiveModal.vue";
import EnablingObjectiveModal from "./EnablingObjectiveModal.vue";
import Achievements from "./Achievements.vue";
import Prerequisites from "../prerequisites/Prerequisites.vue";
import Eventmanagement from "../../../../app/Plugins/Eventmanagement/resources/js/components/Events.vue";
import ObjectiveBox from "./ObjectiveBox.vue";
import Variants from "./Variants.vue";
import ObjectiveMedia from "./ObjectiveMedia.vue";
import Lms from "../lms/Lms.vue";
import LmsModal from "../lms/LmsModal.vue";
import References from "../reference/References.vue";
import Quotes from "../quote/Quotes.vue";
import {useGlobalStore} from "../../store/global";
import MediumModal from "../media/MediumModal.vue";
import ReferenceObjectiveModal from "../reference/ReferenceObjectiveModal.vue";
import PrerequisiteObjectiveModal from "../prerequisites/PrerequisiteObjectiveModal.vue";
import SubscribeModal from "../subscription/SubscribeModal.vue";

export default {
    name: "objective",
    components: {
        SubscribeModal,
        PrerequisiteObjectiveModal,
        ReferenceObjectiveModal,
        MediumModal,
        ObjectiveMedia,
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
        Media,
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
        editable: {
            type: Boolean,
            default: false,
        },
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            componentId: this.$.uid,
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
            errors: {},
            search: '',
        }
    },
    mounted() {
        if (typeof this.objective.terminal_objective === 'object') {
            this.type = 'enabling';
            this.model = 'App\\EnablingObjective';
        } else {
            this.type = 'terminal';
            this.model = 'App\\TerminalObjective';
        }

        //event listener
        this.$eventHub.on('terminalObjective-updated', () => {
            this.globalStore?.closeModal('terminal-objective-modal');
            this.loadObjectives(this.activetab);
        });

        this.$eventHub.on('enablingObjective-updated', () => {
            this.globalStore?.closeModal('enabling-objective-modal');
            this.loadObjectives(this.activetab);
        });

        this.$eventHub.on('reference-added', () => {
            this.globalStore?.closeModal('reference-objective-modal');
            this.loadReferences();
        });
        this.$eventHub.on('reference-deleted', () => {
            this.globalStore?.closeModal('reference-objective-modal');
            this.loadReferences();
        });
        this.$eventHub.on('prerequisite-added', () => {
            this.globalStore?.closeModal('prerequisite-objective-modal');
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

        this.$eventHub.on('shareLms', (id) => {
            //console.log(id);
            this.globalStore?.showModal('subscribe-modal', {
                modelId: id,
                modelUrl: 'lmsReference',
                shareWithUsers: true,
                shareWithGroups: true,
                shareWithOrganizations: true,
                shareWithToken: false,
                canEditCheckbox: false,
            });
        });
    },
    methods: {
        show() {
            this.globalStore?.showModal('reference-objective-modal', {
                subscribable_type: this.model,
                subscribable_id: this.objective.id,
                url: '/referenceSubscriptions',
            });
        },
        editObjective() {
            switch (this.type) {
                case "enabling":    this.globalStore?.showModal('enabling-objective-modal', this.objective);
                    break;
                case "terminal":    this.globalStore?.showModal('terminal-objective-modal', this.objective);
                    break;
            }
        },
        //Loader
        loaderContents: function() {
            this.$refs.Contents.loaderEvent();
        },
        loadMedia() {
            this.$refs.Media.loaderEvent();
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
}
</script>
<style>
.p-margin-0 p { margin-bottom: 0px !important }
</style>