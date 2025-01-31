<template>
<div>
    <div id="media_create_datatable_filter"
         class="dataTables_filter">
        <input
            id="media_search_datatable"
            name="media_search_datatable"
            type="search"
            class="form-control form-control-sm"
            v-model="search"
            placeholder="Suchbegriff"
            aria-controls="media_create_datatable"
        />
    </div>
    <div class="form-group table-responsive" >
        <div
            id="media-datatable-wrapper"
            class="w-100 dataTablesWrapper"
        >
            <DataTable
                id="media-datatable"
                columns="columns"
                :options="options"
                :ajax="'/media/list'"
                :search="search"
                width="100%"
            ></DataTable>
        </div>
    </div>
</div>
</template>

<script>
import Form from 'form-backend-validation';
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import 'datatables.net-select-bs5'
import {useGlobalStore} from "../../store/global.js";

DataTable.use(DataTablesCore);

    export default {
        props: {

        },
        setup () { //use database store
            const globalStore = useGlobalStore();
            return {
                globalStore
            }
        },
        data() {
            return {
                component_id: this.$.uid,
                search: '',
                columns: [
                    {
                        title: 'img',
                        data: 'id',
                        render: function(data, type, full, meta) {
                            return '<img src="/media/'+ data +'" width="60"/>';
                        }
                    },
                    { title: 'title', data: 'title', searchable: true },
                    { title: 'size', data: 'size' },
                    { title: 'created_at', data: 'created_at' },
                ],
                options : this.$dtOptions,
                postProcess: false,
            }
        },

        methods: {
          /*  show(mediumObject) {
                this.globalStore?.showModal('medium-preview-modal', mediumObject);
            },*/
        },

        mounted() {
            const dt = $('#media-datatable').DataTable();

            $('#media_search_datatable').on('keyup', function () {
                dt.search(this.search).draw();
            }.bind(this));

            dt.on('select', function(e, dt, type, indexes) {
                let selection = dt.rows('.selected').data().toArray()
                this.globalStore.setSelectedMedia(selection);
            }.bind(this));

        },
        components: {
            DataTable
        }
    }
</script>
