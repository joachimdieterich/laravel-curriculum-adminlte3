<template>
    <div>
        <div class="d-flex flex-column">
            <div
                id="group-content"
                class="px-3"
            >
                <div v-if="checkPermission('is_schooladmin')"
                    class="d-flex justify-content-end"
                >
                    <button
                        type="button"
                        class="btn"
                        :class="btnClass"
                        @click="setMode()"
                    >
                        {{ trans('global.select') }}
                    </button>
                </div>

                <IndexWidget v-if="checkPermission('group_create')"
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
                                <i class="fa fa-pencil-alt me-2"></i>
                                {{ trans('global.group.edit') }}
                            </button>

                            <hr class="my-1">

                            <button
                                v-permission="'group_delete'"
                                type="submit"
                                class="dropdown-item text-danger"
                                @click="confirmItemDelete(group)"
                            >
                                <i class="fa fa-trash me-2"></i>
                                {{ trans('global.group.delete') }}
                            </button>
                        </div>
                    </template>

                    <template #badges>
                        <span
                            class="btn btn-info btn-xs position-absolute"
                            style="bottom: 5px; right: 5px;"
                        >
                            <i class="fa fa-university"></i>
                            {{ group.organization }}
                        </span>
                    </template>
                </IndexWidget>
            </div>

            <DataTable
                ref="datatable"
                :columns="columns"
                :options="options"
                ajax="/groups/list"
                class="d-none"
                @xhr="(e, settings, json) => groups = json.data"
            />

            <GroupOptions v-if="checkPermission('is_schooladmin')"
                class="mt-4"
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
DataTable.use(DataTablesCore);

export default {
    components: {
        ConfirmModal,
        DataTable,
        GroupModal,
        GroupOptions,
        IndexWidget,
    },
    data() {
        return {
            component_id: this.$.uid,
            groups: null,
            showConfirm: false,
            currentGroup: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
                { title: 'organization', data: 'organization', searchable: true },
            ],
            options : this.$dtOptions,
            dt: null,
        }
    },
    setup() {
        return { store: useDatatableStore() }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.dt = this.$refs.datatable.dt;

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
                select: (this.store.getDatatable('groups')?.select) ? false : true,
                selectedItems: [],
            });
        },
        editGroup(group) {
            this.globalStore.showModal('group-modal', group);
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
    },
    computed: {
        btnClass() {
            return this.store.getDatatable('groups')?.select === true
                ? 'btn-dark'
                : 'btn-outline-dark';
        },
    },
}
</script>