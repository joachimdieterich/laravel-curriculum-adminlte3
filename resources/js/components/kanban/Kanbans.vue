<template >
    <div class="row">
        <div class="col-md-12 ">
            <ul v-if="typeof (this.subscribable_type) == 'undefined' && typeof(this.subscribable_id) == 'undefined'"
                class="nav nav-pills py-2" role="tablist">
                <li class="nav-item">
                    <a class="nav-link "
                       :class="filter === 'all' ? 'active' : ''"
                       id="kanban-filter-all"
                       @click="setFilter('all')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fas fa-columns pr-2"></i>{{ trans('global.all') }} {{ trans('global.kanban.title') }}
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
                <li v-can="'kanban_create'"
                    class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'owner' ? 'active' : ''"
                       id="custom-filter-owner"
                       @click="setFilter('owner')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fa fa-user pr-2"></i>{{ trans('global.my') }} {{ trans('global.kanban.title') }}
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
                <li v-can="'kanban_create'"
                    class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'shared_by_me' ? 'active' : ''"
                       id="custom-tabs-shared-by-me"
                       @click="setFilter('shared_by_me')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fa fa-share-nodes pr-2"></i>{{ trans('global.shared_by_me') }}
                    </a>
                </li>
            </ul>
        </div>

        <div id="kanban-content"
             class="col-md-12 m-0">
            <IndexWidget
                v-permission="'kanban_create'"
                key="kanbanCreate"
                modelName="Kanban"
                url="/kanbans"
                :create=true
                :createLabel="trans('global.kanban.' + create_label_field)">
                <template v-slot:itemIcon>
                    <i v-if="create_label_field == 'enrol'"
                       class="fa fa-2x fa-link text-muted"
                    ></i>
                </template>
            </IndexWidget>
            <IndexWidget
                v-for="kanban in kanbans"
                :key="'kanbanIndex'+kanban.id"
                :model="kanban"
                modelName= "kanban"
                url="/kanbans">
                <template v-slot:itemIcon>
                    <i class="fa fa-2x fa-columns"></i>
                </template>

                <template
                    v-permission="'kanban_edit, kanban_delete'"
                    v-slot:dropdown>
                    <div class="dropdown-menu dropdown-menu-right"
                         style="z-index: 1050;"
                         x-placement="left-start">
                        <button
                            v-if="!subscribable"
                            v-permission="'kanban_edit'"
                            :name="'edit-kanban-' + kanban.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editKanban(kanban)">
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.kanban.edit') }}
                        </button>
                        <button
                            v-if="kanban.allow_copy && !subscribable"
                            :name="'copy-kanban-'+kanban.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="confirmKanbanCopy(kanban)">
                            <i class="fa fa-copy mr-2"></i>
                            {{ trans('global.kanban.copy') }}
                        </button>
                        <hr v-if="!subscribable"
                            class="my-1">
                        <button
                            v-permission="'kanban_delete'"
                            :id="'delete-kanban-' + kanban.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(kanban)">
                             <span v-if="create_label_field == 'enrol'">
                                 <i class="fa fa-unlink mr-2"></i>
                                {{ trans('global.kanban.expel') }}
                            </span>
                            <span v-else>
                                 <i class="fa fa-trash mr-2"></i>
                                {{ trans('global.kanban.delete') }}
                            </span>
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div id="kanban-datatable-wrapper"
             class="w-100 dataTablesWrapper">
            <DataTable
                id="kanban-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display:none; "
            ></DataTable>
        </div>

        <Teleport to="body">
            <SubscribeKanbanModal
                v-if="subscribable"
            >
            </SubscribeKanbanModal>
            <KanbanModal v-if="!subscribable"></KanbanModal>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.kanban.' + delete_label_field)"
                :description="trans('global.kanban.' + delete_label_field +'_helper')"
                @close="() => {
                    this.showConfirm = false;
                }"
                @confirm="() => {
                    this.showConfirm = false;
                    this.destroy();
                }"
            ></ConfirmModal>
            <ConfirmModal
                :showConfirm="this.showCopy"
                :title="trans('global.kanban.copy')"
                :description="trans('global.kanban.copy_helper')"
                css='primary'
                @close="() => {
                    this.showCopy = false;
                }"
                @confirm="() => {
                    this.showCopy = false;
                    this.copy();
                }"
            ></ConfirmModal>
        </Teleport>
    </div>
</template>


<script>
import SubscribeKanbanModal from "../kanban/SubscribeKanbanModal.vue";
import KanbanModal from "../kanban/KanbanModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import {useGlobalStore} from "../../store/global";
DataTable.use(DataTablesCore);

export default {
    props: {
        subscribable: {
            type: Boolean,
            default: false
        },
        create_label_field: {
            type: String,
            default: 'create'
        },
        delete_label_field: {
            type: String,
            default: 'delete'
        },
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
            component_id: this.$.uid,
            kanbans: null,
            search: '',
            showConfirm: false,
            showCopy: false,
            url: (this.subscribable_id) ? '/kanbans/list?group_id=' + this.subscribable_id : '/kanbans/list',

            errors: {},
            currentKanban: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
            ],
            options : this.$dtOptions,
            filter: 'all',
            dt: null
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('kanban-added', (kanban) => {
            if (!this.subscribable) {
                this.globalStore?.closeModal('kanban-modal');
            } else {
                this.globalStore?.closeModal('subscribe-kanban-modal');
            }

            this.kanbans.push(kanban);
        });

        this.$eventHub.on('kanban-updated', (kanban) => {
            this.globalStore?.closeModal('kanban-modal');
            this.update(kanban);
        });

        this.$eventHub.on('kanban-subscription-added', () => {
            this.globalStore?.closeModal('subscribe-kanban-modal');
            this.loaderEvent();
        });

        this.$eventHub.on('createKanban', () => {
            if (!this.subscribable) {
                this.globalStore?.showModal('kanban-modal', {});
            } else {
                this.globalStore?.showModal('subscribe-kanban-modal', {
                    'reference': {},
                    'subscribable_type': this.subscribable_type,
                    'subscribable_id': this.subscribable_id,
                });
            }
        });
    },
    methods: {
        setFilter(filter) {
            this.filter = filter;
            if (typeof (this.subscribable_type) !== 'undefined' && typeof(this.subscribable_id) !== 'undefined'){
                this.url = '/kanbanSubscriptions?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id
            } else {
                this.url = '/kanbans/list?filter=' + this.filter
            }

            this.dt.ajax.url(this.url).load();
        },
        editKanban(kanban) {
            this.globalStore?.showModal('kanban-modal', kanban);
        },
        loaderEvent() {
            this.dt = $('#kanban-datatable').DataTable();

            this.dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the kanban-data
                this.kanbans = this.dt.rows({page: 'current'}).data().toArray();

                $('#kanban-content').insertBefore('#kanban-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                this.dt.search(filter).draw();
            });
        },
        confirmItemDelete(kanban) {
            this.currentKanban = kanban;
            this.showConfirm = true;
        },
        confirmKanbanCopy(kanban) {
            this.currentKanban = kanban;
            this.showCopy = true;
        },
        copy() {
            window.location = "/kanbans/" + this.currentKanban.id + "/copy";
        },
        destroy() {
            if (this.subscribable === true)
            {
                axios.post('/kanbanSubscriptions/expel', {
                    'model_id' : this.currentKanban.id,
                    'subscribable_type' : this.subscribable_type,
                    'subscribable_id' : this.subscribable_id,
                })
                    .then(r => {
                        let index = this.kanbans.indexOf(this.currentKanban);
                        this.kanbans.splice(index, 1);
                    })
                    .catch(e => {
                        console.log(e);
                    });
            }
            else
            {
                axios.delete('/kanbans/' + this.currentKanban.id)
                    .then(res => {
                        let index = this.kanbans.indexOf(this.currentKanban);
                        this.kanbans.splice(index, 1);
                    })
                    .catch(err => {
                        console.log(err.response);
                    });
            }
        },
        update(kanban) {
            const index = this.kanbans.findIndex(
                vc => vc.id === kanban.id
            );

            for (const [key, value] of Object.entries(kanban)) {
                this.kanbans[index][key] = value;
            }
        }
    },
    components: {
        SubscribeKanbanModal,
        ConfirmModal,
        DataTable,
        KanbanModal,
        IndexWidget
    },
}
</script>
