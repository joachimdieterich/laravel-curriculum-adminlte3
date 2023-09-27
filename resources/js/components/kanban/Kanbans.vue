<template >
    <div class="row">
        <div class="col-md-12 py-2">
<!--            <div id="kanbans_filter" class="dataTables_filter">
                <label >
                    <input type="search"
                           class="form-control form-control-sm"
                           placeholder="Suchbegriff"
                           v-model="search">
                </label>
            </div>-->
            <ul class="nav nav-pills" role="tablist">
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
            <KanbanIndexWidget
                v-for="(kanban,index) in kanbans"
                :key="index+'_kanban_'+kanban.id"
                :kanban="kanban"
                :search="search.toLowerCase()"/>
            <KanbanIndexAddWidget
                v-can="'kanban_create'"/>
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
import KanbanIndexWidget from "./KanbanIndexWidget";
import KanbanIndexAddWidget from "./KanbanIndexAddWidget";

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
                filter: 'all',
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
            editKanban(kanban){
                this.$eventHub.$emit('edit_kanban', kanban);
               // window.location = "/kanbans/" + id + "/edit";
            },
            copy(){
                window.location = "/kanbans/" + this.tempId + "/copy";
            },
            loaderEvent(){
                if (typeof (this.subscribable_type) !== 'undefined' && typeof(this.subscribable_id) !== 'undefined'){
                    this.url = '/kanbanSubscriptions?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id
                }
                axios.get(this.url + '?filter=' + this.filter)
                    .then(response => {
                            if (typeof (this.subscribable_type) !== 'undefined' && typeof(this.subscribable_id) !== 'undefined'){
                                this.kanbans = response.data.data;
                            } else {
                                this.kanbans = response.data.data;
                            }
                    })
                    .catch(e => {
                        console.log(e.data.errors);
                    });
            },
            setFilter(filter){
                this.filter = filter;
                this.loaderEvent();
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
        created() {
            document.getElementById('searchbar').classList.remove('d-none');
        },
        mounted() {
            const filters = ["all", "owner", "shared_with_me", "shared_by_me"];

            let url = new URL(window.location.href);
            let urlFilter = url.searchParams.get("filter");

            if (filters.includes(urlFilter)){
                this.filter = urlFilter
            }

            this.$eventHub.$on('filter', (filter) => {
                this.search = filter;
            });
            this.loaderEvent();
            this.$eventHub.$on('kanban-updated', (kanban) => {
                this.loaderEvent();
            });
        },
        components: {
            KanbanIndexWidget,
            KanbanIndexAddWidget,
            Modal
        },
    }
</script>
