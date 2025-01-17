<template >
    <div class="row">
        <div class="col-md-12 ">
            <ul v-if="typeof (this.subscribable_type) == 'undefined' && typeof(this.subscribable_id) == 'undefined'"
                class="nav nav-pills py-2" role="tablist">
                <li class="nav-item">
                    <a class="nav-link "
                       :class="filter === 'all' ? 'active' : ''"
                       id="curriculum-filter-all"
                       @click="setFilter('all')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fas fa-th pr-2"></i>  {{ trans('global.all') }} {{ trans('global.curriculum.title') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'by_organization' ? 'active' : ''"
                       id="custom-filter-by-organization"
                       @click="setFilter('by_organization')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fas fa-university pr-2"></i>{{ trans('global.my') }} {{ trans('global.organization.title_singular') }}
                    </a>
                </li>
                <li v-permission="'curriculum_create'"
                    class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'owner' ? 'active' : ''"
                       id="custom-filter-owner"
                       @click="setFilter('owner')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fa fa-user pr-2"></i>{{ trans('global.my') }} {{ trans('global.curriculum.title') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'shared_with_me' ? 'active' : ''"
                       id="custom-filter-shared-with-me"
                       @click="setFilter('shared_with_me')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fa fa-paper-plane pr-2"></i>{{ trans('global.shared_with_me') }}
                    </a>
                </li>
                <li v-permission="'curriculum_create'"
                    class="nav-item">
                    <a id="custom-tabs-shared-by-me"
                       :class="filter === 'shared_by_me' ? 'active' : ''"
                       class="nav-link"
                       data-toggle="pill"
                       role="tab"
                       @click="setFilter('shared_by_me')"
                    >
                        <i class="fa fa-share-nodes  pr-2"></i>{{ trans('global.shared_by_me') }}
                    </a>
                </li>
            </ul>
        </div>

        <div id="curriculum-content"
             class="col-md-12 m-0">
            <IndexWidget
                v-permission="'curriculum_create'"
                v-if="((filter === 'all' && this.subscribable_type ===  null && this.subscribable_id ===  null) || filter  === 'owner')"
                key="curriculumCreate"
                modelName="Curriculum"
                url="/curricula"
                :create=true
                :label="trans('global.curriculum.create')">
            </IndexWidget>

            <IndexWidget
                v-for="curriculum in curricula"
                :id="curriculum.id"
                :key="'curriculumIndex'+curriculum.id"
                :model="curriculum"
                modelName= "curriculum"
                url="/curricula">
                <template v-slot:icon>
                    <i v-if="curriculum.type_id === 1"
                       class="fas fa-globe pt-2"></i>
                    <i v-else-if="curriculum.type_id === 2"
                       class="fas fa-university pt-2"></i>
                    <i v-else-if="curriculum.type_id === 3"
                       class="fa fa-users pt-2"></i>
                    <i v-else
                       class="fa fa-user pt-2"></i>
                </template>

                <template v-slot:owner>
                    <div v-permission="'is_admin'"
                         style="position:absolute; top:100px; left: 0;"
                         class="badge-primary px-2">
                        {{ curriculum.owner.firstname }} {{ curriculum.owner.lastname }}
                    </div>
                </template>

                <template v-if="curriculum.archived === true"
                    v-slot:badges>
                    <p class="text-muted small">
                           <span class="btn btn-info btn-xs select-all pull-right mr-1"
                                 style="position: absolute;bottom: 0;margin: 5px 40px 8px 0;width: max-content;right: 5px;">
                            <i class="fa fa-archive" aria-hidden="true"></i> {{ trans('global.curriculum.fields.archived') }}
                       </span>
                    </p>
                </template>

                <template
                    v-permission="'curriculum_edit, curriculum_delete'"
                    v-slot:dropdown>
                    <div class="dropdown-menu dropdown-menu-right"
                         style="z-index: 1050;"
                         x-placement="left-start">
                        <button
                            :name="'curriculum-edit_' + curriculum.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editCurriculum(curriculum)">
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.curriculum.edit') }}
                        </button>
                        <button
                            v-if="$userId == curriculum.owner_id"
                            :name="'curriculum-set_owner_' + curriculum.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="setOwner(curriculum)">
                            <i class="fa fa-user mr-2"></i>
                            {{ trans('global.curriculum.edit_owner') }}
                        </button>
                        <button
                            :name="'curriculum-share_' + curriculum.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="shareCurriculum(curriculum)">
                            <i class="fa fa-share-alt mr-2"></i>
                            {{ trans('global.curriculum.share') }}
                        </button>
                        <hr class="my-1">
                        <button
                            :id="'delete-curriculum-' + curriculum.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(curriculum)">
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.curriculum.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <div id="curriculum-datatable-wrapper"
             class="w-100 dataTablesWrapper">
            <DataTable
                id="curriculum-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display:none; "
            ></DataTable>
        </div>

        <Teleport to="body">
            <CurriculumModal></CurriculumModal>
            <OwnerModal></OwnerModal>
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
            ></ConfirmModal>
            <SubscribeModal></SubscribeModal>
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
import {nextTick} from "vue";
DataTable.use(DataTablesCore);

export default {
    props: {
        subscribable_type: null, // has to be null ! to get filter condition for all roles working
        subscribable_id: null,
    },
    setup () {
        const toast = useToast();
        const globalStore = useGlobalStore();
        return {
            globalStore,
            toast
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            curricula: [],
            subscriptions: {},
            search: '',
            showConfirm: false,
            url: (this.subscribable_id) ? '/curriculumSubscriptions?subscribable_type=' + this.subscribable_type + '&subscribable_id='+this.subscribable_id : '/curricula/list',
            errors: {},
            currentCurriculum: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
                { title: 'description', data: 'description', searchable: true},
                { title: 'medium_id', data: 'medium_id'},
            ],
            options : this.$dtOptions,
            filter: 'all',
            dt: null
        }
    },
    methods: {
        confirmItemDelete(curriculum){
            this.currentCurriculum = curriculum;
            this.showConfirm = true;
        },
        editCurriculum(curriculum){
            this.globalStore?.showModal('curriculum-modal', curriculum);
        },
        setOwner(curriculum){
            this.globalStore?.showModal('owner-modal', {
                'model_id': curriculum.id,
                'model': 'curriculum',
                'model_url': 'curricula',
                'owner_id': curriculum.owner_id,
            });
            //window.location = "/curricula/" + curriculum.id + "/editOwner";
        },
        shareCurriculum(curriculum){
            this.globalStore?.showModal(
                'subscribe-modal',
                {
                    'modelId': curriculum.id,
                    'modelUrl': 'curriculum',
                    'shareWithUsers': true,
                    'shareWithGroups': true,
                    'shareWithOrganizations': true,
                    'shareWithToken': true,
                    'canEditCheckbox': true
                });
        },
        setFilter(filter){
            this.filter = filter;
            if (typeof (this.subscribable_type) !== 'undefined' && typeof(this.subscribable_id) !== 'undefined'){
                this.url = '/curriculumSubscriptions?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id
            } else {
                this.url = '/curricula/list?filter=' + this.filter;
            }
            this.dt.ajax.url(this.url).load();
        },
        loaderEvent(){
            this.dt = $('#curriculum-datatable').DataTable();

            this.dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.curricula = this.dt.rows({page: 'current'}).data().toArray();
                $('#curriculum-content').insertBefore('#curriculum-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                this.dt.search(filter).draw();
            });
        },
        destroy() {
            if (this.subscribable){
                axios.delete('/curriculumSubscriptions/expel', {
                    data: {
                        'model_id' : this.currentCurriculum.id,
                        'subscribable_type' : this.subscribable_type,
                        'subscribable_id' : this.subscribable_id,
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
        update(curriculum) {
            const index = this.curricula.findIndex(
                c => c.id === curriculum.id
            );

            for (const [key, value] of Object.entries(curriculum)) {
                this.curricula[index][key] = value;
            }
        },
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('curriculum-added', (curriculum) => {
            this.globalStore?.closeModal('curriculum-modal');
            //this.curricula.push(curriculum); //todo -> use global widget to get add working
            this.loaderEvent();
        });
        this.$eventHub.on('curriculum-imported', (curricula) => {
            this.globalStore?.closeModal('curriculum-modal');
            this.loaderEvent(); //todo -> use global widget to get add working
        });

        this.$eventHub.on('curriculum-updated', (curriculum) => {
            this.globalStore?.closeModal('curriculum-modal');
            //this.update(curriculum); //todo -> use global widget to get update working
            console.log(curriculum);
            this.loaderEvent();
        });

        this.$eventHub.on('createCurriculum', () => {
            this.globalStore?.showModal('curriculum-modal', {});
        });

        this.$eventHub.on('filter', (filter) => {
            this.search = filter;
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
        CurriculumModal
    },
}
</script>
