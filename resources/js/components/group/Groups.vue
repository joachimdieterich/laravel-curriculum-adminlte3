<template>
    <div>
        <div class="d-flex flex-column">
            <div
                id="group-content"
                class="px-3"
            >
                <div class="row">
                    <div class="col-md-12 m-0 pb-2">
                        <span class="custom-control custom-switch custom-switch-on-green">
                            <input
                                id="toggle-view"
                                class="custom-control-input pt-1"
                                type="checkbox"
                                v-model="allGroupsView"
                                @click="toggleAllGroupsView"
                            />
                            <label
                                class="custom-control-label text-muted"
                                for="toggle-view"
                            >
                                {{ trans('global.group.allGroups') }}
                            </label>
                        </span>
                        <button
                            class="pull-right btn"
                            :class="classObject"
                            @click="setMode()"
                        >
                            {{ trans('global.select') }}
                        </button>
                    </div>
                </div>
                <IndexWidget
                    v-permission="'group_create'"
                    key="groupCreate"
                    modelName="Group"
                    url="/groups"
                    :create=true
                    :label="trans('global.group.create')"
                />
                <IndexWidget v-for="group in groups"
                             :key="'groupIndex'+group.id"
                             :model="group"
                             modelName="Group"
                             storeTitle="groups"
                             url="/groups"
                >
                    <template v-slot:icon>
                        <i class="fa fa-layer-group"></i>
                    </template>

                    <template v-slot:dropdown
                              v-permission="'group_edit, group_delete'"
                    >
                        <div
                            class="dropdown-menu dropdown-menu-right"
                            style="z-index: 1050;"
                            x-placement="left-start"
                        >
                            <button
                                v-permission="'group_edit'"
                                :name="'edit-group-' + group.id"
                                class="dropdown-item text-secondary"
                                @click.prevent="editGroup(group)"
                            >
                                <i class="fa fa-pencil-alt mr-2"></i>
                                {{ trans('global.group.edit') }}
                            </button>
                            <hr class="my-1">
                            <button
                                v-permission="'group_delete'"
                                :id="'delete-group-' + group.id"
                                type="submit"
                                class="dropdown-item py-1 text-red"
                                @click.prevent="confirmItemDelete(group)"
                            >
                                <i class="fa fa-trash mr-2"></i>
                                {{ trans('global.group.delete') }}
                            </button>
                        </div>
                    </template>

                    <template v-slot:badges>
                        <p>
                            <span
                                class="btn btn-info btn-xs position-absolute mr-1"
                                style="bottom: 0; margin: 5px 40px 8px 0; width: max-content; right: 5px;"
                            >
                                <i class="fa fa-university"></i>
                                {{ group.organization }}
                            </span>
                        </p>
                    </template>
                </IndexWidget>
            </div>
            <div v-if="organizationFilter !== null"
                 id="organization-filter"
                 class="position-relative d-flex flex-nowrap overflow-auto my-2 px-3"
                 tabindex="-1"
                 aria-label="Filter"
            >
                <span id="organization-filter-wrapper"
                      class="d-flex mb-2"
                >
                    <span v-for="(organization, index) in organizationFilter"
                          :key="organization"
                          class="flex-shrink-0"
                    >
                        <button :id="'organization-filter-' + organization.id"
                                class="btn btn-tab rounded-pill"
                                :class="{'active': organization.filter}"
                                type="button"
                                @click="organizationFilterClick(organization)"
                                @keydown.enter.space="organizationFilterClick(organization)"
                                @keydown.left.right.prevent="moveFocus($event)"
                        >

                            <i class="fas pr-2 fa-city"></i>
                            {{ organization.title }}
                        </button>
                    </span>
                </span>
            </div>
            <div
                id="group-datatable-wrapper"
                class="dataTablesWrapper"
            >
                <DataTable
                    id="group-datatable"
                    :columns="columns"
                    :options="options"
                    :ajax="dtAjaxOptions"
                    :search="search"
                    width="100%"
                    style="display: none;"
                />
            </div>

            <Teleport to="body">
                <GroupModal/>
                <ConfirmModal
                    :showConfirm="this.showConfirm"
                    :title="trans('global.group.delete')"
                    :description="trans('global.group.delete_helper')"
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
        <div v-if="checkPermission('is_schooladmin')"
             class="mt-4"
        >
            <GroupOptions/>
        </div>
    </div>
</template>
<script>
import GroupModal from "../group/GroupModal.vue";
import GroupOptions from "../group/GroupOptions.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import {useDatatableStore} from "../../store/datatables";
import {useGlobalStore} from "../../store/global";
import {globalValues} from "../../globalValues.js";

DataTable.use(DataTablesCore);

export default {
    props: {
        organizations: null
    },
    data() {
        return {
            component_id: this.$.uid,
            groups: null,
            organizationFilter: null,
            search: '',
            showConfirm: false,
            currentGroup: {},
            columns: [
                {title: 'id', data: 'id'},
                {title: 'title', data: 'title', searchable: true},
                {title: 'organization', data: 'organization', searchable: true},
            ],
            options: this.$dtOptions,
            dt: null,
            allGroupsView: false
        }
    },
    setup() {
        const globalStore = useGlobalStore();
        const store = useDatatableStore();
        return {
            store,
            globalStore,
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.loaderEvent();

        this.$eventHub.on('group-added', (group) => {
            this.groups.push(group);
        });

        this.$eventHub.on('group-updated', (updatedGroup) => {
            let group = this.groups.find(g => g.id === updatedGroup.id);

            Object.assign(group, updatedGroup);
        });

        this.$eventHub.on('filter', (filter) => {
            this.dt.search(filter).draw();
        });
    },
    methods: {
        setMode() {
            this.store.addToDatatables({
                datatable: 'groups',
                select: !this.store.getDatatable('groups')?.select,
                selectedItems: [],
            });
        },
        editGroup(group) {
            this.globalStore?.showModal('group-modal', group);
        },
        loaderEvent() {
            this.dt = $('#group-datatable').DataTable();
            this.dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.groups = this.dt.rows({page: 'current'}).data().toArray();

                $('#group-content').insertBefore('#group-datatable-wrapper');
            });

            this.setupOrganizationFilter();
        },
        setupOrganizationFilter() {
            this.organizations.forEach(organization => {
                organization.filter = false;
            });
            this.organizationFilter = this.organizations;
        },
        confirmItemDelete(group) {
            this.currentGroup = group;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/groups/' + this.currentGroup.id)
                .then(res => {
                    let index = this.groups.indexOf(this.currentGroup);
                    this.groups.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        toggleAllGroupsView() {
            this.allGroupsView = !this.allGroupsView;
            this.dt.ajax.reload();
        },
        moveFocus(e) {
            const currentOrganizationFilter = e.target;

            if (e.key === 'ArrowRight') {
                const nextOrganizationFilter = currentOrganizationFilter.parentElement.nextElementSibling ?? currentOrganizationFilter.parentElement.parentElement.firstElementChild;
                if (nextOrganizationFilter) {
                    nextOrganizationFilter.firstElementChild.focus();
                }
            } else {
                const previousOrganizationFilter = currentOrganizationFilter.parentElement.previousElementSibling ?? currentOrganizationFilter.parentElement.parentElement.lastElementChild;
                if (previousOrganizationFilter) {
                    previousOrganizationFilter.firstElementChild.focus();
                }
            }
        },
        organizationFilterClick(organization) {
            organization.filter = !organization.filter;

            this.dt.ajax.reload();
        }
    },
    computed: {
        classObject() {
            if (this.store.getDatatable('groups')?.select === true) {
                return 'btn-dark'
            } else {
                return 'btn-light'
            }
        },
        dtAjaxOptions() {
            return {
                url: '/groups/list',
                data: (d) => {
                    d.allGroups = this.allGroupsView;
                    d.organizationFilter = this.activeOrganizationFilter;

                    return d;
                },
            };
        },
        activeOrganizationFilter() {
            return Object.values(this.organizationFilter)
                .filter(organization => organization.filter === true)
                .map(organization => organization.id);
        }
    },
    components: {
        ConfirmModal,
        DataTable,
        GroupModal,
        GroupOptions,
        IndexWidget,
    },
}
</script>
<style>
#organization-filter-wrapper {
    gap: 0.3rem;
}
</style>