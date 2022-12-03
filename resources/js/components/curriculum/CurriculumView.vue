<template>
    <div class="col-12">
        <ul class="nav nav-tabs"
            role="tablist"
            aria-label="Curriculum Tabs">
            <li class="nav-item"
                @click="setLocalStorage('#curriculum_'+curriculum.id, '#content-tab')"
                role="tab"
                aria-controls="content-tab"
                aria-selected="true">
                <a class="nav-link link-muted"
                   id="content-nav-tab"
                   data-toggle="pill"
                   href="#content-tab"
                   @click="loaderEvent()"
                >
                    <i class="fa fa-align-justify pr-2"></i>{{ trans('global.content.index') }}
                </a>
            </li>
            <li class="nav-item"
                @click="setLocalStorage('#curriculum_'+curriculum.id, '#curriculm-tab')"
                role="tab"
                aria-controls="curriculm-tab"
                aria-selected="false">
                <a class="nav-link link-muted"
                   id="curriculm-nav-tab"
                   data-toggle="pill"
                   href="#curriculm-tab"
                >
                    <i class="fas fa-th pr-2"></i>{{ trans('global.objective_tab') }}
                </a>
            </li>
            <li class="nav-item "
                @click="setLocalStorage('#curriculum_'+curriculum.id, '#medium-tab')"
                role="tab"
                aria-controls="medium-tab"
                aria-selected="false">
                <a class="nav-link link-muted"
                   id="medium-nav-tab"
                   data-toggle="pill"
                   href="#medium-tab">
                    <i class="fa fa-folder-open pr-2"></i>{{ trans('global.media.title') }}
                </a>
            </li>
            <li class="nav-item "
                @click="setLocalStorage('#curriculum_'+curriculum.id, '#glossar-tab')"
                role="tab"
                aria-controls="glossar-tab"
                aria-selected="false">
                <a v-if="curriculum.glossar != null"
                   class="nav-link link-muted"
                   id="glossar-nav-tab"
                   data-toggle="pill"
                   href="#glossar-tab">
                    <i class="fa fa-book-open pr-2"></i>{{ trans('global.glossar.title_singular') }}
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
                class="nav-item"
                role="tab"
                aria-controls="logbook-tab"
                aria-selected="false">
                <a v-if="logbook"
                   v-can="'logbook_access'"
                   class="nav-link link-muted"
                   :href="'/logbooks/'+ logbook.id "
                   id="logbooks-nav-tab">
                    <i class="fas fa-book pr-2"></i>{{ trans('global.logbook.title_singular') }}
                </a>
                <a v-else
                   v-can="'logbook_create'"
                   class="nav-link link-muted"
                   :href="'/logbooks/create?subscribable_type=App\\Course&subscribable_id='+ course.id "
                   id="logbooks-nav-tab">
                    <i class="fas fa-book pr-2"></i>{{trans('global.logbook.create')}}
                </a>
            </li>

            <li v-if="course && showGenerateCertificate"
                v-permission="'certificate_access'"
                class="nav-item ml-auto"
            >
                <a class="nav-link link-muted"
                   @click="show('certificate-generate-modal')"
                   id="certificate-nav-tab">
                    <i class="fa fa-certificate pr-2"></i>{{ trans('global.certificate.generate') }}
                </a>
            </li>
            <li v-permission="'certificate_create'"
                class="nav-item ml-auto">
                <a class="nav-link link-muted"
                   :href="'/certificates/create?curriculum_id='+ curriculum.id "
                   id="certificate-nav-tab">
                    <i class="fa fa-certificate pr-2"></i>{{trans('global.certificate.create')}}
                </a>
            </li>
            <li v-can="'curriculum_print'"
                class="nav-item">
                <a class="nav-link link-muted"
                   data-toggle="tooltip" :title="trans('global.curriculum.print')"
                   @click="printCurriculum()"
                   id="config-nav-tab">
                    <i class="fa fa-print"></i>
                </a>
            </li>
            <li class="nav-item "
                @click="setLocalStorage('#curriculum_'+curriculum.id, '#description-tab')">
                <a class="nav-link link-muted"
                   id="description-nav-tab"
                   data-toggle="pill"
                   href="#description-tab">
                    <i class="fa fa-info"></i>
                </a>
            </li>
            <li v-can="'curriculum_edit'"
                class="nav-item">
                <a class="nav-link link-muted"
                   data-toggle="tooltip" title="Fix order_ids"
                   @click="resetOrderIds('/curricula/'+ curriculum.id +'/resetOrderIds')"
                   id="fix-order-nav-tab">
                    <i class="fa fa-wrench"></i>
                </a>
            </li>
            <li v-can="'curriculum_create'"
                data-toggle="tooltip" title="Export curriculum"
                class="nav-item">
                <a class="nav-link link-muted"
                   @click="exportCurriculum()"
                   id="export-curriculum-nav-tab">
                    <i class="fas fa-cloud-download-alt"></i>
                </a>
            </li>
            <li v-can="'curriculum_edit'"
                class="nav-item">
                <a class="nav-link link-muted"
                   :href="'/curricula/'+ curriculum.id +'/edit'"
                   id="edit-curriculum-nav-tab">
                    <i class="fa fa-pencil-alt"></i>
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
                       :public="1"
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
        <medium-export-modal v-can="'curriculum_create'"></medium-export-modal>

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
                cur: this.curriculum,
                showGenerateCertificate: false
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
                //this.$modal.show('medium-export-modal', {'id': this.curriculum.id });
                this.$modal.show('medium-export-modal', {'url': '/curricula/' + this.curriculum.id + '/export', 'header': window.trans.global.curriculum.export });
            },
            printCurriculum() {
                this.$modal.show('medium-export-modal', {'url': '/curricula/' + this.curriculum.id + '/print', 'header': window.trans.global.curriculum.print });

            },
            externalEvent: function(value) {
                this.showGenerateCertificate = value;
            },
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
