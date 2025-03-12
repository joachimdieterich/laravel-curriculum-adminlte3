<template >
    <div class="row">
        <div class="col-md-12 ">
            <ul v-if="!this.subscribable_type && !this.subscribable_id"
                class="nav nav-pills py-2"
                role="tablist"
            >
                <li class="nav-item pointer">
                    <a
                        id="curriculum-filter-all"
                        class="nav-link "
                        :class="filter === 'all' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('all')"
                    >
                        <i class="fas fa-th pr-2"></i> 
                        {{ trans('global.all') }} {{ trans('global.curriculum.title') }}
                    </a>
                </li>
                <li class="nav-item pointer">
                    <a
                        id="custom-filter-by-organization"
                        class="nav-link"
                        :class="filter === 'by_organization' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('by_organization')"
                    >
                        <i class="fas fa-university pr-2"></i>
                        {{ trans('global.my') }} {{ trans('global.organization.title_singular') }}
                    </a>
                </li>
                <li
                    v-permission="'curriculum_create'"
                    class="nav-item pointer"
                >
                    <a
                        id="custom-filter-owner"
                        class="nav-link"
                        :class="filter === 'owner' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('owner')"
                    >
                        <i class="fa fa-user pr-2"></i>
                        {{ trans('global.my') }} {{ trans('global.curriculum.title') }}
                    </a>
                </li>
                <li class="nav-item pointer">
                    <a
                        id="custom-filter-shared-with-me"
                        class="nav-link"
                        :class="filter === 'shared_with_me' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('shared_with_me')"
                    >
                        <i class="fa fa-paper-plane pr-2"></i>
                        {{ trans('global.shared_with_me') }}
                    </a>
                </li>
                <li
                    v-permission="'curriculum_create'"
                    class="nav-item pointer"
                >
                    <a
                        id="custom-tabs-shared-by-me"
                        :class="filter === 'shared_by_me' ? 'active' : ''"
                        class="nav-link"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('shared_by_me')"
                    >
                        <i class="fa fa-share-nodes  pr-2"></i>
                        {{ trans('global.shared_by_me') }}
                    </a>
                </li>
            </ul>
        </div>

        <div
            id="curriculum-content"
            class="col-md-12 m-0"
        >
            <IndexWidget v-if="(filter === 'all' && !this.subscribable_type && !this.subscribable_id)
                    || filter  === 'owner'"
                v-permission="'curriculum_create'"
                key="curriculumCreate"
                modelName="Curriculum"
                url="/curricula"
                :create=true
                :label="trans('global.curriculum.create')"
            />

            <IndexWidget v-for="curriculum in curricula"
                :id="curriculum.id"
                :key="'curriculumIndex' + curriculum.id"
                :model="curriculum"
                modelName="Curriculum"
                url="/curricula"
            >
                <template v-slot:icon>
                    <i v-if="curriculum.type_id === 1"
                        class="fas fa-globe pt-2"
                    ></i>
                    <i v-else-if="curriculum.type_id === 2"
                        class="fas fa-university pt-2"
                    ></i>
                    <i v-else-if="curriculum.type_id === 3"
                        class="fa fa-users pt-2"
                    ></i>
                    <i v-else
                        class="fa fa-user pt-2"
                    ></i>
                </template>

                <template v-slot:owner>
                    <div
                        v-permission="'is_admin'"
                        class="badge-primary position-absolute px-2"
                        style="top: 100px; left: 0;"
                    >
                        {{ curriculum.owner.firstname }} {{ curriculum.owner.lastname }}
                    </div>
                </template>

                <template v-if="curriculum.archived"
                    v-slot:badges
                >
                    <p class="text-muted small">
                        <span
                            class="btn btn-info btn-xs position-absolute select-all pull-right mr-1"
                            style="bottom: 0; margin: 5px 40px 8px 0; width: max-content; right: 5px;"
                        >
                            <i class="fa fa-archive" aria-hidden="true"></i>
                            {{ trans('global.curriculum.fields.archived') }}
                       </span>
                    </p>
                </template>

                <template v-slot:dropdown
                    v-permission="'curriculum_edit, curriculum_delete'"
                >
                    <div
                        class="dropdown-menu dropdown-menu-right"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            :name="'curriculum-edit_' + curriculum.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editCurriculum(curriculum)"
                        >
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.curriculum.edit') }}
                        </button>
                        <button v-if="$userId == curriculum.owner_id"
                            :name="'curriculum-set_owner_' + curriculum.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="setOwner(curriculum)"
                        >
                            <i class="fa fa-user mr-2"></i>
                            {{ trans('global.curriculum.edit_owner') }}
                        </button>
                        <button
                            :name="'curriculum-share_' + curriculum.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="shareCurriculum(curriculum)"
                        >
                            <i class="fa fa-share-alt mr-2"></i>
                            {{ trans('global.curriculum.share') }}
                        </button>
                        <hr class="my-1">
                        <button
                            :id="'delete-curriculum-' + curriculum.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(curriculum)"
                        >
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.curriculum.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <div
            id="curriculum-datatable-wrapper"
            class="w-100 dataTablesWrapper"
        >
            <DataTable
                id="curriculum-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                width="100%"
                style="display: none;"
            />
        </div>

        <Teleport to="body">
            <CurriculumModal/>
            <SubscribeModal/>
            <MediumModal/>
            <OwnerModal/>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.curriculum.delete')"
                :description="trans('global.curriculum.delete_helper')"
                @close="() => {
                    this.showConfirm = false;
                }"
                @confirm="() => {
                    this.showConfirm = false;
                    this.destroy();
                }"
            />
        </Teleport>
    </div>
</template>
<script>
import IndexWidget from "../uiElements/IndexWidget.vue";
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import MediumModal from "../media/MediumModal.vue";
import SubscribeModal from "../subscription/SubscribeModal.vue";
import CurriculumModal from "./CurriculumModal.vue";
import {useGlobalStore} from "../../store/global";
import OwnerModal from "../user/OwnerModal.vue";
import {useToast} from "vue-toastification";
DataTable.use(DataTablesCore);

export default {
    props: {
        subscribable_type: {
            type: String,
            default: null,
        },
        subscribable_id: {
            type: Number,
            default: null,
        },
    },
    setup() {
        const toast = useToast();
        const globalStore = useGlobalStore();
        return {
            globalStore,
            toast,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            curricula: [],
            subscriptions: {},
            showConfirm: false,
            url: (this.subscribable_id) ? '/curriculumSubscriptions?subscribable_type=' + this.subscribable_type + '&subscribable_id=' + this.subscribable_id : '/curricula/list',
            errors: {},
            currentCurriculum: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
                { title: 'description', data: 'description', searchable: true },
                { title: 'medium_id', data: 'medium_id' },
            ],
            options : this.$dtOptions,
            filter: 'all',
            dt: null,
        }
    },
    methods: {
        confirmItemDelete(curriculum) {
            this.currentCurriculum = curriculum;
            this.showConfirm = true;
        },
        editCurriculum(curriculum) {
            this.globalStore?.showModal('curriculum-modal', curriculum);
        },
        setOwner(curriculum) {
            this.globalStore?.showModal('owner-modal', {
                model_id: curriculum.id,
                model: 'curriculum',
                model_url: 'curricula',
                owner_id: curriculum.owner_id,
            });
            //window.location = "/curricula/" + curriculum.id + "/editOwner";
        },
        shareCurriculum(curriculum){
            this.globalStore?.showModal(
                'subscribe-modal',
                {
                    modelId: curriculum.id,
                    modelUrl: 'curriculum',
                    shareWithUsers: true,
                    shareWithGroups: true,
                    shareWithOrganizations: true,
                    shareWithToken: true,
                    canEditCheckbox: false,
                }
            );
        },
        setFilter(filter) {
            this.filter = filter;
            if (this.subscribable_type && this.subscribable_id) {
                this.url = '/curriculumSubscriptions?subscribable_type=' + this.subscribable_type + '&subscribable_id=' + this.subscribable_id;
            } else {
                this.url = '/curricula/list?filter=' + this.filter;
            }
            this.dt.ajax.url(this.url).load();
        },
        loaderEvent() {
            this.dt = $('#curriculum-datatable').DataTable();

            this.dt.on('draw.dt', () => {
                this.curricula = this.dt.rows({page: 'current'}).data().toArray();
                $('#curriculum-content').insertBefore('#curriculum-datatable-wrapper');
            });
        },
        destroy() {
            if (this.subscribable) {
                axios.delete('/curriculumSubscriptions/expel', {
                    data: {
                        model_id : this.currentCurriculum.id,
                        subscribable_type : this.subscribable_type,
                        subscribable_id : this.subscribable_id,
                    }
                })
                    .then(r => {
                        this.toast.success(r)
                    })
                    .catch(e => {
                        this.toast.error(e)
                    });
            } else {
                axios.delete('/curricula/' + this.currentCurriculum.id)
                    .then(res => {
                        let index = this.curricula.indexOf(this.currentCurriculum);
                        this.curricula.splice(index, 1);
                    })
                    .catch(err => {
                        console.log(err.response);
                    });
            }
        },
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('curriculum-added', (curriculum) => {
            this.curricula.push(curriculum);
        });
        this.$eventHub.on('curriculum-imported', (curricula) => {
            this.globalStore?.closeModal('curriculum-modal');
            this.loaderEvent(); //todo -> use global widget to get add working
        });

        this.$eventHub.on('curriculum-updated', (updatedCurriculum) => {
            let cur = this.curricula.find(c => c.id === updatedCurriculum.id);

            Object.assign(cur, updatedCurriculum);
        });

        this.$eventHub.on('filter', (filter) => {
            this.dt.search(filter).draw();
        });

        this.$eventHub.on('owner-updated', (owner) => {
            this.globalStore?.closeModal('owner-modal');
            this.loaderEvent();
        });
    },
    components: {
        OwnerModal,
        IndexWidget,
        MediumModal,
        DataTable,
        SubscribeModal,
        ConfirmModal,
        CurriculumModal,
    },
}
</script>