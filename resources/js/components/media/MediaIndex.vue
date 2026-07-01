<template>
    <div>
        <div class="bg-white mx-3 mb-3">
            <h2>Parameter</h2>

            <span class="custom-control custom-switch custom-switch-on-green">
                <input
                    id="show-local"
                    class="custom-control-input pt-1"
                    type="checkbox"
                    v-model="showLocal"
                />
                <label
                    class="custom-control-label text-muted"
                    for="show-local"
                >
                    Lokale Medien anzeigen
                </label>
            </span>
            <span class="custom-control custom-switch custom-switch-on-green">
                <input
                    id="show-external"
                    class="custom-control-input pt-1"
                    type="checkbox"
                    v-model="showExternal"
                />
                <label
                    class="custom-control-label text-muted"
                    for="show-external"
                >
                    Externe Medien anzeigen
                </label>
            </span>

            <button
                class="btn btn-primary my-2"
                type="button"
                :disabled="!showLocal && !showExternal"
                @click="loader"
            >
                Aktualisieren
            </button>
        </div>
        <div
            id="media-datatable-wrapper"
            class="w-100 dataTablesWrapper"
        >
            <DataTable
                id="media-datatable"
                :columns="columns"
                :data="media"
                width="100%"
            />
        </div>
    </div>
</template>
<script>
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import 'datatables.net-select-bs5'
DataTable.use(DataTablesCore);

export default {
    data() {
        return {
            component_id: this.$.uid,
            media: null,
            dt: null,
            columns: [
                {
                    title: 'img',
                    data: 'id',
                    // render: function(data) {
                    //     return '<img src="/media/'+ data +'" width="60"/>';
                    // }
                },
                { title: 'title', data: 'title' },
                { title: 'adapter', data: 'adapter' },
                { title: 'size', data: 'size' },
            ],
            options : this.$dtOptions,
            showLocal: true,
            showExternal: true,
        }
    },
    mounted() {
        this.loader();
    },
    methods: {
        loader() {
            this.dt = $('#media-datatable').DataTable();
            
            axios.get('/media/adminSearch', {
                params: {
                    showLocal: this.showLocal,
                    showExternal: this.showExternal,
                },
            }).then(response => {
                this.media = response.data;
            });
        },
    },
    components: {
        DataTable,
    }
}
</script>