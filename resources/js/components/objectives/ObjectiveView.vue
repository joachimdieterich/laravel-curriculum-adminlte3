<template>
    <div class="row" >
        <modals-container/>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-bullseye"></i>
                    <span v-if="type === 'enabling'">{{ trans('global.enablingObjective.title_singular') }}</span>
                    <span v-else> {{ trans('global.terminalObjective.title_singular') }}</span>

                    <div v-can="'task_edit'" class="card-tools pr-2">
                        <a @click.prevent="editObjective()" >
                            <i class="fa fa-pencil-alt"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body"
                     v-html="objective.title">
                </div>
                <div class="card-footer">
                    <div class="float-left">
                        <small>
                            {{ trans('global.enablingObjective.fields.time_approach') }}: {{objective.time_approach}}
                        </small>
                    </div>
                    <small class="float-right">
                        {{ trans('global.updated_at') }}: {{objective.updated_at}}
                    </small>
                </div>
            </div>

            <ul class="nav nav-tabs ">
                <li v-if="objective.description != ''"
                    class="nav-item small">
                    <a class="nav-link active show link-muted"
                       href="#description"
                       data-toggle="tab">
                        <i class="fa fa-info pr-1"></i>
                        <span v-if="help">{{ trans('global.description') }}</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link small link-muted"
                       href="#related_objectives"
                       data-toggle="tab">
                        <i class="fa fa-sitemap pr-1"></i>
                        <span v-if="type === 'terminal'" >
                            <span v-if="help">{{ trans('global.subordinate_element') }}</span>
                        </span>
                        <span v-else >
                            <span v-if="help">{{ trans('global.superordinate_element_singular') }}</span>
                        </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link small link-muted"
                       data-toggle="tab"
                       href="#content-tab"
                       role="tab"
                       aria-controls="content-tab"
                       aria-selected="true"
                       @click="loaderEvent()">
                        <i class="fa fa-align-justify pr-2"></i>
                        <span v-if="help">{{trans('global.content.index_alt')}}</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link small link-muted"
                       href="#tab_media"
                       data-toggle="tab">
                        <i class="fa fa-folder-open pr-2"></i>
                        <span v-if="help">{{trans('global.media.title')}}</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link small link-muted"
                       href="#references"
                       data-toggle="tab">
                        <i class="fa fa-project-diagram pr-1"></i>
                        <span v-if="help">{{trans('global.referenceable_types.objective')}}</span>
                    </a>
                </li>

                <li v-can="'achievement_access'"
                    v-if="this.type === 'enabling'"
                    class="nav-item">
                    <a class="nav-link small link-muted"
                       href="#achievements"
                       data-toggle="tab"
                       @click="loadAchievements()">
                        <i class="far fa-check-circle pr-1"></i>
                        <span v-if="help">{{trans('global.objective_tab')}}</span>
                    </a>
                </li>

                <li v-can="'prerequisite_access'"
                    class="nav-item">
                    <a class="nav-link small link-muted"
                       href="#prerequisites"
                       data-toggle="tab"
                       @click="loadPrerequisites()">
                        <i class="fa fa-puzzle-piece pr-1"></i>
                        <span v-if="help">{{trans('global.prerequisite.title')}}</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link small link-muted"
                       href="#events"
                       data-toggle="tab">
                        <i class="fa fa-user-graduate pr-1"></i>
                        <span v-if="help">{{trans('global.eventSubscription.title_alt')}}</span>
                    </a>
                </li>
               <!-- <li class="nav-item pull-right">
                    <a class="nav-link small link-muted"
                       @click="help = !help">
                        <i class="fa fa-question pr-1"></i>
                    </a>
                </li>-->
            </ul>

            <div class="tab-content ">
                <!-- 1 Description -->
                <div class="tab-pane active show "
                     id="description"
                     >
                    <div class="card-body" v-html="objective.description"></div>
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane pt-2"
                     id="related_objectives" >
                    <span v-if="type === 'enabling'">
                        <ObjectiveBox type="terminal"
                            :objective="objective.terminal_objective">
                        </ObjectiveBox>
                    </span>
                    <span v-else>
                        <ObjectiveBox
                            v-for="enablingObjective in objective.enabling_objectives"
                            v-bind:key="'ena_'+enablingObjective.id"
                            type="enabling"
                            :objective="enablingObjective">
                        </ObjectiveBox>
                    </span>
                </div>
                <!--                    1 Contents -->
                <div class="tab-pane fade "
                     id="content-tab"
                     role="tab"
                     aria-labelledby="content-nav-tab">
                    <span v-if="type === 'enabling'">
                        <contents
                            ref="Contents"
                            subscribable_type="App\EnablingObjective"
                            :subscribable_id="objective.id"></contents>
                    </span>
                    <span v-else>
                        <contents
                            ref="Contents"
                            subscribable_type="App\TerminalObjective"
                            :subscribable_id="objective.id"></contents>
                    </span>
                </div>
                <!--                    1 Files -->
                <div class="tab-pane"
                     id="tab_media"
                     name="tab_media">
                    <div class="row">
                        <div class="col-12">
                             <div
                                class="tab-pane active show"
                                id="sub_medium"
                                name="sub_medium">
                                 <objectiveMedia
                                     :objective="objective"
                                     :repository="repository"
                                     :type="type"/>
                            </div>
                        </div>
                    </div>
                </div><!-- /.tab-pane -->
                <!--                References -->
                <div class="tab-pane pt-2"
                     id="references">
                    <div class="card-tools"
                         v-can="'objective_edit'">
                        <button
                            class="dropdown-item "
                            @click.prevent="open('reference-objective-modal')">
                            <i class="fa fa-plus pull-right"></i>
                        </button>
                    </div>

                    <references
                        :reference_subscriptions="reference_subscriptions"
                        :curricula_list="curricula_list"
                    ></references>
                    <quotes
                        :quote_subscriptions="quote_subscriptions"
                        :quote_curricula_list="quote_curricula_list"
                    ></quotes>
                </div>

                <div v-can="'achievement_access'"
                    class="tab-pane pt-2 box"
                     id="achievements">
                    <Achievements
                        v-if="this.type === 'enabling'"
                        ref="Achievements"
                        :objective="objective"
                        :type="type"
                        :settings="setting">
                    </Achievements>
                </div>

                <div class="tab-pane pt-2"
                     id="prerequisites"
                     v-can="'prerequisite_access'">
                    <div class="card-tools"
                         v-can="'prerequisite_create'">
                        <button
                            v-can="'objective_edit'"
                            class="dropdown-item "
                            @click.prevent="open('prerequisite-modal')">
                            <i class="fa fa-plus pull-right"></i>
                        </button>
                    </div>
                    <prerequisites
                        ref="Prerequisites"
                        :successor_type="model"
                        :successor_id="objective.id"/>
                </div>


                <div class="tab-pane pt-2"
                    id="events">
                    <eventmanagement ref="eventPlugin"
                         :model="objective"
                         :curriculum="objective.curriculum"></eventmanagement>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import References from '../reference/References';
    import Quotes from '../quote/Quotes';
    import Contents from '../content/Contents';
    import Eventmanagement from '../../../../app/Plugins/Eventmanagement/resources/js/components/Events';
    import ObjectiveBox from './ObjectiveBox'
    import Achievements from './Achievements'
    import ObjectiveMedia from "./ObjectiveMedia";
    import Prerequisites from "../prerequisites/Prerequisites";

    export default {
        props: {
            'objective': Object,
            'repository': Object
        },
        data() {
            return {
                type: null,
                media_subscriptions: [],
                reference_subscriptions: [],
                curricula_list: [],
                quote_subscriptions: [],
                quote_curricula_list: [],
                categories: [],
                help: true,
                setting: {
                    'last': null
                },
                model: '',
                errors: {}
            }
        },
        methods: {
            editObjective() {
                this.$modal.show(this.type+'-objective-modal', {'objective': this.objective, 'method': 'PATCH' });
            },
            open(modal) {
                if (modal == 'reference-objective-modal'){
                    this.$modal.show(modal, {'referenceable_type': this.model, 'referenceable_id': this.objective.id, 'requestUrl': '/referenceSubscriptions'});
                } else {
                    this.$modal.show(modal, {'referenceable_type': this.model, 'referenceable_id': this.objective.id});
                }
            },

            loaderEvent: function() {
                this.$refs.Contents.loaderEvent();
            },
            loadAchievements() {
                this.$refs.Achievements.loaderEvent();
            },
            loadPrerequisites(){
                this.$refs.Prerequisites.loaderEvent();
            },

        },
        mounted() {
            if (typeof this.objective.terminal_objective === 'object'){
                this.type = 'enabling';
                this.model = 'App\\EnablingObjective';
            } else {
                this.type = 'terminal';
                this.model = 'App\\TerminalObjective';
            }

            axios.get('/'+this.type+'Objectives/' + this.objective.id + '/referenceSubscriptionSiblings').then(response => {
                if (response.data.siblings.length !== 0) {
                    this.reference_subscriptions = response.data.siblings;
                    this.curricula_list = response.data.curricula_list;

                }
            }).catch(e => {
                //this.errors = response.data.errors;
            });

            axios.get('/'+this.type+'Objectives/' + this.objective.id + '/quoteSubscriptions').then(response => {
                if (response.data.quotes_subscriptions.length !== 0) {
                    this.quote_subscriptions = response.data.quotes_subscriptions;
                    this.quote_curricula_list = response.data.curricula_list;
                 }
            }).catch(e => {
                //this.errors = response.data.errors;
            });



        },
        computed: {
            scr: function () {
                return '/media/' + this.objective.media_subscriptions.medium_id;
            },
        },
        components: {
            Prerequisites,
            ObjectiveMedia,
            References,
            Quotes,
            ObjectiveBox,
            Eventmanagement,
            Contents,
            Achievements
        }

    }
    </script>
