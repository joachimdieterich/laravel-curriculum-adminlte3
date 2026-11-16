<template>
    <div class="d-flex flex-column">
        <div
            id="objective-type-content"
            class="px-3"
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
                <template #icon>
                    <i class="fa fa-university"></i>
                </template>

                <template #dropdown
                    v-permission="'objectivetype_edit, objectivetype_delete'"
                >
                    <div class="dropdown-menu dropdown-menu-end">
                        <button
                            v-permission="'objectivetype_edit'"
                            type="button"
                            class="dropdown-item"
                            @click="editObjectiveType(objectiveType)"
                        >
                            <i class="fa fa-pencil-alt"></i>
                            {{ trans('global.objectiveType.edit') }}
                        </button>

                        <hr class="my-1">

                        <button
                            v-permission="'objectivetype_delete'"
                            type="submit"
                            class="dropdown-item text-danger"
                            @click="confirmItemDelete(objectiveType)"
                        >
                            <i class="fa fa-trash"></i>
                            {{ trans('global.objectiveType.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <DataTable
            ref="datatable"
            id="objective-type-datatable"
            :columns="columns"
            :options="$dtOptions"
            ajax="/objectiveTypes/list"
            class="d-none"
            @xhr="(e, settings, json) => objectiveTypes = json.data"
        />

        <Teleport to="body">
            <ObjectiveTypeModal/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.objectiveType.delete')"
                :description="trans('global.objectiveType.delete_helper')"
                @close="showConfirm = false"
                @confirm="destroy()"
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
DataTable.use(DataTablesCore);

export default {
    components: {
        ConfirmModal,
        DataTable,
        ObjectiveTypeModal,
        IndexWidget,
    },
    data() {
        return {
            component_id: this.$.uid,
            objectiveTypes: null,
            showConfirm: false,
            currentObjectiveType: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
            ],
            dt: null,
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.dt = this.$refs.datatable.dt;

        this.$eventHub.on('objectiveType-added', objectiveType => {
            this.objectiveTypes.push(objectiveType);
        });

        this.$eventHub.on('objectiveType-updated', updatedObjectiveType => {
            let objectiveType = this.objectiveTypes.find(type => type.id === updatedObjectiveType.id);

            Object.assign(objectiveType, updatedObjectiveType);
        });

        this.$eventHub.on('filter', filter => {
            this.dt.search(filter.searchString).draw();
        });
    },
    methods: {
        editObjectiveType(objectiveType) {
            this.globalStore.showModal('objectivetype-modal', objectiveType);
        },
        confirmItemDelete(objectiveType) {
            this.currentObjectiveType = objectiveType;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/objectiveTypes/' + this.currentObjectiveType.id)
                .then(res => {
                    this.showConfirm = false;
                    let index = this.objectiveTypes.indexOf(this.currentObjectiveType);
                    this.objectiveTypes.splice(index, 1);
                })
                .catch(e => {
                    console.log(e);
                    this.showConfirm = false;
                    this.toast.error(this.errorMessage(e));
                });
        },
    },
}
</script>