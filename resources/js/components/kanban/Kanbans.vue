<template>
    <div class="row">
        <TabList
            class="px-3"
            :model="'kanban'"
            modelIcon="fa-columns"
            :tabs="['favourite', 'all', 'owner', 'shared_with_me', 'shared_by_me', 'hidden']"
            :activeTab="filter"
            @change-tab="setFilter"
        />

        <div
            id="kanban-content"
            class="col-md-12 m-0"
        >
            <IndexWidget
                v-permission="'kanban_create'"
                key="kanbanCreate"
                modelName="Kanban"
                url="/kanbans"
                :create="!subscribable"
                :subscribe="subscribable"
                :subscribable_id="subscribable_id"
                :subscribable_type="subscribable_type"
                :label="trans('global.kanban.' + create_label_field)"
            >
                <template v-slot:itemIcon>
                    <i v-if="create_label_field == 'enrol'"
                       class="fa fa-2x fa-link text-muted"
                    ></i>
                </template>
            </IndexWidget>

            <IndexWidget v-for="(kanban, index) in kanbans"
                :key="'kanbanIndex' + kanban.id"
                :model="kanban"
                modelName="Kanban"
                url="/kanbans"
                :showSubscribable="subscribable"
            >
                <template v-slot:itemIcon>
                    <i class="fa fa-2x fa-columns"></i>
                </template>

                <template v-slot:additional-button>
                    <favourite
                        url="/kanbans/[id]/favour"
                        :model="kanban"
                        :is-favourited="kanban.is_favourited"
                        @mark-status-changed="(newKanban) => {
                            kanbans[index] = newKanban;
                        }"
                    />
                </template>

                <template v-slot:dropdown>
                    <div v-if="subscribable"
                        class="dropdown-menu dropdown-menu-right"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            v-permission="'kanban_delete'"
                            :id="'delete-kanban-' + kanban.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(kanban)"
                        >
                            <span>
                                <i class="fa fa-unlink mr-2"></i>
                                {{ trans('global.kanban.expel') }}
                            </span>
                        </button>
                    </div>
                    <div v-else
                        class="dropdown-menu dropdown-menu-right"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <hide
                            url="/kanbans/[id]/hide"
                            :model="kanban"
                            :is-hidden="kanban.is_hidden"
                            @mark-status-changed="(newKanban) => {
                                kanbans[index] = newKanban;
                            }"
                        />

                        <button v-if="ownerOrAdmin(kanban)"
                            v-permission="'kanban_edit'"
                            :name="'edit-kanban-' + kanban.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editKanban(kanban)"
                        >
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.kanban.edit') }}
                        </button>

                        <button v-if="ownerOrAdmin(kanban)"
                            :name="'kanban-share_' + kanban.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="shareKanban(kanban)"
                        >
                            <i class="fa fa-share-alt mr-2"></i>
                            {{ trans('global.kanban.share') }}
                        </button>

                        <button v-if="ownerOrAdmin(kanban) || kanban.allow_copy"
                            :name="'copy-kanban-' + kanban.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="confirmKanbanCopy(kanban)"
                        >
                            <i class="fa fa-copy mr-2"></i>
                            {{ trans('global.kanban.copy') }}
                        </button>

                        <hr v-if="ownerOrAdmin(kanban)" class="my-1"/>

                        <button v-if="ownerOrAdmin(kanban)"
                            v-permission="'kanban_delete'"
                            :id="'delete-kanban-' + kanban.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(kanban)"
                        >
                            <span>
                                <i class="fa fa-trash mr-2"></i>
                                {{ trans('global.kanban.delete') }}
                            </span>
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <div
            id="kanban-datatable-wrapper"
            class="w-100 dataTablesWrapper"
        >
            <DataTable
                id="kanban-datatable"
                :columns="columns"
                :options="dtOptions(this.subscribable ? '/kanbans/list?group_id=' + this.subscribable_id : '/kanbans/list')"
                width="100%"
                style="display: none;"
            />
        </div>

        <Teleport to="body">
            <KanbanModal v-if="!subscribable"/>
            <MediumModal v-if="!subscribable"/>
            <SubscribeModal v-if="!subscribable"/>
            <SubscribeKanbanModal v-if="subscribable"/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.kanban.' + delete_label_field)"
                :description="trans('global.kanban.' + delete_label_field +'_helper')"
                @close="showConfirm = false"
                @confirm="destroy()"
            />
            <ConfirmModal v-if="!subscribable"
                :showConfirm="showCopy"
                :title="trans('global.kanban.copy')"
                :description="trans('global.kanban.copy_helper') + '</br>' + trans('global.kanban.copy_note')"
                css='primary'
                @close="showCopy = false"
                @confirm="copy()"
            />
        </Teleport>
    </div>
</template>
<script>
import SubscribeModal from "../subscription/SubscribeModal.vue";
import SubscribeKanbanModal from "../kanban/SubscribeKanbanModal.vue";
import KanbanModal from "../kanban/KanbanModal.vue";
import TabList from "../uiElements/TabList.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import MediumModal from "../media/MediumModal.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import {useGlobalStore} from "../../store/global";
import {useToast} from "vue-toastification";
import Hide from "../tag/Hide.vue";
import Favourite from "../tag/Favourite.vue";
import useTaggableDataTable from "../tag/useTaggableDataTable.js";
DataTable.use(DataTablesCore);

export default {
    props: {
        subscribable: {
            type: Boolean,
            default: false,
        },
        create_label_field: {
            type: String,
            default: 'create',
        },
        delete_label_field: {
            type: String,
            default: 'delete',
        },
        subscribable_type: {
            type: String,
            default: '',
        },
        subscribable_id: {
            type: Number,
            default: null,
        },
    },
    setup() {
        const {selectedTags, selectedNegativeTags, dtOptions} = useTaggableDataTable();
        const toast = useToast();
        const globalStore = useGlobalStore();

        return {
            selectedTags, selectedNegativeTags, dtOptions,
            globalStore,
            toast,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            kanbans: null,
            showConfirm: false,
            showCopy: false,
            errors: {},
            currentKanban: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
                { title: 'description', data: 'description', searchable: true },
                { title: 'tags', data: 'tags' }
            ],
            filter: 'favourite',
            dt: null,
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;
        this.globalStore['searchTagModelContext'] =  'App\\Kanban';

        this.loaderEvent();

        this.$eventHub.on('kanban-subscription-added', (kanbanSubscription) => {
            this.kanbans.push(kanbanSubscription.kanban);
        });

        this.$eventHub.on('kanban-added', (kanban) => {
            this.kanbans.push(kanban);
        });

        this.$eventHub.on('kanban-updated', (updatedKanban) => {
            let kanban = this.kanbans.find(k => k.id === updatedKanban.id);

            Object.assign(kanban, updatedKanban);
        });
    },
    methods: {
        setFilter(filter) {
            this.filter = filter;
            this.dt.ajax.url('/kanbans/list?filter=' + this.filter).load();
        },
        editKanban(kanban) {
            this.globalStore?.showModal('kanban-modal', kanban);
        },
        shareKanban(kanban) {
            this.globalStore?.showModal(
                'subscribe-modal',
                {
                    modelId: kanban.id,
                    modelUrl: 'kanban' ,
                    shareWithUsers: true,
                    shareWithGroups: true,
                    shareWithOrganizations: true,
                    shareWithToken: true,
                    canEditCheckbox: true,
                });
        },
        loaderEvent() {
            this.dt = $('#kanban-datatable').DataTable();

            this.dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the kanban-data
                let newFilter = this.dt.ajax.json().newFilter;
                if (newFilter) {
                    this.setFilter(newFilter);
                }

                this.kanbans = this.dt.rows({page: 'current'}).data().toArray();
                $('#kanban-content').insertBefore('#kanban-datatable-wrapper');
            });

            this.$eventHub.on('filter', (filter) => {
                this.selectedTags = filter.tags;
                this.selectedNegativeTags = filter.negativeTags;

                this.dt.search(filter.searchString).draw();
            });
        },
        ownerOrAdmin(kanban) {
            return kanban.owner_id == this.$userId || this.checkPermission('is_admin');
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
            axios.get('/kanbans/' + this.currentKanban.id + '/copy')
                .then(response => {
                    this.showCopy = false;
                    this.kanbans.push(response.data);
                })
                .catch(e => {
                    console.log(e);
                    this.showCopy = false;
                    this.toast.error(this.errorMessage(e));
                });
        },
        destroy() {
            if (this.subscribable) {
                axios.post('/kanbanSubscriptions/expel', {
                    model_id : this.currentKanban.id,
                    subscribable_type : this.subscribable_type,
                    subscribable_id : this.subscribable_id,
                })
                    .then(response => {
                        this.showConfirm = false;
                        let index = this.kanbans.indexOf(this.currentKanban);
                        this.kanbans.splice(index, 1);
                        this.toast.success(response.data);
                    })
                    .catch(e => {
                        console.log(e);
                        this.showConfirm = false;
                        this.toast.error(trans('global.expel_error'));
                    });
            }  else {
                axios.delete('/kanbans/' + this.currentKanban.id)
                    .then(() => {
                        this.showConfirm = false;
                        let index = this.kanbans.indexOf(this.currentKanban);
                        this.kanbans.splice(index, 1);
                    })
                    .catch(e => {
                        console.log(e);
                        this.showConfirm = false;
                        this.toast.error(this.errorMessage(e));
                    });
            }
        },
    },
    components: {
        Hide,
        Favourite,
        SubscribeModal,
        SubscribeKanbanModal,
        MediumModal,
        ConfirmModal,
        DataTable,
        KanbanModal,
        IndexWidget,
        TabList,
    },
}
</script>