<template>
    <div>
        <div
            v-if="visible"
            :id="'kanban-add'"
            class="box box-objective nav-item-box-image pointer my-1"
            style="min-width: 200px !important; "
            @click="open()">
            <div class="nav-item-box-image-size text-center bg-success">
                <i class="fa fa-2x p-5 fa-plus nav-item-text text-white"></i>
            </div>

            <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                   <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                       {{ trans('global.kanban.create') }}
                   </h1>
            </span>
        </div>
        <!-- Create Modal -->
        <KanbanForm
            id="modal-kanban-form"
            :kanban="kanban"
        />
    </div>

</template>
<script>
import Form from "form-backend-validation";
import KanbanForm from "./KanbanForm";

export default {
    name: 'KanbanIndexAddWidget',
    props: {
        visible: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            kanban: null,
        }
    },
    methods: {
       open(){
           $('#modal-kanban-form').modal('show');
       },
    },
    mounted() {
        this.$eventHub.$on('edit_kanban', (kanban) => {
            this.kanban = kanban;
            this.open();
        });
    },
    components: {
        KanbanForm,
    },
}
</script>
