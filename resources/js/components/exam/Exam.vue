<template>
    <div class="row">
        <div class="col-lg-4 col-sm-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div class="card-title">
                        <h5 class="m-0">
                            <i class="fas fa-user-tag mr-1"></i>
                            {{ this.currentExam.title }}
                        </h5>
                    </div>
<!--                    <div
                        v-permission="'organization_edit'"
                        class="card-tools pr-2">
                        <a  @click="editExam()">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    </div>-->
                </div>

                <div class="card-body">

                </div>

                <div class="card-footer">
                    <small class="float-right">
                        {{ this.currentExam.updated_at }}
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title px-1">
                        {{ trans('global.permission.title') }}
                    </div>
                </div>
                <div class="card-body"
                     style="position:relative;">
                    <div class="tab-content">
                        <div class="tab-pane active show">
                            <div class="row">
                                <div v-for="permission in exam.permissions "
                                     class="col-3">
                                    <ul class=" btn btn-block btn-secondary btn-xs">
                                        {{ permission.title }}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <Teleport to="body">
            <ExamModal
                :show="this.showExamModal"
                @close="this.showExamModal = false"
                :params="this.currentExam"
            ></ExamModal>
        </Teleport>
    </div>
</template>

<script>
import ExamModal from "../exam/ExamModal.vue";

export default {
    name: "exam",
    components:{
        ExamModal
    },
    props: {
        exam: {
            default: null
        },
    },
    data() {
        return {
            componentId: this.$.uid,
            showExamModal: false,
            currentExam: {},
        }
    },
    mounted() {
        this.currentExam = this.exam;
        this.$eventHub.on('exam-updated', (exam) => {
            this.currentExam = exam;
            this.showExamModal = false;
        });

    },
    methods: {
        editExam(){
            this.showExamModal = true;
        },
    }
}
</script>
