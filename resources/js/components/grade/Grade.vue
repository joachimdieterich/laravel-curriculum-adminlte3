<template>
    <div class="px-3">
        <div class="col-lg-4 col-12 mb-3 rounded-3 shadow-layout">
            <div class="d-flex align-items-center justify-content-between p-2 text-bg-primary rounded-top-3">
                <h5 class="m-0">
                    <i class="fa fa-layer-group mx-1"></i>{{ grade.title }}
                </h5>

                <button v-if="checkPermission('grade_edit')"
                    type="button"
                    class="btn btn-icon-alt"
                    @click="editGrade()"
                >
                    <i class="fa fa-pencil-alt"></i>
                </button>
            </div>

            <div class="px-3 py-2 bg-white rounded-bottom-3">
                <span class="text-muted">
                    {{ trans('global.grade.fields.external_begin') }}: {{ grade.external_begin }}<br/>
                    {{ trans('global.grade.fields.external_end') }}: {{ grade.external_end }}
                </span>

                <hr>

                <strong>
                    <i class="fa fa-city me-1"></i>
                    {{ trans('global.organizationType.title_singular') }}
                </strong>
                <div class="text-muted">{{ grade.organization_type?.title }}</div>
            </div>
        </div>

        <Teleport to="body">
            <GradeModal/>
        </Teleport>
    </div>
</template>
<script>
import GradeModal from "../grade/GradeModal.vue";

export default {
    name: "grade",
    components: { GradeModal },
    props: {
        grade: {
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
        this.$eventHub.on('grade-updated', () => window.location.reload());
    },
    methods: {
        editGrade() {
            this.globalStore.showModal('grade-modal', this.grade);
        },
    },
}
</script>