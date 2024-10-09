<template>
    <div class="row">
        <div class="col-lg-4 col-sm-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div class="card-title">
                        <h5 class="m-0">
                            <i class="fa fa-swatchbook mr-1"></i>
                            {{ this.currentSubject.title }}
                        </h5>
                    </div>
                    <div
                        v-permission="'organization_edit'"
                        class="card-tools pr-2">
                        <a  @click="editSubject()">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <strong>
                    {{ trans('global.subject.title_singular') }}
                    </strong>

                    <p class="text-muted">
                        {{ subject.title }}
                    </p>

                    <hr>
                    <strong>
                        {{ trans('global.subject.fields.title_short') }}
                    </strong>
                    <p class="text-muted">
                        {{ subject.title_short }}
                    </p>
                </div>

                <div class="card-footer">
                    <small class="float-right">
                        {{ this.currentSubject.updated_at }}
                    </small>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <SubjectModal></SubjectModal>
        </Teleport>
    </div>
</template>

<script>
import SubjectModal from "../subject/SubjectModal.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: "subject",
    components:{
        SubjectModal
    },
    props: {
        subject: {
            default: null
        },
    },
    setup () {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            componentId: this.$.uid,
            currentSubject: {},
        }
    },
    mounted() {
        this.currentSubject = this.subject;
        this.$eventHub.on('subject-updated', (subject) => {
            this.globalStore?.closeModal('subject-modal');
            this.currentSubject = subject;
        });

    },
    methods: {
        editSubject(){
            this.globalStore?.showModal('subject-modal', this.currentSubject);
        },
    }
}
</script>
