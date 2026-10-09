<template>
    <div class="px-3">
        <div class="col-lg-4 col-12 mb-3 rounded-3 shadow-layout">
            <div class="d-flex align-items-center justify-content-between p-2 text-bg-primary rounded-top-3">
                <h5 class="m-0">
                    <i class="fa fa-university mx-1"></i>{{ objectiveType.title }}
                </h5>

                <button v-if="checkPermission('objectivetype_edit')"
                    type="button"
                    class="btn btn-icon-alt"
                    @click="editObjectiveType()"
                >
                    <i class="fa fa-pencil-alt"></i>
                </button>
            </div>

            <div class="px-3 py-2 bg-white rounded-bottom-3">
                <small class="text-muted">{{ objectiveType.updated_at }}</small>
            </div>
        </div>

        <Teleport to="body">
            <ObjectiveTypeModal/>
        </Teleport>
    </div>
</template>
<script>
import ObjectiveTypeModal from "../objectiveType/ObjectiveTypeModal.vue";

export default {
    name: "ObjectiveType",
    components: { ObjectiveTypeModal },
    props: {
        objectiveType: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            componentId: this.$.uid,
        }
    },
    mounted() {
        this.$eventHub.on('objectiveType-updated', () => window.location.reload());
    },
    methods: {
        editObjectiveType(){
            this.globalStore.showModal('objectivetype-modal', this.objectiveType);
        },
    },
}
</script>