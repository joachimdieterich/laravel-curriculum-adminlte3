<template>
    <div class="row">
        <div class="col-md-12 py-2">
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

            <table id="kanban-datatable" style="display: none;"></table>
            <div id="kanban-content">
                <div class="py-2">
                    <KanbanIndexWidget
                        v-for="(kanban,index) in kanbans"
                        :key="index+'_kanban_'+kanban.id"
                        :kanban="kanban"
                    />
                    <KanbanIndexAddWidget
                        v-if="((this.filter == 'all' && typeof (this.subscribable_type) == 'undefined' && typeof(this.subscribable_id) == 'undefined') || this.filter == 'owner')"
                        v-can="'kanban_create'"
                    />
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
        </div>
    </div>
</template>

<script>
import KanbanIndexWidget from "./KanbanIndexWidget";
import KanbanIndexAddWidget from "./KanbanIndexAddWidget";

const Modal = () => import('./../uiElements/Modal');
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
            url: '/kanbans/list',
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
        editKanban(kanban){
            this.$eventHub.emit('edit_kanban', kanban);
            // window.location = "/kanbans/" + id + "/edit";
        },
        copy(){
            window.location = "/kanbans/" + this.tempId + "/copy";
        },
        loaderEvent(){
            if (typeof (this.subscribable_type) !== 'undefined' && typeof(this.subscribable_id) !== 'undefined'){
                this.url = '/kanbanSubscriptions?subscribable_type=' + this.subscribable_type + '&subscribable_id=' + this.subscribable_id;
            } else {
                this.url = '/kanbans/list?filter=' + this.filter;
            }

            $('#kanban-datatable').DataTable().ajax.url(this.url).load();
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
    mounted() {
        this.$eventHub.emit('showSearchbar');

        const parent = this;

        let dt = $('#kanban-datatable').DataTable({
            dom: 'tilpr',
            columns: [ // only gets attributes used in this component
                { title: 'id', data: 'id', searchable: false },
                { title: 'title', data: 'title', searchable: true },
                { title: 'description', data: 'description', searchable: true },
                { title: 'color', data: 'color', searchable: false },
                { title: 'owner_id', data: 'owner_id', searchable: false },
                { title: 'medium_id', data: 'medium_id', searchable: false },
                { title: 'allow_copy', data: 'allow_copy', searchable: false },
                { title: 'commentable', data: 'commentable', searchable: false },
                { title: 'auto_refresh', data: 'auto_refresh', searchable: false },
                { title: 'only_edit_owned_items', data: 'only_edit_owned_items', searchable: false },
            ],
            //pageLength: 6, // TODO: maybe set per variable based on window-width (mobile/tablet etc.)
        }).on('draw.dt', () => {  // checks if the datatable-data changes, to update the kanban-data
            parent.kanbans = $('#kanban-datatable').DataTable().rows({ page: 'current' }).data().toArray();
            $('#kanban-content').insertBefore('#kanban-datatable');
        });

        this.loaderEvent();

        this.$eventHub.on('filter', (filter) => {
            dt.search(filter).draw();
        });
        this.$eventHub.on('kanban-updated', (kanban) => {
            const index = this.kanbans.findIndex(
                k => k.id === kanban.id
            );

            for (const [key, value] of Object.entries(kanban)) {
                this.kanbans[index][key] = value;
            }
        });
    },
    components: {
        KanbanIndexWidget,
        KanbanIndexAddWidget,
        Modal
    },
}
</script>
<style>
#kanban-datatable_wrapper { width: 100%; }

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
