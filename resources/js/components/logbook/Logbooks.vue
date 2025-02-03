<template>
    <div class="row">
        <div class="col-md-12 ">
            <ul v-if="!subscribable"
                class="nav nav-pills py-2"
                role="tablist"
            >
                <li class="nav-item pointer">
                    <a
                        id="logbook-filter-all"
                        class="nav-link"
                        :class="filter === 'all' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('all')"
                    >
                        <i class="fas fa-th pr-2"></i> 
                        {{ trans('global.all') }} {{ trans('global.logbook.title') }}
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
                    v-permission="'logbook_create'"
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
                        {{ trans('global.my') }} {{ trans('global.logbook.title') }}
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
                    v-permission="'logbook_create'"
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
                        <i class="fa fa-share-nodes  pr-2"></i>
                        {{ trans('global.shared_by_me') }}
                    </a>
                </li>
            </ul>
        </div>

        <div
            id="logbook-content"
            class="col-md-12 m-0"
        >
            <IndexWidget
                v-permission="'logbook_create'"
                key="logbookCreate"
                modelName="Logbook"
                url="/logbooks"
                :create=!subscribable
                :subscribe="subscribable"
                :subscribable_id="subscribable_id"
                :subscribable_type="subscribable_type"
                :label="trans('global.logbook.' + create_label_field)"
            >
                <template v-slot:itemIcon>
                    <i v-if="create_label_field == 'enrol'"
                        class="fa fa-2x fa-link text-muted"
                    ></i>
                </template>
            </IndexWidget>

            <IndexWidget v-for="logbook in logbooks"
                :id="logbook.id"
                :key="'logbookIndex'+logbook.id"
                :model="logbook"
                modelName="Logbook"
                url="/logbooks"
            >
                <template v-slot:icon>
                    <i v-if="logbook.type_id === 1"
                        class="fas fa-globe pt-2"
                    ></i>
                    <i v-else-if="logbook.type_id === 2"
                        class="fas fa-university pt-2"
                    ></i>
                    <i v-else-if="logbook.type_id === 3"
                        class="fa fa-users pt-2"
                    ></i>
                    <i v-else
                        class="fa fa-user pt-2"
                    ></i>
                </template>

                <template v-slot:itemIcon>
                    <i v-if="logbook.css_icon"
                        class="fa-2x"
                        :class="logbook.css_icon"
                    ></i>
                </template>

                <template v-slot:dropdown
                    v-permission="'logbook_edit, logbook_delete'"
                >
                    <div v-if="subscribable"
                        class="dropdown-menu dropdown-menu-right"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            :id="'delete-logbook-' + logbook.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(logbook)"
                        >
                            <i class="fa fa-unlink mr-2"></i>
                            {{ trans('global.logbook.expel') }}
                        </button>
                    </div>
                    <div v-else
                        class="dropdown-menu dropdown-menu-right"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            :name="'logbook-edit_' + logbook.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editLogbook(logbook)"
                        >
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.logbook.edit') }}
                        </button>
                        <button
                            :name="'logbook-share_' + logbook.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="shareLogbook(logbook)"
                        >
                            <i class="fa fa-share-alt mr-2"></i>
                            {{ trans('global.logbook.share') }}
                        </button>
                        <hr class="my-1">
                        <button
                            :id="'delete-logbook-' + logbook.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(logbook)"
                        >
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.logbook.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <div
            id="logbook-datatable-wrapper"
            class="w-100 dataTablesWrapper"
        >
            <DataTable
                id="logbook-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display: none;"
            />
        </div>

        <Teleport to="body">
            <LogbookModal v-if="!subscribable"/>
            <SubscribeModal v-if="!subscribable"/>
            <SubscribeLogbookModal v-if="subscribable"/>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.logbook.' + delete_label_field)"
                :description="trans('global.logbook.' + delete_label_field + '_helper')"
                @close="() => {
                    this.showConfirm = false;
                }"
                @confirm="() => {
                    this.showConfirm = false;
                    this.destroy();
                }"
            />
        </Teleport>
    </div>
</template>
<script>
import IndexWidget from "../uiElements/IndexWidget.vue";
import LogbookModal from "./LogbookModal.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import SubscribeLogbookModal from "./SubscribeLogbookModal.vue";
import {useGlobalStore} from "../../store/global";
import SubscribeModal from "../subscription/SubscribeModal.vue";
import {useToast} from "vue-toastification";
DataTable.use(DataTablesCore);

export default {
    props: {
        reference : Object,
        subscribable: {
            type: Boolean,
            default: false,
        },
        create_label_field: {
            type: String,
            default: 'enrol',
        },
        delete_label_field: {
            type: String,
            default: 'delete',
        },
        subscribable_type: {
            type: String,
            default: null,
        },
        subscribable_id: {
            type: Number,
            default: null,
        },
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
            logbooks: [],
            subscriptions: {},
            search: '',
            showConfirm: false,
            url: this.subscribable ? '/logbooks/list?group_id=' + this.subscribable_id : '/logbooks/list',
            errors: {},
            currentLogbook: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
                { title: 'description', data: 'description', searchable: true},
                { title: 'medium_id', data: 'medium_id'},
            ],
            options : this.$dtOptions,
            filter: 'all',
            dt: null,
        }
    },
    methods: {
        confirmItemDelete(logbook) {
            this.currentLogbook = logbook;
            this.showConfirm = true;
        },
        editLogbook(logbook) {
            this.globalStore?.showModal('logbook-modal', logbook);
        },
        shareLogbook(logbook) {
            this.globalStore?.showModal(
                'subscribe-modal',
                {
                    modelId: logbook.id,
                    modelUrl: 'logbook' ,
                    shareWithUsers: true,
                    shareWithGroups: true,
                    shareWithOrganizations: true,
                    shareWithToken: true,
                    canEditCheckbox: true
                });
        },
        setFilter(filter)  {
            this.filter = filter;
            this.url = '/logbooks/list?filter=' + this.filter;
            this.dt.ajax.url(this.url).load();
        },
        loaderEvent() {
            this.dt = $('#logbook-datatable').DataTable();
            this.dt.on('draw.dt', () => {
                this.logbooks = this.dt.rows({ page: 'current' }).data().toArray();

                $('#logbook-content').insertBefore('#logbook-datatable-wrapper');
            });
        },
        destroy() {
            if (this.subscribable) {
                axios.post('/logbookSubscriptions/expel', {
                    model_id : this.currentLogbook.id,
                    subscribable_type : this.subscribable_type,
                    subscribable_id : this.subscribable_id,
                })
                    .then(response => {
                        let index = this.logbooks.indexOf(this.currentLogbook);
                        this.logbooks.splice(index, 1);
                        this.toast.success(response.data);
                    })
                    .catch(e => {
                        this.toast.error(trans('global.expel_error'));
                    });
            } else {
                axios.delete('/logbooks/' + this.currentLogbook.id)
                    .then(() => {
                        let index = this.logbooks.indexOf(this.currentLogbook);
                        this.logbooks.splice(index, 1);
                    })
                    .catch ((e) => {
                        console.log(e);
                    });
            }
        },
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        if (this.subscribable) {
            this.$eventHub.on('logbook-subscription-added', (logbookSubscription) => {
                this.logbooks.push(logbookSubscription.logbook);
            });
        } else {
            this.$eventHub.on('logbook-added', (logbook) => {
                this.logbooks.push(logbook);
            });

            this.$eventHub.on('logbook-updated', (updatedLogbook) => {
                let logbook = this.logbooks.find(l => l.id === updatedLogbook.id);
                Object.assign(logbook, updatedLogbook);
            });
        }

        this.$eventHub.on('filter', (filter) => {
            this.dt.search(filter).draw();
        });
    },
    components: {
        SubscribeModal,
        ConfirmModal,
        SubscribeLogbookModal,
        DataTable,
        IndexWidget,
        LogbookModal,
    },
}
</script>