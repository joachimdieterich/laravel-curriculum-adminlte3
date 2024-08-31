<template>
    <div class="row">
        <div class="col-lg-4 col-sm-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div class="card-title">
                        <h5 class="m-0">
                            <i class="fas fa-layer-group mr-1"></i>
                            {{ this.currentGrade.title }}
                        </h5>
                    </div>
                    <div
                        v-permission="'organization_edit'"
                        class="card-tools pr-2">
                        <a  @click="editGrade()">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    </div>

                </div>

                <div class="card-body">

                    <p class="text-muted">
                        {{ trans('global.grade.fields.external_begin') }}: {{ this.currentGrade.external_begin }}<br>
                        {{ trans('global.grade.fields.external_end') }}: {{ this.currentGrade.external_end }}
                    </p>
                    <hr>

                    <strong>
                        <i class="fas fa-city mr-1"></i>
                        {{ trans('global.organizationType.title_singular') }}
                    </strong>
                    <p class="text-muted">
                        {{ this.currentGrade.organization_type?.title }}
                    </p>
                    <hr>
                </div>

                <div class="card-footer">
                    <small class="float-right">
                        {{ this.currentGrade.updated_at }}
                    </small>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <GradeModal
                :show="this.showGradeModal"
                @close="this.showGradeModal = false"
                :params="this.currentGrade"
            ></GradeModal>
        </Teleport>
    </div>
</template>

<script>
import GradeModal from "../grade/GradeModal";

export default {
    name: "grade",
    components:{
        GradeModal
    },
    props: {
        grade: {
            default: null
        },
    },
    data() {
        return {
            componentId: this._uid,
            showGradeModal: false,
            currentGrade: {},
        }
    },
    mounted() {
        this.currentGrade = this.grade;
        this.$eventHub.on('grade-updated', (grade) => {
            this.currentGrade = grade;
            this.showGradeModal = false;
        });

    },
    methods: {
        editGrade(){
            this.showGradeModal = true;
        },
    }
}
</script>
