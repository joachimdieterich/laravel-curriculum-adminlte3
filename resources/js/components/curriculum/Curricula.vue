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
                        <i class="fas fa-th pr-2"></i>{{ trans('global.all') }} {{ trans('global.curriculum.title') }}
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
                <li v-can="'curriculum_create'"
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
                <li v-can="'curriculum_create'"
                    class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'shared_by_me' ? 'active' : ''"
                       id="custom-tabs-shared-by-me"
                       @click="setFilter('shared_by_me')"
                       data-toggle="pill"
                       role="tab"
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
                v-if="((this.filter == 'all' && typeof (this.subscribable_type) == 'undefined' && typeof(this.subscribable_id) == 'undefined')|| this.filter  == 'owner') "
                key="'CurriculumCreate'"
                modelName="Curriculum"
                url="/curricula"
                :create=true
                :createLabel="trans('global.curriculum.create')">
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

                <template
                    v-permission="'curriculum_edit, curriculum_delete'"
                    v-slot:dropdown>
                    <div class="dropdown-menu dropdown-menu-right"
                         style="z-index: 1050;"
                         x-placement="left-start">
                        <button :name="'curriculum-edit_' + curriculum.id"
                                class="dropdown-item text-secondary"
                                @click.prevent="editCurriculum(curriculum)">
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.curriculum.edit') }}
                        </button>
                        <button :name="'curriculum-set_owner_' + curriculum.id"
                                class="dropdown-item text-secondary"
                                @click.prevent="setOwner(curriculum)">
                            <i class="fa fa-user mr-2"></i>
                            {{ trans('global.curriculum.edit_owner') }}
                        </button>
                        <button :name="'curriculum-share_' + curriculum.id"
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
                width="100%"
                style="display:none; "
            ></DataTable>
        </div>

        <Teleport to="body">
            <CurriculumModal></CurriculumModal>
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
import IndexWidget from "../uiElements/IndexWidget";
import ConfirmModal from "../uiElements/ConfirmModal";

import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
DataTable.use(DataTablesCore);
import MediumModal from "../media/MediumModal";
import SubscribeModal from "../subscription/SubscribeModal";
import CurriculumModal from "./CurriculumModal";
import {useGlobalStore} from "../../store/global";

export default {
    props: {
        subscribable_type: '',
        subscribable_id: '',
    },
    setup () {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            curricula: [],
            subscriptions: {},
            search: '',
            showConfirm: false,
            url: '/curricula/list',
            errors: {},
            currentCurriculum: {},
            filter: 'all',
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
            ],
            options : this.$dtOptions,
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
            window.location = "/curricula/" + curriculum.id + "/editOwner";
        },
        shareCurriculum(curriculum){
            this.globalStore?.showModal('subscribe-modal', { 'modelId': curriculum.id, 'modelUrl': 'curriculum' , 'shareWithToken': true, 'canEditCheckbox': false});
        },
        loaderEvent(){
            if (typeof (this.subscribable_type) !== 'undefined' && typeof(this.subscribable_id) !== 'undefined'){
                this.url = '/curriculumSubscriptions?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id
            } else {
                this.url = '/curricula/list?filter=' + this.filter;
            }

            const dt = $('#curriculum-datatable').DataTable();
            dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.curricula = dt.rows({page: 'current'}).data().toArray();
                $('#curriculum-content').insertBefore('#curriculum-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                dt.search(filter).draw();
            });
        },
        setFilter(filter){
            this.filter = filter;
            this.loaderEvent();
        },
        destroy() {
            axios.delete('/curricula/' + this.currentCurriculum.id)
                .then(res => {
                    let index = this.curricula.indexOf(this.currentCurriculum);
                    this.curricula.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        update(curriculum) {
            const index = this.curricula.findIndex(
                c => c.id === curriculum.id
            );

            for (const [key, value] of Object.entries(curriculum)) {
                this.curricula[index][key] = value;
            }
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('curriculum-added', (curriculum) => {
            this.globalStore?.closeModal('curriculum-modal');
            this.curricula.push(curriculum); //todo -> use global widget to get add working
        });

        this.$eventHub.on('curriculum-updated', (curriculum) => {
            this.globalStore?.closeModal('curriculum-modal');
            this.loaderEvent();
            this.update(curriculum); //todo -> use global widget to get update working
        });

        this.$eventHub.on('createCurriculum', () => {
            this.globalStore?.showModal('curriculum-modal', {});
        });
    },

    components: {
        IndexWidget,
        MediumModal,
        DataTable,
        SubscribeModal,
        ConfirmModal,
        CurriculumModal
    },
}
</script>
