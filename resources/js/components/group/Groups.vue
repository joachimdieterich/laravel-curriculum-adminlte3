<template>
    <div class="d-flex flex-column">
        <div
            id="group-content"
            class="px-3"
        >
            <div
                class="d-flex justify-content-between"
            >
                <Switch
                    id="group-toggle-all"
                    label="global.group.allGroups"
                    v-model="allGroupsView"
                    @change="toggleAllGroupsView()"
                />
            </div>

            <div v-if="organizationFilter !== null && (organizationFilter.length > 1 || checkPermission('is_admin'))"
                 id="organization-filter"
                 class="position-relative d-flex flex-nowrap overflow-auto my-2"
                 tabindex="-1"
                 aria-label="Filter"
            >
                <span id="organization-filter-wrapper"
                      class="d-flex"
                >
                    <span v-for="organization in organizationFilter"
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

            <IndexWidget v-if="checkPermission('group_create')"
                         key="groupCreate"
                         modelName="Group"
                         url="/groups"
                         :create=true
                         :label="trans('global.group.create')"
            />
            <IndexWidget v-for="group in groups"
                         :key="'groupIndex' + group.id"
                         :model="group"
                         modelName="Group"
                         storeTitle="groups"
                         url="/groups"
            >
                <template #icon>
                    <i class="fa fa-layer-group"></i>
                </template>

                <template #dropdown
                          v-permission="'group_edit, group_delete'"
                >
                    <div class="dropdown-menu dropdown-menu-end">
                        <button
                            v-permission="'group_edit'"
                            type="button"
                            class="dropdown-item"
                            @click="editGroup(group)"
                        >
                            <i class="fa fa-pencil-alt"></i>
                            {{ trans('global.group.edit') }}
                        </button>

                        <hr class="my-1">

                        <button
                            v-permission="'group_delete'"
                            type="submit"
                            class="dropdown-item text-danger"
                            @click="confirmItemDelete(group)"
                        >
                            <i class="fa fa-trash"></i>
                            {{ trans('global.group.delete') }}
                        </button>
                    </div>
                </template>

                <template #badges>
                    <button
                        type="button"
                        class="btn btn-info btn-xs position-absolute"
                        style="bottom: 5px; right: 5px;"
                        tabindex="-1"
                        @click.stop="filterByOrg(group.organization.title)"
                    >
                        <i class="fa fa-university"></i>
                        {{ group.organization.title }}
                    </button>
                </template>
            </IndexWidget>
        </div>

        <DataTable
            ref="datatable"
            :columns="columns"
            :options="$dtOptions"
            :ajax="dtAjaxOptions"
            class="d-none"
            @xhr="(e, settings, json) => groups = json.data"
        />

        <Teleport to="body">
            <GroupModal/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.group.delete')"
                :description="trans('global.group.delete_helper')"
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
import GroupModal from "../group/GroupModal.vue";
import Switch from "../forms/Switch.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import {useDatatableStore} from "../../store/datatables";

DataTable.use(DataTablesCore);

export default {
    components: {
        Switch,
        ConfirmModal,
        DataTable,
        GroupModal,
        IndexWidget,
    },
    props: {
        organizations: null
    },
    data() {
        return {
            component_id: this.$.uid,
            groups: null,
            organizationFilter: null,
            showConfirm: false,
            currentGroup: {},
            columns: [
                {title: 'id', data: 'id'},
                {title: 'title', data: 'title', searchable: true},
                {title: 'organization', data: 'organization.title', searchable: true},
            ],
            dt: null,
            allGroupsView: false
        }
    },
    setup() {
        return {store: useDatatableStore()}
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.dt = this.$refs.datatable.dt;

        this.setupOrganizationFilter();

        this.$eventHub.on('group-added', group => {
            this.groups.push(group);
        });

        this.$eventHub.on('group-updated', updatedGroup => {
            let group = this.groups.find(g => g.id === updatedGroup.id);

            Object.assign(group, updatedGroup);
        });

        this.$eventHub.on('filter', filter => {
            this.dt.search(filter.searchString).draw();
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
            this.globalStore.showModal('group-modal', group);
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
                .catch(e => {
                    console.log(e);
                });
        },
        filterByOrg(title) {
            this.globalStore['search'] = title;
        },
        toggleAllGroupsView() {
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
        btnClass() {
            return this.store.getDatatable('groups')?.select === true
                ? 'btn-dark'
                : 'btn-outline-dark';
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
        },
    },
}
</script>
<style>
#organization-filter-wrapper {
    gap: 0.3rem;
}
</style>