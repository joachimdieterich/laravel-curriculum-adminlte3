<template>
    <div>
        <div v-permission="'achievement_access'"
             v-if="this.course">
            <div id="user-datatable-wrapper"
                 class="w-100 dataTablesWrapper">
                <DataTable
                    id="curriculum-user-datatable"
                    :columns="columns"
                    :options="options"
                    :ajax="'/courses/list?course_id=' + this.course?.id"
                    :search="search"
                    width="100%"
                ></DataTable>
            </div>
        </div>

        <hr class="clearfix">
        <div class="row pt-3">
            <div class="col-12">
                <ul class="nav nav-tabs"
                    role="tablist"
                    aria-label="Curriculum Tabs">
                    <li class="nav-item"
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
                        role="tab"
                        aria-controls="medium-tab"
                        aria-selected="false">
                        <a class="nav-link link-muted"
                           id="medium-nav-tab"
                           data-toggle="pill"
                           href="#medium-tab">
                            <i class="fa fa-folder-open pr-2"></i>{{ trans('global.medium.title') }}
                        </a>
                    </li>
                    <li class="nav-item "
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
{{}}
                    <li v-if="(this.store.getSelectedIds('curriculum-user-datatable')?.length > 0) && course"
                        v-permission="'certificate_access'"
                        class="nav-item ml-auto"
                    >
                        <a class="nav-link link-muted"
                           @click.prevent="generateCertificate()"
                           id="certificate-nav-tab">
                            <i class="fa fa-certificate pr-2"></i>{{ trans('global.certificate.generate') }}
                        </a>
                    </li>
                    <li v-permission="'certificate_create'"
                        class="nav-item ml-auto">
                        <a class="nav-link link-muted"
                           @click.prevent="this.showCertificateModal = true"
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
                        @click="setGlobalStorage('#curriculum_'+curriculum.id, '#description-tab')">
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
                    <li v-if="$userId == curriculum.owner_id"
                        v-can="'curriculum_edit'"
                        class="nav-item">
                        <a class="nav-link link-muted"
                           @click="edit()"
                           id="edit-curriculum-nav-tab">
                            <i class="fa fa-pencil-alt"></i>
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="custom-content-below-tabContent">
                    <div class="tab-pane fade show active" id="curriculm-tab" role="tabpanel" aria-labelledby="curriculm-nav-tab">
                        <TerminalObjectives
                            ref="terminalObjectives"
                            :curriculum="curriculum"
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
                            :subscribable_id="curriculum.id">
                        </contents>
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
                             v-dompurify-html="curriculum.description"></div>
                    </div>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <CurriculumModal
                :show="this.showCurriculumModal"
                @close="this.showCurriculumModal = false"
                :params="this.currentCurriculum"
            ></CurriculumModal>
            <CertificateModal
                :show="this.showCertificateModal"
                @close="this.showCertificateModal = false"
                :params="{'curriculum_id': this.curriculum.id}"
            ></CertificateModal>
            <GenerateCertificateModal></GenerateCertificateModal>
        </Teleport>
    </div>
</template>

<script>
import CurriculumModal from "../curriculum/CurriculumModal";
import Media from "../media/Media";
import TerminalObjectives from '../objectives/TerminalObjectives'
import Glossars from '../glossar/Glossars';
import Contents from '../content/Contents';

import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import 'datatables.net-select-bs5';
import {useDatatableStore} from "../../store/datatables";
import CertificateModal from "../certificate/CertificateModal.vue";
import GenerateCertificateModal from "../certificate/GenerateCertificateModal.vue";
import {useGlobalStore} from "../../store/global";

DataTable.use(DataTablesCore);

export default {
    name: "curriculum",
    components:{
        GenerateCertificateModal,
        CertificateModal,
        CurriculumModal,
        TerminalObjectives,
        Glossars,
        Contents,
        DataTable,
        Media
    },
    props: {
        curriculum: {
            default: null
        },
        course: {
            default: null
        },
       /* usersCurricula: {
            default: null
        },
        current_curriculum_cross_reference_id: {
            default: null
        },*/
        objectivetypes: {
            type: Array
        },
        settings: {
            type: Object
        },

    },
    setup () { //https://pinia.vuejs.org/core-concepts/getters.html#passing-arguments-to-getters
        const store = useDatatableStore();
        const globalStore = useGlobalStore();
        return {
            store,
            globalStore,
        }
    },
    data() {
        return {
            componentId: this.$.uid,
            showCurriculumModal: false,
            currentCurriculum: {},
            showCertificateModal: false,
            columns: [
                { title: window.trans.global.user.fields.username, data: 'username', searchable: true},
                { title: window.trans.global.lastname, data: 'lastname', searchable: true},
                { title: window.trans.global.firstname, data: 'firstname', searchable: true},
                { title: window.trans.global.role.title_singular, data: 'role', searchable: true},
                { title: window.trans.global.progress.title_singular,  data: 'progress',},
            ],
            options : this.$dtOptions,
            search: ''
        }
    },
    mounted() {
        this.currentCurriculum = this.curriculum;

        this.store.addToDatatables(
            {
                'datatable': 'curriculum-user-datatable',
                'select': (this.store.getDatatable('curriculum-user-datatable')?.select) ? false : true,
                'selectedItems': []
            }
        )
        const dt = $('#curriculum-user-datatable').DataTable();
        dt.on('select', function(e, dt, type, indexes) {
            let selection = dt.rows('.selected').data().toArray()
            this.store.setSelectedIds('curriculum-user-datatable', selection);
            this.$refs.terminalObjectives.externalEvent(this.store.getSelectedIds('curriculum-user-datatable'));
        }.bind(this));

        this.$eventHub.on('curriculum-updated', (curriculum) => {
            this.currentCurriculum = curriculum;
            this.showCurriculumModal = false;
        });


    },
    methods: {
        editCurriculum(){
            this.showCurriculumModal = true;
        },
        loaderEvent: function() {
            this.$refs.Contents.loaderEvent();
        },
        setCrossReferenceCurriculumId: function(curriculum_id) { //can be called external
            this.settings.cross_reference_curriculum_id = curriculum_id;
        },
        generateCertificate(){
            this.globalStore?.showModal('generate-certificate-modal', {'curriculum_id': this.curriculum.id});
        }
        /*externalEvent: function(ids) {
            this.reloadEnablingObjectives(ids);
        },
        async reloadEnablingObjectives(ids) {
            try {
                this.cur = (await axios.post('/curricula/'+this.curriculum.id+'/achievements', {'user_ids' : ids})).data.curriculum;
            } catch(error) {
                this.errors = error.response.data.errors;
            }
        },*/

    }
}
</script>
