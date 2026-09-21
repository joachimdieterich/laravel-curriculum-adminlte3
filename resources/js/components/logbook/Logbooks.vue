<template>
    <div class="d-flex flex-column">
        <ul v-if="!subscribable"
            class="nav nav-pills px-3 py-2"
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
                    <i class="fas fa-th pe-2"></i>
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
                    <i class="fas fa-university pe-2"></i>
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
                    <i class="fa fa-user pe-2"></i>
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
                    <i class="fa fa-paper-plane pe-2"></i>
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
                    <i class="fa fa-share-nodes  pe-2"></i>
                    {{ trans('global.shared_by_me') }}
                </a>
            </li>
        </ul>

        <div
            id="logbook-content"
            class="px-3"
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
                :label="trans('global.logbook.' + createLabel)"
            >
                <template #itemIcon>
                    <i v-if="subscribable" class="fa fa-2x fa-link text-muted"></i>
                </template>
            </IndexWidget>

            <IndexWidget v-for="logbook in logbooks"
                :id="logbook.id"
                :key="'logbookIndex' + logbook.id"
                :model="logbook"
                modelName="Logbook"
                url="/logbooks"
                :showSubscribable="subscribable"
            >
                <template #itemIcon>
                    <i v-if="logbook.css_icon"
                        class="fa-2x"
                        :class="logbook.css_icon"
                    ></i>
                </template>

                <template #dropdown>
                    <div v-if="subscribable"
                        class="dropdown-menu dropdown-menu-end"
                    >
                        <button
                            v-permission="'logbook_delete'"
                            type="submit"
                            class="dropdown-item text-danger"
                            @click="confirmItemDelete(logbook)"
                        >
                            <i class="fa fa-unlink"></i>
                            {{ trans('global.logbook.expel') }}
                        </button>
                    </div>
                    <div v-else
                        class="dropdown-menu dropdown-menu-end"
                    >
                        <button v-if="ownerOrAdmin(logbook)"
                            type="button"
                            class="dropdown-item"
                            @click="editLogbook(logbook)"
                        >
                            <i class="fa fa-pencil-alt"></i>
                            {{ trans('global.logbook.edit') }}
                        </button>
                        <button v-if="ownerOrAdmin(logbook)"
                            type="button"
                            class="dropdown-item"
                            @click="shareLogbook(logbook)"
                        >
                            <i class="fa fa-share-alt"></i>
                            {{ trans('global.logbook.share') }}
                        </button>

                        <hr v-if="ownerOrAdmin(logbook)" class="my-1">

                        <button v-if="ownerOrAdmin(logbook)"
                            v-permission="'logbook_delete'"
                            type="submit"
                            class="dropdown-item text-danger"
                            @click="confirmItemDelete(logbook)"
                        >
                            <i class="fa fa-trash"></i>
                            {{ trans('global.logbook.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <DataTable
            ref="datatable"
            :columns="columns"
            :options="options"
            :ajax="subscribable ? '/logbooks/list?group_id=' + subscribable_id : '/logbooks/list'"
            class="d-none"
            @xhr="(e, settings, json) => logbooks = json.data"
        />

        <Teleport to="body">
            <LogbookModal v-if="!subscribable"/>
            <MediumModal v-if="!subscribable"/>
            <SubscribeModal v-if="!subscribable"/>
            <SubscribeLogbookModal v-if="subscribable"/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.logbook.' + deleteLabel)"
                :description="trans('global.logbook.' + deleteLabel + '_helper')"
                @close="showConfirm = false"
                @confirm="() => {
                    showConfirm = false;
                    destroy();
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
import SubscribeModal from "../subscription/SubscribeModal.vue";
import MediumModal from "../media/MediumModal.vue";
DataTable.use(DataTablesCore);

export default {
    components: {
        SubscribeModal,
        MediumModal,
        ConfirmModal,
        SubscribeLogbookModal,
        DataTable,
        IndexWidget,
        LogbookModal,
    },
    props: {
        reference : Object,
        subscribable: {
            type: Boolean,
            default: false,
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
    data() {
        return {
            component_id: this.$.uid,
            logbooks: [],
            subscriptions: {},
            search: '',
            showConfirm: false,
            currentLogbook: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
                { title: 'description', data: 'description', searchable: true},
            ],
            options : this.$dtOptions,
            filter: 'all',
            dt: null,
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.dt = this.$refs.datatable.dt;

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

        this.$eventHub.on('filter', filter => {
            this.dt.search(filter.searchString).draw();
        });
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
                    shareWithToken: false,
                    canEditCheckbox: true,
                });
        },
        setFilter(filter) {
            this.filter = filter;
            this.dt.ajax.url('/logbooks/list?filter=' + this.filter).load();
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
        ownerOrAdmin(logbook) {
            return logbook.owner_id == this.$userId || this.checkPermission('is_admin');
        },
    },
    computed: {
        createLabel() {
            return this.subscribable ? 'enrol' : 'create';
        },
        deleteLabel() {
            return this.subscribable ? 'expel' : 'delete';
        },
    },
}
</script>