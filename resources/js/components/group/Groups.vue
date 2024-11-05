<template >
    <div>
        <div class="row">
            <div id="group-content"
                 class="col-md-12 m-0">
                <div class="row">
                    <div class="col-md-12 m-0 pb-2">
                        <button
                            class="pull-right btn"
                            :class="classObject"
                            @click="setMode()">
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
                    :createLabel="trans('global.group.create')">
                </IndexWidget>
                <IndexWidget
                    v-for="group in groups"
                    :key="'groupIndex'+group.id"
                    :model="group"
                    modelName= "group"
                    storeTitle= "groups"
                    url="/groups">
                    <template v-slot:icon>
                        <i class="fa fa-layer-group pt-2"></i>
                    </template>

                    <template
                        v-permission="'group_edit, group_delete'"
                        v-slot:dropdown>
                        <div class="dropdown-menu dropdown-menu-right"
                             style="z-index: 1050;"
                             x-placement="left-start">
                            <button
                                v-permission="'group_edit'"
                                :name="'edit-group-' + group.id"
                                class="dropdown-item text-secondary"
                                @click.prevent="editGroup(group)">
                                <i class="fa fa-pencil-alt mr-2"></i>
                                {{ trans('global.group.edit') }}
                            </button>
                            <hr class="my-1">
                            <button
                                v-permission="'group_delete'"
                                :id="'delete-group-' + group.id"
                                type="submit"
                                class="dropdown-item py-1 text-red"
                                @click.prevent="confirmItemDelete(group)">
                                <i class="fa fa-trash mr-2"></i>
                                {{ trans('global.group.delete') }}
                            </button>
                        </div>
                    </template>

                    <template
                        v-slot:badges>
                        <p class="text-muted small">
                               <span class="btn btn-info btn-xs select-all pull-right mr-1"
                                     style="position: absolute;bottom: 0;margin: 5px 40px 8px 0;width: max-content;right: 5px;">
                               <i class="fa fa-university"></i> {{ group.organization }}
                           </span>
                        </p>
                    </template>

                </IndexWidget>
            </div>
            <div id="group-datatable-wrapper"
                 class="w-100 dataTablesWrapper">
                <DataTable
                    id="group-datatable"
                    :columns="columns"
                    :options="options"
                    :ajax="url"
                    :search="search"
                    width="100%"
                    style="display:none; "
                ></DataTable>
            </div>

            <Teleport to="body">
                <GroupModal></GroupModal>
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
                ></ConfirmModal>
            </Teleport>
        </div>
        <div class="row mt-4">
            <group-options></group-options>
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
    props: {

    },
    data() {
        return {
            component_id: this.$.uid,
            groups: null,
            search: '',
            showConfirm: false,
            url: '/groups/list',
            errors: {},
            currentGroup: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
                { title: 'grade', data: 'grade'},
                { title: 'period', data: 'period', searchable: true},
                { title: 'organization', data: 'organization', searchable: true},
            ],
            options : this.$dtOptions,
            modalMode: 'edit'
        }
    },
    setup () {
        const globalStore = useGlobalStore();
        const store = useDatatableStore();
        return {
            store,
            globalStore
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('group-added', (group) => {
            this.globalStore?.closeModal('group-modal');
            this.groups.push(group);
        });

        this.$eventHub.on('group-updated', (group) => {
            this.globalStore?.closeModal('group-modal');
            this.update(group);
        });
        this.$eventHub.on('createGroup', () => {
            this.globalStore?.showModal('group-modal', {});
        });
    },
    methods: {
        setMode(){
            this.store.addToDatatables(
                {
                    'datatable': 'groups',
                    'select': (this.store.getDatatable('groups')?.select) ? false : true,
                    'selectedItems': []
                }
            )
        },
        editGroup(group){
            this.globalStore?.showModal('group-modal', group);
        },
        loaderEvent(){
            const dt = $('#group-datatable').DataTable();
            dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.groups = dt.rows({page: 'current'}).data().toArray();

                $('#group-content').insertBefore('#group-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                dt.search(filter).draw();
            });
        },
        confirmItemDelete(group){
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
        update(group) {
            const index = this.groups.findIndex(
                vc => vc.id === group.id
            );

            for (const [key, value] of Object.entries(group)) {
                this.groups[index][key] = value;
            }
        }
    },
    computed: {
        classObject() {
            if (this.store.getDatatable('groups')?.select === true) {
                return 'btn-dark'
            } else {
                return 'btn-light'
            }
        }
    },
    components: {
        ConfirmModal,
        DataTable,
        GroupModal,
        GroupOptions,
        IndexWidget
    },
}
</script>
