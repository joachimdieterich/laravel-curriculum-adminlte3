<template>
    <div>
        <div v-if="Object.keys(course).length"
            v-permission="'achievement_access'"
        >
            <div
                id="user-datatable-wrapper"
                class="dataTablesWrapper"
            >
                <DataTable
                    id="curriculum-user-datatable"
                    :columns="columns"
                    :options="options"
                    :ajax="'/courses/list?course_id=' + course.id"
                    :search="search"
                    width="100%"
                    @select="updateAchievements"
                    @deselect="updateAchievements"
                />
            </div>
        </div>

        <hr class="clearfix">
        <div class="d-flex flex-column px-3 pb-3">
            <ul
                class="nav nav-tabs align-items-center"
                role="tablist"
                aria-label="Curriculum Tabs"
            >
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
                        {{ trans('global.description') }}
                    </button>
                </li>

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
                        @click="loaderEvent()"
                    >
                        <i class="fa fa-align-justify pe-2"></i>
                        {{ trans('global.content.index') }}
                    </button>
                </li>

                <li
                    class="nav-item"
                    role="presentation"
                >
                    <button
                        id="curriculum-nav-tab"
                        class="nav-link link-muted active"
                        type="button"
                        role="tab"
                        data-bs-toggle="tab"
                        data-bs-target="#curriculum-tab"
                        aria-controls="curriculum-tab"
                        aria-selected="true"
                    >
                        <i class="fas fa-th pe-2"></i>
                        {{ trans('global.objective_tab') }}
                    </button>
                </li>

                <li
                    class="nav-item"
                    role="presentation"
                >
                    <button
                        id="medium-nav-tab"
                        class="nav-link link-muted"
                        type="button"
                        role="tab"
                        data-bs-toggle="tab"
                        data-bs-target="#medium-tab"
                        aria-controls="medium-tab"
                        aria-selected="false"
                    >
                        <i class="fa fa-folder-open pe-2"></i>
                        {{ trans('global.medium.title') }}
                    </button>
                </li>

                <li
                    class="nav-item"
                    role="presentation"
                >
                    <button v-if="curriculum.glossar != null"
                        id="glossar-nav-tab"
                        class="nav-link link-muted"
                        type="button"
                        role="tab"
                        data-bs-toggle="tab"
                        data-bs-target="#glossar-tab"
                        aria-controls="glossar-tab"
                        aria-selected="false"
                    >
                        <i class="fa fa-book-open pe-2"></i>
                        {{ trans('global.glossar.title_singular') }}
                    </button>
                    <a v-else-if="checkPermission('glossar_create')"
                        id="glossar-nav-tab"
                        class="nav-link link-muted"
                        :href="'/glossar/create?subscribable_type=App\\Curriculum&subscribable_id=' + curriculum.id"
                    >
                        <i class="fa fa-book-open pe-2"></i>
                        {{ trans('global.glossar.create') }}
                    </a>
                </li>

                <li v-if="(store.getSelectedIds('curriculum-user-datatable')?.length > 0) && Object.keys(course).length"
                    v-permission="'certificate_access'"
                    class="nav-item ms-auto"
                >
                    <button
                        id="certificate-nav-tab"
                        class="nav-link link-muted"
                        type="button"
                        @click.prevent="generateCertificate()"
                    >
                        <i class="fa fa-certificate pe-2"></i>
                        {{ trans('global.certificate.generate') }}
                    </button>
                </li>

                <li v-if="checkPermission('certificate_create')"
                    class="nav-item ms-auto"
                >
                    <button
                        id="certificate-nav-tab"
                        class="nav-link link-muted"
                        type="button"
                        @click.prevent="createCertificate()"
                    >
                        <i class="fa fa-certificate pe-2"></i>
                        {{ trans('global.certificate.create') }}
                    </button>
                </li>

                <button
                    id="config-nav-tab"
                    class="d-print-none btn btn-icon text-secondary mx-2"
                    data-bs-toggle="tooltip"
                    :data-bs-title="trans('global.curriculum.print')"
                    @click="printCurriculum()"
                >
                    <i class="fa fa-print"></i>
                </button>

                <button v-if="checkPermission('is_admin') || $userId == curriculum.owner_id"
                    id="fix-order-nav-tab"
                    class="d-print-none btn btn-icon text-secondary mx-2"
                    data-bs-toggle="tooltip"
                    :data-bs-title="trans('global.resetOrder')"
                    @click="resetOrderIds()"
                >
                    <i class="fa fa-wrench"></i>
                </button>

                <button v-if="checkPermission('curriculum_create')"
                    id="export-curriculum-nav-tab"
                    class="d-print-none btn btn-icon text-secondary ms-2"
                    data-bs-toggle="tooltip"
                    data-bs-title="Export Curriculum"
                    @click="exportCurriculum()"
                >
                    <i class="fas fa-cloud-download-alt"></i>
                </button>
            </ul>

            <div
                id="custom-content-below-tabContent"
                class="tab-content"
            >
                <div
                    id="description-tab"
                    class="tab-pane fade bg-white border-top-0 rounded-bottom-2"
                    style="border: 1px solid var(--bs-border-color);"
                    role="tabpanel"
                    tabindex="0"
                    aria-labelledby="description-nav-tab"
                >
                    <div
                        class="p-3"
                        v-html="currentCurriculum.description"
                    ></div>
                </div>

                <div
                    id="content-tab"
                    class="tab-pane fade"
                    role="tabpanel"
                    tabindex="0"
                    aria-labelledby="content-nav-tab"
                >
                    <Contents
                        ref="Contents"
                        subscribable_type="App\Curriculum"
                        :subscribable_id="curriculum.id"
                    />
                </div>

                <div
                    id="curriculum-tab"
                    class="tab-pane fade show active"
                    role="tabpanel"
                    tabindex="0"
                    aria-labelledby="curriculm-nav-tab"
                >
                    <TerminalObjectives
                        ref="terminalObjectives"
                        :curriculum="curriculum"
                        :settings="settings"
                    />
                </div>

                <div
                    id="medium-tab"
                    class="tab-pane fade"
                    role="tabpanel"
                    tabindex="0"
                    aria-labelledby="medium-nav-tab"
                >
                    <Media
                        subscribable_type="App\Curriculum"
                        :subscribable_id="curriculum.id"
                        :public="true"
                        format="list"
                    />
                </div>

                <div v-if="curriculum.glossar != null"
                    id="glossar-tab"
                    class="tab-pane fade"
                    role="tabpanel"
                    tabindex="0"
                    aria-labelledby="glossar-nav-tab"
                >
                    <glossars :glossar="curriculum.glossar"/>
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
            <div class="d-flex align-items-center">
                <small v-text="currentCurriculum.title"></small>
                <button v-if="curriculum.owner_id == $userId || checkPermission('is_admin')"
                    v-permission="'curriculum_edit'"
                    type="button"
                    class="d-print-none btn btn-icon text-secondary mx-1"
                    data-bs-toggle="tooltip"
                    :data-bs-title="trans('global.curriculum.edit')"
                    @click="edit()"
                >
                    <i class="fa fa-pencil-alt"></i>
                </button>
    
                <button v-if="curriculum.owner_id == $userId || checkPermission('is_admin')"
                    type="button"
                    class="d-print-none btn btn-icon text-secondary mx-1"
                    data-bs-toggle="tooltip"
                    :data-bs-title="trans('global.share')"
                    @click="share()"
                >
                    <i class="fa fa-share-alt"></i>
                </button>
            </div>
        </Teleport>
        <Teleport to="#contributors">
            <contributors-list v-if="Object.values(currentContributors).length > 1"
                :contributors="currentContributors"
                :heading="true"
            />
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
import ContributorsList from "../uiElements/ContributorsList.vue";
import {useToast} from "vue-toastification";
DataTable.use(DataTablesCore);

export default {
    name: "Curriculum",
    components: {
        ContributorsList,
        MediumModal,
        MediumExportModal,
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
            default: {},
        },
        settings: {
            type: Object,
            default: null,
        },
    },
    setup() {
        const store = useDatatableStore();
        const globalStore = useGlobalStore();
        const toast = useToast();

        return {
            store,
            globalStore,
            toast,
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
            dt: null,
            currentContributors: {},
        }
    },
    mounted() {
        this.currentCurriculum = this.curriculum;

        this.enableTooltips();

        this.store.addToDatatables({
            datatable: 'curriculum-user-datatable',
            select: (this.store.getDatatable('curriculum-user-datatable')?.select) ? false : true,
            selectedItems: [],
        });
        this.dt = $('#curriculum-user-datatable').DataTable();

        this.$eventHub.on('curriculum-updated', (updatedCurriculum) => {
            Object.assign(this.currentCurriculum, updatedCurriculum);
        });

        this.startWebsocket();
    },
    unmounted() {
        this.stopWebsocket();
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
        generateCertificate() {
            this.globalStore?.showModal('generate-certificate-modal', {'curriculum_id': this.curriculum.id});
        },
        printCurriculum() {
            axios.get('/curricula/' + this.curriculum.id + '/print')
                .then(response => window.location.href = response.data.path);
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
        resetOrderIds() {
            axios.patch('/curricula/' + this.curriculum.id + '/resetOrderIds')
            .then(r => window.location.reload());
        },
        updateAchievements() {
            let selection = this.dt.rows('.selected').data().toArray();
            this.store.setSelectedIds('curriculum-user-datatable', selection);

            this.$refs.terminalObjectives.externalEvent(this.store.getSelectedIds('curriculum-user-datatable'));
        },
        startWebsocket() {
            if (this.settings.websocket === true) {
                this.$echo
                    .join('App.Curriculum.' + this.curriculum.id)
                    .here((users) => {
                        for(let user of users) {
                            this.currentContributors[user.id] = user;
                        }
                    })
                    .listen('.CurriculumUpdated', (payload) => {
                        this.$eventHub.emit('curriculum-updated', payload.model);
                    })
                    .joining((user) => {
                        this.currentContributors[user.id] = user;
                        this.toast.info(this.trans('global.websockets.contributor_joined') + ': ' + user.firstname + ' ' + user.lastname);
                    })
                    .leaving((user) => {
                        delete this.currentContributors[user.id];
                        this.toast.info(this.trans('global.websockets.contributor_left') + ': ' + user.firstname + ' ' + user.lastname);
                    });
            }
        },
        stopWebsocket() {
            if (this.settings.websocket === true) {
                this.$echo.leave('App.Kanban.' + this.kanban.id);
            }
        },
    }
}
</script>
