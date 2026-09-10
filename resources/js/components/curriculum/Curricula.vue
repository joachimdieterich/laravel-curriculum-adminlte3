<template>
    <div class="d-flex flex-column">
        <TabList
            class="px-3"
            :model="'curriculum'"
            modelIcon="fa-th"
            :tabs="['favourite', 'all', 'by_organization', 'owner', 'shared_with_me', 'shared_by_me', 'hidden']"
            :activeTab="filter"
            @change-tab="setFilter"
        />

        <div
            id="curriculum-content"
            class="px-3"
        >
            <IndexWidget v-if="checkPermission('curriculum_create')
                    && (filter === 'all' || filter  === 'owner' || filter === 'favourite')"
                key="curriculumCreate"
                modelName="Curriculum"
                url="/curricula"
                :create=true
                :label="trans('global.curriculum.create')"
            />

            <IndexWidget v-for="(curriculum, index) in curricula"
                :id="curriculum.id"
                :key="'curriculumIndex' + curriculum.id"
                :model="curriculum"
                modelName="Curriculum"
                url="/curricula"
                :hidable="true"
            >
                <template v-slot:icon>
                    <i v-if="curriculum.type_id === 1"
                       class="fas fa-globe"
                    ></i>
                    <i v-else-if="curriculum.type_id === 2"
                       class="fas fa-university"
                    ></i>
                    <i v-else-if="curriculum.type_id === 3"
                       class="fa fa-users"
                    ></i>
                    <i v-else
                       class="fa fa-user"
                    ></i>
                </template>

                <template v-if="checkPermission('is_admin')" v-slot:owner>
                    <div
                        class="owner-badge position-absolute bg-primary px-2"
                        style="top: 100px; left: -6px;"
                    >
                        {{ curriculum.owner.firstname }} {{ curriculum.owner.lastname }}
                    </div>
                </template>

                <template v-if="curriculum.archived"
                    v-slot:badges
                >
                    <span
                        class="btn btn-info btn-xs position-absolute"
                        style="bottom: 5px; right: 5px;"
                    >
                        <i class="fa fa-box-archive"></i>
                        {{ trans('global.curriculum.fields.archived') }}
                    </span>
                </template>

                <template v-slot:dropdown>
                    <div class="dropdown-menu dropdown-menu-end">
                        <button v-if="ownerOrAdmin(curriculum)"
                            v-permission="'curriculum_edit'"
                            type="button"
                            class="dropdown-item"
                            @click.prevent="editCurriculum(curriculum)"
                        >
                            <i class="fa fa-pencil-alt"></i>
                            {{ trans('global.curriculum.edit') }}
                        </button>

                        <button
                            v-permission="'tag_access'"
                            type="button"
                            class="dropdown-item"
                            @click.prevent="manageTags(curriculum)"
                        >
                            <i class="fa fa-tag"></i>
                            {{ trans('global.tag.title') }}
                        </button>

                        <button v-if="ownerOrAdmin(curriculum)"
                            type="button"
                            class="dropdown-item"
                            @click.prevent="setOwner(curriculum)"
                        >
                            <i class="fa fa-user"></i>
                            {{ trans('global.curriculum.edit_owner') }}
                        </button>

                        <button v-if="ownerOrAdmin(curriculum)"
                            type="button"
                            class="dropdown-item"
                            @click.prevent="shareCurriculum(curriculum)"
                        >
                            <i class="fa fa-share-alt"></i>
                            {{ trans('global.curriculum.share') }}
                        </button>
                        <Hide v-if="filter === 'shared_with_me' || filter === 'all' || filter === 'hidden'"
                            url="/curricula/[id]/hide"
                            :model="curriculum"
                            :is-hidden="curriculum.is_hidden"
                            @mark-status-changed="() => curriculum.splice(index, 1)"
                        />

                        <hr v-if="ownerOrAdmin(curriculum)" class="my-1">

                        <button v-if="ownerOrAdmin(curriculum)"
                            v-permission="'curriculum_delete'"
                            type="submit"
                            class="dropdown-item text-danger"
                            @click.prevent="confirmItemDelete(curriculum)"
                        >
                            <i class="fa fa-trash"></i>
                            {{ trans('global.curriculum.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <DataTable
            ref="datatable"
            :columns="columns"
            :options="dtOptions('/curricula/list')"
            class="d-none"
            @xhr="xhrEvent"
        />

        <Teleport to="body">
            <TagComponentModal event-prefix="curriculum" model-namespace="\App\Curriculum"/>
            <CurriculumModal/>
            <SubscribeModal/>
            <MediumModal/>
            <OwnerModal/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.curriculum.delete')"
                :description="trans('global.curriculum.delete_helper')"
                @close="showConfirm = false"
                @confirm="() => {
                    showConfirm = false;
                    destroy();
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
import OwnerModal from "../user/OwnerModal.vue";
import Favourite from "../tag/Favourite.vue";
import Hide from "../tag/Hide.vue";
import useTaggableDataTable from "../tag/useTaggableDataTable.js";
import TabList from "../uiElements/TabList.vue";
import TagComponentModal from "../tag/TagComponentModal.vue";
DataTable.use(DataTablesCore);

export default {
    setup() {
        const {selectedTags, selectedNegativeTags, dtOptions} = useTaggableDataTable();

        return { selectedTags, selectedNegativeTags, dtOptions }
    },
    data() {
        return {
            component_id: this.$.uid,
            curricula: [],
            subscriptions: {},
            showConfirm: false,
            currentCurriculum: {},
            columns: [
                {title: 'id', data: 'id'},
                {title: 'title', data: 'title', searchable: true},
                {title: 'description', data: 'description', searchable: true},
            ],
            filter: 'favourite',
            dt: null,
        }
    },
    methods: {
        manageTags(curriculum) {
            this.globalStore?.showModal('tag-component-modal', curriculum);
        },
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
        },
        shareCurriculum(curriculum) {
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
            this.dt.ajax.url('/curricula/list?filter=' + this.filter).load();
        },
        xhrEvent(e, settings, json) {
            // if user doesn't have any favourited objects, default to 'all'-tab
            if (json.draw === 1 && json.data.length === 0) {
                this.setFilter('all');
                return;
            }
            this.curricula = json.data;
        },
        ownerOrAdmin(curriculum) {
            return curriculum.owner_id == this.$userId || this.checkPermission('is_admin');
        },
        destroy() {
            axios.delete('/curricula/' + this.currentCurriculum.id)
                .then(res => {
                    let index = this.curricula.indexOf(this.currentCurriculum);
                    this.curricula.splice(index, 1);
                })
                .catch(e => {
                    this.toast.error(this.errorMessage(e));
                    console.log(e.response);
                });
        },
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;
        this.globalStore['searchTagModelContext'] = 'App\\Curriculum';

        this.dt = this.$refs.datatable.dt;

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
            this.selectedTags = filter.tags;
            this.selectedNegativeTags = filter.negativeTags;

            this.dt.search(filter.searchString).draw();
        });

        this.$eventHub.on('owner-updated', (owner) => {
            this.globalStore?.closeModal('owner-modal');
            this.loaderEvent();
        });
    },
    components: {
        TagComponentModal,
        TabList,
        Hide,
        Favourite,
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
<style>
.owner-badge {
    &::before {
        content: '';
        position: absolute;
        top: 100%;
        left: 0;
        border-top: 4px solid #1e40af;
        border-right: 3px solid #1e40af;
        border-bottom: 4px solid transparent;
        border-left: 3px solid transparent;
    }
    &::after {
        content: '';
        position: absolute;
        left: 100%;
        border: 0.75rem solid transparent;
        border-left-color: var(--bs-primary);
    }
}
</style>