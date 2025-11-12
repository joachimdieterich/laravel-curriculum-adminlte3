<template >
    <div class="row">
        <div
            id="objective-type-content"
            class="col-md-12 m-0"
        >
            <IndexWidget
                v-permission="'objectivetype_create'"
                key="objectiveTypeCreate"
                modelName="ObjectiveType"
                url="/objectiveTypes"
                :create=true
                :label="trans('global.objectiveType.create')"
            />
            <IndexWidget v-for="objectiveType in objectiveTypes"
                :key="'objectiveTypeIndex' + objectiveType.id"
                :model="objectiveType"
                modelName="ObjectiveType"
                url="/objectiveTypes"
            >
                <template v-slot:icon>
                    <i class="fa fa-university"></i>
                </template>

                <template v-slot:dropdown
                    v-permission="'objectivetype_edit, objectivetype_delete'"
                >
                    <div
                        class="dropdown-menu dropdown-menu-right"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            v-permission="'objectivetype_edit'"
                            :name="'edit-objective-type-' + objectiveType.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editObjectiveType(objectiveType)"
                        >
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.objectiveType.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'objectivetype_delete'"
                            :id="'delete-objective-type-' + objectiveType.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(objectiveType)"
                        >
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.objectiveType.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <div
            id="objective-type-datatable-wrapper"
            class="w-100 dataTablesWrapper"
        >
            <DataTable
                id="objective-type-datatable"
                :columns="columns"
                :options="options"
                ajax="/objectiveTypes/list"
                :search="search"
                width="100%"
                style="display: none;"
            />
        </div>

        <Teleport to="body">
            <ObjectiveTypeModal/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.objectiveType.delete')"
                :description="trans('global.objectiveType.delete_helper')"
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
import ObjectiveTypeModal from "../objectiveType/ObjectiveTypeModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import {useGlobalStore} from "../../store/global";
DataTable.use(DataTablesCore);

export default {
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            objectiveTypes: null,
            showConfirm: false,
            search: '',
            currentObjectiveType: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
            ],
            options : this.$dtOptions,
            dt: null,
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.loaderEvent();

        this.$eventHub.on('objectiveType-added', (objectiveType) => {
            this.objectiveTypes.push(objectiveType);
        });

        this.$eventHub.on('objectiveType-updated', (updatedObjectiveType) => {
            let objectiveType = this.objectiveTypes.find(type => type.id === updatedObjectiveType.id);

            Object.assign(objectiveType, updatedObjectiveType);
        });

        this.$eventHub.on('filter', (filter) => {
            this.dt.search(filter).draw();
        });
    },
    methods: {
        editObjectiveType(objectiveType) {
            this.globalStore?.showModal('objectivetype-modal', objectiveType);
        },
        confirmItemDelete(objectiveType) {
            this.currentObjectiveType = objectiveType;
            this.showConfirm = true;
        },
        loaderEvent() {
            this.dt = $('#objective-type-datatable').DataTable();
            this.dt.on('draw.dt', () => {
                this.objectiveTypes = this.dt.rows({page: 'current'}).data().toArray();

                $('#objective-type-content').insertBefore('#objective-type-datatable-wrapper');
            });
        },
        destroy() {
            axios.delete('/objectiveTypes/' + this.currentObjectiveType.id)
                .then(res => {
                    let index = this.objectiveTypes.indexOf(this.currentObjectiveType);
                    this.objectiveTypes.splice(index, 1);
                })
                .catch(err => {
                    console.log(err);
                });
        },
    },
    components: {
        ConfirmModal,
        DataTable,
        ObjectiveTypeModal,
        IndexWidget,
    },
}
</script>