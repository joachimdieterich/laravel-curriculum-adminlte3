<template>
    <div class="col-12">
        <ul class="nav nav-tabs"
            role="tablist">
            <li class="nav-item"
                @click="setLocalStorage('#curriculum_'+curriculum.id, '#content-tab')">
                <a class="nav-link link-muted"
                   id="content-nav-tab"
                   data-toggle="pill"
                   href="#content-tab"
                   role="tab"
                   aria-controls="content-tab"
                   aria-selected="true"
                   @click="loaderEvent()"
                >
                    <i class="fa fa-align-justify pr-2"></i>{{trans('global.content.index')}}
                </a>
            </li>
            <li class="nav-item"
                @click="setLocalStorage('#curriculum_'+curriculum.id, '#curriculm-tab')">
                <a class="nav-link link-muted"
                   id="curriculm-nav-tab"
                   data-toggle="pill"
                   href="#curriculm-tab"
                   role="tab"
                   aria-controls="curriculm-tab"
                   aria-selected="false">
                    <i class="fas fa-th pr-2"></i>{{trans('global.objective_tab')}}
                </a>
            </li>
            <li class="nav-item "
                @click="setLocalStorage('#curriculum_'+curriculum.id, '#medium-tab')">
                <a class="nav-link link-muted"
                   id="medium-nav-tab"
                   data-toggle="pill"
                   href="#medium-tab"
                   role="tab"
                   aria-controls="medium-tab"
                   aria-selected="true">
                    <i class="fa fa-folder-open pr-2"></i>{{trans('global.media.title')}}
                </a>
            </li>
            <li class="nav-item "
                @click="setLocalStorage('#curriculum_'+curriculum.id, '#glossar-tab')">
                <a v-if="curriculum.glossar != null"
                   class="nav-link link-muted"
                   id="glossar-nav-tab"
                   data-toggle="pill"
                   href="#glossar-tab"
                   role="tab"
                   aria-controls="glossar-tab"
                   aria-selected="true">
                    <i class="fa fa-book-open pr-2"></i>{{trans('global.glossar.title_singular')}}
                </a>
                <a v-else
                   v-can="'glossar_create'"
                   class="nav-link link-muted"
                   id="glossar-nav-tab"
                   :href="'/glossar/create?subscribable_type=App\\Curriculum&subscribable_id='+ curriculum.id "
                 >
                    <i class="fa fa-book-open pr-2"></i>{{trans('global.glossar.create')}}
                </a>
            </li>
            <li v-if="course"
                class="nav-item">
                <a v-if="logbook"
                   v-can="'logbook_access'"
                   class="nav-link link-muted"
                   :href="'/logbooks/'+ logbook.id "
                   id="logbooks-nav-tab">
                    <i class="fas fa-book pr-2"></i>{{trans('global.logbook.title_singular')}}
                </a>
                <a v-else
                   v-can="'logbook_create'"
                   class="nav-link link-muted"
                   :href="'/logbooks/create?subscribable_type=App\\Course&subscribable_id='+ course.id "
                   id="logbooks-nav-tab">
                    <i class="fas fa-book pr-2"></i>{{trans('global.logbook.create')}}
                </a>
            </li>

            <li v-if="course"
                v-can="'certificate_access'"
                class="nav-item ml-auto">
                <a class="nav-link link-muted"
                   @click="show('certificate-generate-modal')"
                   id="certificate-nav-tab">
                    <i class="fa fa-certificate pr-2"></i>{{trans('global.certificate.generate')}}
                </a>
            </li>
            <li v-else
                v-can="'certificate_create'"
                class="nav-item ml-auto">
                <a class="nav-link link-muted"
                   :href="'/certificates/create?curriculum_id='+ curriculum.id "
                   id="certificate-nav-tab">
                    <i class="fa fa-certificate pr-2"></i>{{trans('global.certificate.create')}}
                </a>
            </li>
            <li class="nav-item "
                @click="setLocalStorage('#curriculum_'+curriculum.id, '#description-tab')">
                <a class="nav-link link-muted"
                   id="description-nav-tab"
                   data-toggle="pill"
                   href="#description-tab"
                   role="tab"
                   aria-controls="description-tab"
                   aria-selected="true">
                    <i class="fa fa-info"></i>
                </a>
            </li>
            <li v-can="'curriculum_edit'"
                class="nav-item">
                <a class="nav-link link-muted"
                   @click="resetOrderIds('/curricula/'+ curriculum.id +'/resetOrderIds')"
                   id="config-nav-tab">
                    <i class="fa fa-wrench"></i>
                </a>
            </li>
            <li v-can="'curriculum_create'"
                class="nav-item">
                <a class="nav-link link-muted"
                   @click="exportCurriculum()"
                   id="config-nav-tab">
                    <i class="fas fa-cloud-download-alt"></i>
                </a>
            </li>
            <li v-can="'curriculum_edit'"
                class="nav-item">
                <a class="nav-link link-muted"
                   :href="'/curricula/'+ curriculum.id +'/edit'"
                   id="config-nav-tab">
                    <i class="fa fa-cog"></i>
                </a>
            </li>

        </ul>

        <div class="tab-content" id="custom-content-below-tabContent">
            <div class="tab-pane fade show active" id="curriculm-tab" role="tabpanel" aria-labelledby="curriculm-nav-tab">
                <TerminalObjectives
                    ref="terminalObjectives"
                    :curriculum="cur"
                    :objectivetypes="objectivetypes"
                    :settings="settings">
                </TerminalObjectives>
            </div>
            <div class="tab-pane fade "
                 id="content-tab"
                 role="tab"
                 aria-labelledby="content-nav-tab">
                <contents
                    ref="Contents"
                    subscribable_type="App\Curriculum"
                    :subscribable_id="curriculum.id"></contents>
            </div>
            <div class="tab-pane fade "
                 id="medium-tab"
                 role="tab"
                 aria-labelledby="medium-nav-tab">
                <media subscribable_type="App\Curriculum"
                       :subscribable_id="curriculum.id"
                       format="list">
                </media>
            </div>
            <div v-if="curriculum.glossar != null"
                 class="tab-pane fade"
                 id="glossar-tab"
                 role="tab"
                 aria-labelledby="glossar-nav-tab">
                <glossars
                    :glossar="curriculum.glossar">
                </glossars>
            </div>
            <div class="tab-pane fade"
                 id="description-tab"
                 role="tab"
                 aria-labelledby="description-nav-tab">
                <div class="card p-3"
                     v-html="curriculum.description"></div>
            </div>

        </div>
        <curriculum-export-modal v-can="'curriculum_create'"></curriculum-export-modal>

    </div>

</template>

<script>
    import TerminalObjectives from '../objectives/TerminalObjectives.vue'
    import Glossars from '../glossar/Glossars';
    import Media from '../media/Media';
    import Contents from '../content/Contents';

    export default {
        props: {
            'curriculum': Object,
            'course': null,
            'usersCurricula': null,
            'current_curriculum_cross_reference_id': null,
            'logbook': null,
            'objectivetypes': Array,
            'settings': Object,
        },
        data () {
            return {
                cur: this.curriculum
            };
        },

        methods: {
            setCrossReferenceCurriculumId: function(curriculum_id) { //can be called external
                this.settings.cross_reference_curriculum_id = curriculum_id;
            },
            show(modal){
                this.$modal.show(modal, { 'curriculum_id': this.curriculum.id });
            },
            loaderEvent: function() {
                this.$refs.Contents.loaderEvent();
            },
            resetOrderIds(path) {
                if (confirm("Reihenfolge zurücksetzen?")){
                    try {
                        axios.patch(path);
                    } catch(error) {
                        this.errors = error.response.data.errors;
                    }
                    window.location = '/curricula/'+ this.curriculum.id;

                }
            },
            exportCurriculum(){
                this.$modal.show('curriculum-export-modal', {'id': this.curriculum.id });
            }
        },
        mounted() {
        },
        components: {
            TerminalObjectives,
            Media,
            Glossars,
            Contents
        }
    }
</script>
