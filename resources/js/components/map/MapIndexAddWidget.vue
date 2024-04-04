<template>
    <div>
        <div
            v-if="visible"
            :id="'map-add'"
            class="box box-objective nav-item-box-image pointer my-1"
            style="min-width: 200px !important; border-style: dotted !important; "
            @click="open()">
            <div class="nav-item-box-image-size text-center">
                <i class="fa fa-2x p-5 fa-plus nav-item-text text-muted"></i>
            </div>

            <span class="text-center p-1 overflow-auto nav-item-box bg-gray-light">
                   <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                       {{ trans('global.map.create') }}
                   </h1>
            </span>
        </div>
        <!-- Create Modal -->
        <MapCreate
            id="modal-map-form"
            :method="method"
            :map="map"
        />
    </div>

</template>
<script>
import Form from "form-backend-validation";
import MapCreate from "./MapCreate";

export default {
    name: 'mapIndexAddWidget',
    props: {
        visible: {
            type: Boolean,
            default: true
        },

    },
    data() {
        return {
            map: null,
            method: {
                type: String,
                default: 'post'
            }
        }
    },
    methods: {
       open(method = 'post'){
           this.method = method;
           $('#modal-map-form').modal('show');
       },
    },
    mounted() {
        this.$eventHub.$on('edit_map', (map) => {
            this.map = map;
            this.open('patch');
        });
    },
    components: {
        MapCreate,
    },
}
</script>
