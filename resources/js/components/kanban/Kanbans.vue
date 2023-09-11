<template >
    <div class="row">
        <div class="col-md-12 py-2">
            <div id="kanbans_filter" class="dataTables_filter">
                <label >
                    <input type="search"
                           class="form-control form-control-sm"
                           placeholder="Suchbegriff"
                           v-model="search">
                </label>
            </div>
            <ul v-if="typeof (this.subscribable_type) == 'undefined' && typeof(this.subscribable_id) == 'undefined'"
                class="nav nav-pills" role="tablist">
                <li class="nav-item">
                    <a class="nav-link "
                       :class="filter === 'all' ? 'active' : ''"
                       id="kanban-filter-all"
                       @click="setFilter('all')"
                       data-toggle="pill"
                       role="tab"
                       >
                       <i class="fa fa-columns pr-2"></i>Alle Pinnwände
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'owner' ? 'active' : ''"
                       id="custom-filter-owner"
                       @click="setFilter('owner')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fa fa-user  pr-2"></i>Meine Pinnwände
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
                        <i class="fa fa-paper-plane pr-2"></i>Für mich freigegeben
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'shared_by_me' ? 'active' : ''"
                       id="custom-tabs-shared-by-me"
                       @click="setFilter('shared_by_me')"
                       data-toggle="pill"
                       role="tab"
                       >
                        <i class="fa fa-share-nodes  pr-2"></i>Von mir freigegeben
                    </a>
                </li>

            </ul>
        </div>

        <div class="col-md-12 py-2">
            <div v-for="kanban in kanbans"
                 v-if="(kanban.title.toLowerCase().indexOf(search.toLowerCase()) !== -1)
                        || search.length < 3"
                 :id="'kanban-' + kanban.id"
                 class="box box-objective nav-item-box-image pointer my-1"
                 style="min-width: 200px !important;"
                 :style="'border-bottom: 5px solid ' + kanban.color">
                <a :href="'/kanbans/'+kanban.id"
                   class="text-decoration-none text-black"
                   >
                    <div v-if="kanban.medium_id"
                         class="nav-item-box-image-size"
                         :style="{'background': 'url(/media/' + kanban.medium_id + '?model=Kanban&model_id=' + kanban.id +') top center no-repeat', 'background-size': 'cover', }">
                         <div class="nav-item-box-image-size"
                              style="width: 100% !important;"
                              :style="{backgroundColor: kanban.color + ' !important',  'opacity': '0.5'}">
                         </div>
                    </div>
                    <div v-else
                         class="nav-item-box-image-size text-center"
                         :style="{backgroundColor: kanban.color + ' !important'}">
                        <i class="fa fa-2x p-5 fa-columns nav-item-text text-white"></i>
                    </div>

                    <div class="bg-white text-center p-1 overflow-auto nav-item-box">
                       <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                           {{ kanban.title }}
                       </h1>
                       <p class="text-muted small"
                          v-html="decodeHtml(kanban.description)">
                       </p>
                    </div>

                    <div class="symbol"
                         :style="'color:' + $textcolor(kanban.color) + '!important'"
                         style="position: absolute; width: 30px; height: 40px;"
                    >
                        <i v-if="$userId == kanban.owner_id"
                           class="fa fa-user pt-2"></i>
                        <i v-else
                           class="fa fa-share-nodes pt-2"></i>
                    </div>

                    <div v-if="$userId == kanban.owner_id"
                         class="btn btn-flat pull-right "
                         :id="'kanbanDropdown_' + kanban.id"
                         style="position:absolute; top:0; right: 0; background-color: transparent;"
                         data-toggle="dropdown"
                         aria-expanded="false">
                        <i class="fas fa-ellipsis-v"
                           :style="'color:' + $textcolor(kanban.color)"></i>
                        <div class="dropdown-menu dropdown-menu-right"
                             x-placement="left-start">
                            <button :name="'kanbanEdit_'+kanban.id"
                                    class="dropdown-item text-secondary"
                                    @click.prevent="editKanban(kanban.id)">
                                    <i class="fa fa-pencil-alt mr-2"></i>
                                {{ trans('global.kanban.edit') }}
                            </button>
                            <button
                                v-if="kanban.allow_copy"
                                :id="'copy-kanban-'+kanban.id"
                                type="submit"
                                class="dropdown-item text-secondary py-1"
                                @click.prevent="confirmKanbanCopy(kanban.id)">
                                <i class="fa fa-copy mr-2"></i>
                                {{ trans('global.kanban.copy') }}
                            </button>
                            <hr class="my-1">
                            <button
                                :id="'delete-kanban-'+kanban.id"
                                type="submit"
                                class="dropdown-item py-1 text-red"
                                @click.prevent="confirmItemDelete(kanban.id)">
                                <i class="fa fa-trash mr-2"></i>
                                {{ trans('global.kanban.delete') }}
                            </button>
                        </div>
                    </div>

                </a>

            </div>
        </div>
        <Modal
            :id="'kanbanModal'"
            css="danger"
            :title="trans('global.kanban.delete')"
            :text="trans('global.kanban.delete_helper')"
            :ok_label="trans('global.kanban.delete')"
            v-on:ok="destroy()"
        />
        <Modal
            :id="'kanbanCopyModal'"
            css="primary"
            :title="trans('global.kanban.copy')"
            :text="trans('global.kanban.copy_helper')"
            ok_label="OK"
            v-on:ok="copy()"
        />

    </div>
</template>

<script>
const Modal =
    () => import('./../uiElements/Modal');
//import Modal from "./../uiElements/Modal";

    export default {
        props: {
            subscribable_type: '',
            subscribable_id: '',
              },
        data() {
            return {
                kanbans: [],
                subscriptions: {},
                search: '',
                url: 'kanbans/list',
                errors: {},
                tempId: Number,
                filter: 'all'
            }
        },
        methods: {
            confirmItemDelete(kanbanId){
                $('#kanbanModal').modal('show');
                this.tempId = kanbanId;
            },
            confirmKanbanCopy(kanbanId){
                $('#kanbanCopyModal').modal('show');
                this.tempId = kanbanId;
            },
            editKanban(id){
                window.location = "/kanbans/" + id + "/edit";
            },
            copy(){
                window.location = "/kanbans/" + this.tempId + "/copy";
            },
            loaderEvent(){
                if (typeof (this.subscribable_type) !== 'undefined' && typeof(this.subscribable_id) !== 'undefined'){
                    this.url = '/kanbanSubscriptions?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id
                } else {
                    this.url =  'kanbans/list?filter=' + this.filter
                }
                axios.get(this.url)
                    .then(response => {
                        if (typeof (this.subscribable_type) !== 'undefined' && typeof(this.subscribable_id) !== 'undefined'){
                            this.kanbans = response.data.data;
                        } else {
                            this.kanbans = response.data.data;
                        }
                    })
                    .catch(e => {
                        console.log(e);
                    });
            },
            setFilter(filter){
                this.filter = filter;
                this.loaderEvent();
            },
            decodeHtml(html) {
                let txt = document.createElement("textarea");
                txt.innerHTML = html;
                return txt.value.replace(/(<([^>]+)>)/ig,"");
            },
            async destroy() {
                try {
                    this.kanbans = (await axios.delete('/kanbans/' + this.tempId)).data.data;
                } catch (error) {
                    console.log(error);
                }
                window.location = "/kanbans";
            },
        },

        mounted() {
            this.loaderEvent();
        },
        components: {
            Modal
        },
    }
</script>
