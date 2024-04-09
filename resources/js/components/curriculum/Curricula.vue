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
                <li v-can="'curriculum_create'"
                    class="nav-item">
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

        <table id="curriculum-datatable" style="display: none;"></table>
        <div id="curriculum-content" >
            <div v-for="curriculum in curricula"
                 v-if="(curriculum.title.toLowerCase().indexOf(search.toLowerCase()) !== -1)
                || search.length < 3"
                 :id="curriculum.id"
                 v-bind:value="curriculum.id"
                 class="box box-objective nav-item-box-image pointer my-1 "
                 style="min-width: 200px !important;"
                 :style="'border-bottom: 5px solid ' + curriculum.color"
            >
                <a :href="'/curricula/' + curriculum.id"
                   class="text-decoration-none text-black"
                >
                    <div v-if="curriculum.medium_id"
                         class="nav-item-box-image-size"
                         :style="{'background': 'url(/media/' + curriculum.medium_id + '?model=Curriculum&model_id=' + curriculum.id +') top center no-repeat', 'background-size': 'cover', }">
                        <div class="nav-item-box-image-size"
                             style="width: 100% !important;"
                             :style="{backgroundColor: curriculum.color + ' !important',  'opacity': '0.5'}">
                        </div>
                    </div>
                    <div v-else
                         class="nav-item-box-image-size text-center"
                         :style="{backgroundColor: curriculum.color + ' !important'}">
<!--                        <i class="fa fa-2x p-5 fa-video nav-item-text text-white"></i>-->
                    </div>
                    <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                   <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                       {{ curriculum.title }}
                   </h1>
                   <p class="text-muted small"
                      v-html="htmlToText(curriculum.description)">
                   </p>
                </span>
                    <div v-permission="'is_admin'"
                        style="position:absolute; top:100px; left: 0;"
                        class="badge-primary px-2">
                        {{ curriculum.owner.firstname }} {{ curriculum.owner.lastname }}
                    </div>
                    <div class="symbol"
                         :style="'color:' + $textcolor(curriculum.color) + '!important'"
                         style="position: absolute; width: 30px; height: 40px;">
                        <i v-if="curriculum.type_id === 1"
                           class="fas fa-globe pt-2"></i>
                        <i v-else-if="curriculum.type_id === 2"
                           class="fas fa-university pt-2"></i>
                        <i v-else-if="curriculum.type_id === 3"
                           class="fa fa-users pt-2"></i>
                        <i v-else
                           class="fa fa-user pt-2"></i>
                    </div>
                    <div v-if="$userId == curriculum.owner_id"
                         class="btn btn-flat pull-right"
                         :id="'curriculumDropdown_' + curriculum.id"
                         style="position:absolute; top:0; right: 0; background-color: transparent;"
                         data-toggle="dropdown"
                         aria-expanded="false"
                        >
                        <i class="fas fa-ellipsis-v"
                           :style="'color:' + $textcolor(curriculum.color)"></i>
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
                    </div>
                    <div v-else
                         v-permission="'is_admin'"
                         class="btn btn-flat pull-right"
                         :id="'curriculumDropdown_' + curriculum.id"
                         style="position:absolute; top:0; right: 0; background-color: transparent;"
                         data-toggle="dropdown"
                         aria-expanded="false"
                    >
                        <i class="fas fa-ellipsis-v"
                           :style="'color:' + $textcolor(curriculum.color)"></i>
                        <div class="dropdown-menu dropdown-menu-right"
                             style="z-index: 1050;"
                             x-placement="left-start">
                            <button :name="'curriculum-set_owner_' + curriculum.id"
                                    class="dropdown-item text-secondary"
                                    @click.prevent="setOwner(curriculum)">
                                <i class="fa fa-user mr-2"></i>
                                {{ trans('global.curriculum.edit_owner') }}
                            </button>
                        </div>
                    </div>

                </a>
            </div>
            <curriculum-index-add-widget
                v-if="((this.filter == 'all' && typeof (this.subscribable_type) == 'undefined' && typeof(this.subscribable_id) == 'undefined')|| this.filter  == 'owner') "
                v-can="'curriculum_create'"/>
        </div>
        <Modal
            :id="'curriculumModal'"
            css="danger"
            :title="trans('global.curriculum.delete')"
            :text="trans('global.curriculum.delete_helper')"
            :ok_label="trans('global.curriculum.delete')"
            v-on:ok="destroy()"
        />
    </div>
</template>

<script>
import CurriculumIndexAddWidget from "./CurriculumIndexAddWidget";
const Modal =
    () => import('./../uiElements/Modal');

export default {
    props: {
        subscribable_type: '',
        subscribable_id: '',
    },
    data() {
        return {
            curricula: [],
            subscriptions: {},
            search: '',
            url: '/curricula/list',
            errors: {},
            currentCurriculum: {},
            filter: 'all'
        }
    },
    methods: {
        confirmItemDelete(curriculum){
            $('#curriculumModal').modal('show');
            this.currentCurriculum = curriculum;
        },
        editCurriculum(curriculum){
            this.$eventHub.$emit('edit_curriculum', curriculum);
            //window.location = "/curricula/" + curriculum.id + "/edit";
        },
        setOwner(curriculum){
            window.location = "/curricula/" + curriculum.id + "/editOwner";
        },
        shareCurriculum(curriculum){
            this.$modal.show('subscribe-modal', { 'modelId': curriculum.id, 'modelUrl': 'curriculum' , 'shareWithToken': true, 'canEditCheckbox': false});
        },
        loaderEvent(){
            if (typeof (this.subscribable_type) !== 'undefined' && typeof(this.subscribable_id) !== 'undefined'){
                this.url = '/curriculumSubscriptions?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id
            } else {
                this.url = '/curricula/list?filter=' + this.filter
            }

            if ($.fn.dataTable.isDataTable( '#curriculum-datatable' )){
                $('#curriculum-datatable').DataTable().ajax.url(this.url).load();
            } else {
                const dtObject = $('#curriculum-datatable').DataTable({
                    ajax: this.url,
                    dom: 'tilpr',
                    pageLength: 50,
                    language: {
                        url: '/datatables/i18n/German.json',
                        paginate: {
                            "first":      '<i class="fa fa-angle-double-left"></id>',
                            "last":       '<i class="fa fa-angle-double-right"></id>',
                            "next":       '<i class="fa fa-angle-right"></id>',
                            "previous":   '<i class="fa fa-angle-left"></id>',
                        },
                    },
                    columns: [
                        { title: 'id', data: 'id' },
                        { title: 'title', data: 'title', searchable: true},
                    ],
                }).on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                    this.curricula = dtObject.rows({ page: 'current' }).data().toArray();
                    $('#curriculum-content').insertBefore('#curriculum-datatable');
                });
            }
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
    },
    mounted() {
        if (document.getElementById('searchbar') != null) {
            document.getElementById('searchbar').classList.remove('d-none');
        }

        const filters = ["all", "owner", "shared_with_me", "shared_by_me"];
        let url = new URL(window.location.href);
        let urlFilter = url.searchParams.get("filter");

        if (filters.includes(urlFilter)){
          this.filter = urlFilter
        }

        this.$eventHub.$on('filter', (filter) => {
            $('#curriculum-datatable').DataTable().search(filter).draw();
        });
        this.$eventHub.$on('curriculum-added', (curriculum) => {
            this.loaderEvent();//this.curricula.push(curriculum);
        });
        this.$eventHub.$on('curriculum-updated', (curriculum) => {
            //console.log(curriculum);
            const index = this.curricula.findIndex(
                vc => vc.id === curriculum.id
            );

            for (const [key, value] of Object.entries(curriculum)) {
                this.curricula[index][key] = value;
            }
        });
        this.loaderEvent()
    },

    components: {
        CurriculumIndexAddWidget,
        Modal
    },
}
</script>
<style>
#curriculum-datatable_wrapper {
    width: 100%;
    padding: 0px 15px;
}
</style>
<style scoped>
.nav-link:hover {
    cursor: default;
    user-select: none;
}

.nav-item:hover .nav-link:not(.active) {
    background-color: rgba(0, 0, 0, 0.1);
    cursor: pointer;
}
</style>
