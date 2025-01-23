<template >
    <div>
        <div class="row">
            <div
                id="group-content"
                class="col-md-12 m-0"
            >
                <div class="row">
                    <div class="col-md-12 m-0 pb-2">
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
                        <i class="fa fa-layer-group pt-2"></i>
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
                        <p class="text-muted small">
                            <span
                                class="btn btn-info btn-xs position-absolute select-all pull-right mr-1"
                                style="bottom: 0; margin: 5px 40px 8px 0; width: max-content; right: 5px;"
                            >
                                <i class="fa fa-university"></i>
                                {{ group.organization }}
                            </span>
                        </p>
                    </template>
                </IndexWidget>
            </div>
            <div
                id="group-datatable-wrapper"
                class="w-100 dataTablesWrapper"
            >
                <DataTable
                    id="group-datatable"
                    :columns="columns"
                    :options="options"
                    ajax="/groups/list"
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
        <div class="row mt-4">
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
DataTable.use(DataTablesCore);

export default {
    props: {},
    data() {
        return {
            component_id: this.$.uid,
            groups: null,
            search: '',
            showConfirm: false,
            errors: {},
            currentGroup: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
                { title: 'grade', data: 'grade' },
                { title: 'period', data: 'period', searchable: true },
                { title: 'organization', data: 'organization', searchable: true },
            ],
            options : this.$dtOptions,
            dt: null,
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
        this.$eventHub.emit('showSearchbar', true);

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
                select: (this.store.getDatatable('groups')?.select) ? false : true,
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
    },
    computed: {
        classObject() {
            if (this.store.getDatatable('groups')?.select === true) {
                return 'btn-dark'
            } else {
                return 'btn-light'
            }
        },
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