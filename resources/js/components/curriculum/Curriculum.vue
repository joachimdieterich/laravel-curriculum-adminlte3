<template>
    <div>
        <div v-if="course"
            v-permission="'achievement_access'"
        >
            <div
                id="user-datatable-wrapper"
                class="w-100 dataTablesWrapper"
            >
                <DataTable
                    id="curriculum-user-datatable"
                    :columns="columns"
                    :options="options"
                    :ajax="'/courses/list?course_id=' + course.id"
                    :search="search"
                    width="100%"
                />
            </div>
        </div>

        <hr class="clearfix">
        <div class="row pt-3">
            <div class="col-12">
                <ul
                    class="nav nav-tabs"
                    role="tablist"
                    aria-label="Curriculum Tabs"
                >
                    <li
                        class="nav-item"
                        role="tab"
                        aria-controls="content-tab"
                        aria-selected="true"
                    >
                        <a
                            id="content-nav-tab"
                            class="nav-link link-muted"
                            data-toggle="pill"
                            href="#content-tab"
                            @click="loaderEvent()"
                        >
                            <i class="fa fa-align-justify pr-2"></i>
                            {{ trans('global.content.index') }}
                        </a>
                    </li>
                    <li
                        class="nav-item"
                        role="tab"
                        aria-controls="curriculm-tab"
                        aria-selected="false"
                    >
                        <a
                            id="curriculm-nav-tab"
                            class="nav-link link-muted"
                            data-toggle="pill"
                            href="#curriculm-tab"
                        >
                            <i class="fas fa-th pr-2"></i>
                            {{ trans('global.objective_tab') }}
                        </a>
                    </li>
                    <li
                        class="nav-item"
                        role="tab"
                        aria-controls="medium-tab"
                        aria-selected="false"
                    >
                        <a
                            id="medium-nav-tab"
                            class="nav-link link-muted"
                            data-toggle="pill"
                            href="#medium-tab"
                        >
                            <i class="fa fa-folder-open pr-2"></i>
                            {{ trans('global.medium.title') }}
                        </a>
                    </li>
                    <li
                        class="nav-item"
                        role="tab"
                        aria-controls="glossar-tab"
                        aria-selected="false"
                    >
                        <a v-if="curriculum.glossar != null"
                            id="glossar-nav-tab"
                            class="nav-link link-muted"
                            data-toggle="pill"
                            href="#glossar-tab"
                        >
                            <i class="fa fa-book-open pr-2"></i>
                            {{ trans('global.glossar.title_singular') }}
                        </a>
                        <a v-else
                            v-permission="'glossar_create'"
                            id="glossar-nav-tab"
                            class="nav-link link-muted"
                            :href="'/glossar/create?subscribable_type=App\\Curriculum&subscribable_id=' + curriculum.id"
                        >
                            <i class="fa fa-book-open pr-2"></i>
                            {{trans('global.glossar.create')}}
                        </a>
                    </li>
                    <li v-if="(this.store.getSelectedIds('curriculum-user-datatable')?.length > 0) && course"
                        v-permission="'certificate_access'"
                        class="nav-item ml-auto"
                    >
                        <a
                            id="certificate-nav-tab"
                            class="nav-link link-muted"
                            @click.prevent="generateCertificate()"
                        >
                            <i class="fa fa-certificate pr-2"></i>
                            {{ trans('global.certificate.generate') }}
                        </a>
                    </li>
                    <li
                        v-permission="'certificate_create'"
                        class="nav-item ml-auto"
                    >
                        <a
                            id="certificate-nav-tab"
                            class="nav-link link-muted"
                            @click.prevent="createCertificate()"
                        >
                            <i class="fa fa-certificate pr-2"></i>
                            {{trans('global.certificate.create')}}
                        </a>
                    </li>
                    <li
                        v-permission="'curriculum_print'"
                        class="nav-item"
                    >
                        <a
                            id="config-nav-tab"
                            class="nav-link link-muted"
                            data-toggle="tooltip"
                            :title="trans('global.curriculum.print')"
                            @click="printCurriculum()"
                        >
                            <i class="fa fa-print"></i>
                        </a>
                    </li>
                    <li
                        class="nav-item"
                        @click="setGlobalStorage('#curriculum_'+curriculum.id, '#description-tab')"
                    >
                        <a
                            id="description-nav-tab"
                            class="nav-link link-muted"
                            data-toggle="pill"
                            href="#description-tab"
                        >
                            <i class="fa fa-info"></i>
                        </a>
                    </li>
                    <li
                        v-permission="'curriculum_edit'"
                        class="nav-item"
                    >
                        <a
                            id="fix-order-nav-tab"
                            class="nav-link link-muted"
                            data-toggle="tooltip"
                            title="Fix order_ids"
                            @click="resetOrderIds('/curricula/' + curriculum.id + '/resetOrderIds')"
                        >
                            <i class="fa fa-wrench"></i>
                        </a>
                    </li>
                    <li
                        v-permission="'curriculum_create'"
                        data-toggle="tooltip"
                        title="Export curriculum"
                        class="nav-item"
                    >
                        <a
                            id="export-curriculum-nav-tab"
                            class="nav-link link-muted"
                            @click="exportCurriculum()"
                        >
                            <i class="fas fa-cloud-download-alt"></i>
                        </a>
                    </li>
                </ul>

                <div
                    id="custom-content-below-tabContent"
                    class="tab-content"
                >
                    <div
                        id="curriculm-tab"
                        class="tab-pane fade show active"
                        role="tabpanel"
                        aria-labelledby="curriculm-nav-tab"
                    >
                        <TerminalObjectives
                            ref="terminalObjectives"
                            :curriculum="curriculum"
                            :objectivetypes="objectivetypes"
                            :settings="settings"
                        />
                    </div>
                    <div
                        id="content-tab"
                        class="tab-pane fade"
                        role="tab"
                        aria-labelledby="content-nav-tab"
                    >
                        <contents
                            ref="Contents"
                            subscribable_type="App\Curriculum"
                            :subscribable_id="curriculum.id"
                        />
                    </div>
                    <div
                        id="medium-tab"
                        class="tab-pane fade"
                        role="tab"
                        aria-labelledby="medium-nav-tab"
                    >
                        <media
                            subscribable_type="App\Curriculum"
                            :subscribable_id="curriculum.id"
                            :public="1"
                            format="list"
                        />
                    </div>
                    <div v-if="curriculum.glossar != null"
                        id="glossar-tab"
                        class="tab-pane fade"
                        role="tab"
                        aria-labelledby="glossar-nav-tab"
                    >
                        <glossars :glossar="curriculum.glossar"/>
                    </div>
                    <div
                        id="description-tab"
                        class="tab-pane fade"
                        role="tab"
                        aria-labelledby="description-nav-tab"
                    >
                        <div
                            class="card p-3"
                            v-dompurify-html="currentCurriculum.description"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <MediumModal/>
            <MediumExportModal/>
            <CurriculumModal/>
            <ContentModal/>
            <CertificateModal/>
            <GenerateCertificateModal/>
            <SubscribeModal/>
        </Teleport>

        <Teleport to="#customTitle">
            <small>{{ currentCurriculum.title }}</small>
            <a v-if="curriculum.owner_id == $userId || checkPermission('is_admin')"
                v-permission="'curriculum_edit'"
                class="btn btn-flat text-secondary px-2 mx-1"
                @click="edit()"
            >
                <i class="fa fa-pencil-alt"></i>
            </a>

            <a v-if="curriculum.owner_id == $userId || checkPermission('is_admin')"
                class="btn btn-flat text-secondary px-2"
                @click="share()"
            >
                <i class="fa fa-share-alt"></i>
            </a>
        </Teleport>
    </div>
</template>
<script>
import CurriculumModal from "../curriculum/CurriculumModal.vue";
import Media from "../media/Media.vue";
import TerminalObjectives from '../objectives/TerminalObjectives.vue'
import Glossars from '../glossar/Glossars.vue';
import Contents from '../content/Contents.vue';
import SubscribeModal from "../subscription/SubscribeModal.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import 'datatables.net-select-bs5';
import {useDatatableStore} from "../../store/datatables";
import CertificateModal from "../certificate/CertificateModal.vue";
import GenerateCertificateModal from "../certificate/GenerateCertificateModal.vue";
import {useGlobalStore} from "../../store/global";
import ContentModal from "../content/ContentModal.vue";
import MediumModal from "../media/MediumModal.vue";
import MediumExportModal from "../media/MediumExportModal.vue";
DataTable.use(DataTablesCore);

export default {
    name: "Curriculum",
    components: {
        MediumExportModal,
        MediumModal,
        ContentModal,
        GenerateCertificateModal,
        CertificateModal,
        CurriculumModal,
        TerminalObjectives,
        Glossars,
        Contents,
        DataTable,
        Media,
        SubscribeModal,
    },
    props: {
        curriculum: {
            type: Object,
            default: null,
        },
        course: {
            type: Object,
            default: null,
        },
       /* usersCurricula: {
            default: null
        },
        current_curriculum_cross_reference_id: {
            default: null
        },*/
        objectivetypes: {
            type: Array,
            default: null,
        },
        settings: {
            type: Object,
            default: null,
        },
    },
    setup() {
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
            currentCurriculum: {},
            columns: [
                { title: window.trans.global.user.fields.username, data: 'username', searchable: true },
                { title: window.trans.global.lastname, data: 'lastname', searchable: true },
                { title: window.trans.global.firstname, data: 'firstname', searchable: true },
                { title: window.trans.global.role.title_singular, data: 'role', searchable: true },
                { title: window.trans.global.progress.title_singular,  data: 'progress' },
            ],
            options : this.$dtOptions,
            search: '',
        }
    },
    mounted() {
        this.currentCurriculum = this.curriculum;

        this.store.addToDatatables({
            datatable: 'curriculum-user-datatable',
            select: (this.store.getDatatable('curriculum-user-datatable')?.select) ? false : true,
            selectedItems: [],
        });
        const dt = $('#curriculum-user-datatable').DataTable();
        dt.on('select', function(e, dt, type, indexes) {
            let selection = dt.rows('.selected').data().toArray();
            this.store.setSelectedIds('curriculum-user-datatable', selection);

            this.$refs.terminalObjectives.externalEvent(this.store.getSelectedIds('curriculum-user-datatable'));
        }.bind(this));

        this.$eventHub.on('curriculum-updated', (updatedCurriculum) => {
            Object.assign(this.currentCurriculum, updatedCurriculum);
        });
    },
    methods: {
        createCertificate() {
            this.globalStore?.showModal('certificate-modal', {
                curriculum_id: this.curriculum.id
            });
        },
        edit() {
            this.globalStore?.showModal('curriculum-modal', this.curriculum);
        },
        loaderEvent: function() {
            this.$refs.Contents.loaderEvent();
        },
        setCrossReferenceCurriculumId: function(curriculum_id) { //can be called external
            this.settings.cross_reference_curriculum_id = curriculum_id;
        },
        generateCertificate() {
            this.globalStore?.showModal('generate-certificate-modal', {'curriculum_id': this.curriculum.id});
        },
        exportCurriculum() {
            this.globalStore?.showModal('medium-export-modal', {
                id: this.curriculum.id,
                url: '/curricula/' + this.curriculum.id + '/export',
                header: window.trans.global.curriculum.export,
            });
        },
        share() {
            this.globalStore?.showModal('subscribe-modal', {
                modelId: this.curriculum.id,
                modelUrl: 'curriculum',
                shareWithUsers: true,
                shareWithGroups: true,
                shareWithOrganizations: true,
                shareWithToken: true,
                canEditCheckbox: false,
            });
        },
    }
}
</script>
