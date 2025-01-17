<template >
    <div class="row">
        <div id="objective-type-content"
             class="col-md-12 m-0">
            <IndexWidget
                v-permission="'objectivetype_create'"
                key="'objectiveTypeCreate'"
                modelName="ObjectiveType"
                url="/objectiveTypes"
                :create=true
                :label="trans('global.objectiveType.create')">
            </IndexWidget>
            <IndexWidget
                v-for="objectiveType in objectiveTypes"
                :key="'objectiveTypeIndex'+objectiveType.id"
                :model="objectiveType"
                modelName= "ObjectiveType"
                url="/objectiveTypes">
                <template v-slot:icon>
                    <i class="fa fa-university pt-2"></i>
                </template>

                <template
                    v-permission="'objectivetype_edit, objectivetype_delete'"
                    v-slot:dropdown>
                    <div class="dropdown-menu dropdown-menu-right"
                         style="z-index: 1050;"
                         x-placement="left-start">
                        <button
                            v-permission="'objectivetype_edit'"
                            :name="'edit-objective-type-' + objectiveType.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editObjectiveType(objectiveType)">
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.objective.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'objectivetype_delete'"
                            :id="'delete-objective-type-' + objectiveType.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(objectiveType)">
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.objectiveType.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div id="objective-type-datatable-wrapper"
             class="w-100 dataTablesWrapper">
            <DataTable
                id="objective-type-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display:none; "
            ></DataTable>
        </div>

        <Teleport to="body">
            <ObjectiveTypeModal></ObjectiveTypeModal>
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
    props: {

    },
    setup () {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            objectiveTypes: null,
            search: '',
            url: '/objectiveTypes/list',
            errors: {},
            currentObjectiveType: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
            ],
            options : this.$dtOptions,
            modalMode: 'edit'
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('objectiveType-added', (objectiveType) => {
            this.loaderEvent();
            this.globalStore?.closeModal('objective-type-modal');
        });

        this.$eventHub.on('objectiveType-updated', (objectiveType) => {
            this.globalStore?.closeModal('objective-type-modal');
        });
        this.$eventHub.on('createObjectiveType', () => {
            this.globalStore?.showModal('objective-type-modal', {});
        });
    },
    methods: {
        editObjectiveType(objectiveType){
            this.currentObjectiveType = objectiveType;
            this.globalStore?.showModal('objective-type-modal', this.currentObjectiveType);
        },
        loaderEvent(){
            const dt = $('#objective-type-datatable').DataTable();
            dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.objectiveTypes = dt.rows({page: 'current'}).data().toArray();

                $('#objective-type-content').insertBefore('#objective-type-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                dt.search(filter).draw();
            });
        },
        destroy() {
            axios.delete('/objectiveTypes/' + this.currentObjectiveType.id)
                .then(res => {
                    let index = this.objectiveTypes.indexOf(this.currentObjectiveType);
                    this.objectiveTypes.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        update(objectiveType) {
            const index = this.objectiveTypes.findIndex(
                vc => vc.id === objectiveType.id
            );

            for (const [key, value] of Object.entries(objectiveType)) {
                this.objectives[index][key] = value;
            }
        }
    },
    components: {
        ConfirmModal,
        DataTable,
        ObjectiveTypeModal,
        IndexWidget
    },
}
</script>
