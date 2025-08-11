<template>
    <div class="row">
        <div class="col-md-12 ">
            <ul v-if="!subscribable"
                class="nav nav-pills py-2"
                role="tablist"
            >
                <li class="nav-item pointer">
                    <a
                        id="kanban-filter-all"
                        class="nav-link "
                        :class="filter === 'all' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('all')"
                    >
                        <i class="fas fa-columns pr-2"></i>
                        {{ trans('global.all') }} {{ trans('global.kanban.title') }}
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
                    v-permission="'kanban_create'"
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
                        {{ trans('global.my') }} {{ trans('global.kanban.title') }}
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
                    v-permission="'kanban_create'"
                    class="nav-item pointer"
                >
                    <a
                        id="custom-tabs-shared-by-me"
                        class="nav-link"
                        :class="filter === 'shared_by_me' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('shared_by_me')"
                    >
                        <i class="fa fa-share-nodes pr-2"></i>
                        {{ trans('global.shared_by_me') }}
                    </a>
                </li>
            </ul>
        </div>

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

            <IndexWidget v-for="kanban in kanbans"
                :key="'kanbanIndex' + kanban.id"
                :model="kanban"
                modelName="Kanban"
                url="/kanbans"
                :showSubscribable="subscribable"
            >
                <template v-slot:itemIcon>
                    <i class="fa fa-2x fa-columns"></i>
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
                        <button v-if="kanban.owner_id == $userId || checkPermission('is_admin')"
                            v-permission="'kanban_edit'"
                            :name="'edit-kanban-' + kanban.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editKanban(kanban)"
                        >
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.kanban.edit') }}
                        </button>

                        <button v-if="kanban.owner_id == $userId || checkPermission('is_admin')"
                            :name="'kanban-share_' + kanban.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="shareKanban(kanban)"
                        >
                            <i class="fa fa-share-alt mr-2"></i>
                            {{ trans('global.kanban.share') }}
                        </button>

                        <button v-if="kanban.allow_copy"
                            :name="'copy-kanban-' + kanban.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="confirmKanbanCopy(kanban)"
                        >
                            <i class="fa fa-copy mr-2"></i>
                            {{ trans('global.kanban.copy') }}
                        </button>

                        <hr v-if="kanban.owner_id == $userId || checkPermission('is_admin')"
                            class="my-1"
                        />

                        <button v-if="kanban.owner_id == $userId || checkPermission('is_admin')"
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
                :options="options"
                :ajax="url"
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
                @close="() => {
                    this.showConfirm = false;
                }"
                @confirm="() => {
                    this.showConfirm = false;
                    this.destroy();
                }"
            />
            <ConfirmModal v-if="!subscribable"
                :showConfirm="showCopy"
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
            />
        </Teleport>
    </div>
</template>
<script>
import SubscribeModal from "../subscription/SubscribeModal.vue";
import SubscribeKanbanModal from "../kanban/SubscribeKanbanModal.vue";
import KanbanModal from "../kanban/KanbanModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import MediumModal from "../media/MediumModal.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import {useGlobalStore} from "../../store/global";
import {useToast} from "vue-toastification";
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
        subscribable_type: '',
        subscribable_id: '',
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
            kanbans: null,
            showConfirm: false,
            showCopy: false,
            url: this.subscribable ? '/kanbans/list?group_id=' + this.subscribable_id : '/kanbans/list',
            errors: {},
            currentKanban: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
                { title: 'description', data: 'description', searchable: true },
            ],
            options : this.$dtOptions,
            filter: 'all',
            dt: null,
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

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

        this.$eventHub.on('filter', (filter) => {
            this.dt.search(filter).draw();
        });
    },
    methods: {
        setFilter(filter) {
            this.filter = filter;
            this.url = '/kanbans/list?filter=' + this.filter;
            this.dt.ajax.url(this.url).load();
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
                this.kanbans = this.dt.rows({page: 'current'}).data().toArray();
                $('#kanban-content').insertBefore('#kanban-datatable-wrapper');
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
            axios.get('/kanbans/' + this.currentKanban.id + '/copy')
                .then(response => {
                    this.kanbans.push(response.data);
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
                        let index = this.kanbans.indexOf(this.currentKanban);
                        this.kanbans.splice(index, 1);
                        this.toast.success(response.data);
                    })
                    .catch(e => {
                        this.toast.error(trans('global.expel_error'));
                    });
            }  else {
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
    },
    components: {
        SubscribeModal,
        SubscribeKanbanModal,
        MediumModal,
        ConfirmModal,
        DataTable,
        KanbanModal,
        IndexWidget,
    },
}
</script>