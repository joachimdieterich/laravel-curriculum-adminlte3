<template>
    <div class="px-3">
        <div class="col-lg-4 col-12 mb-3 rounded-3 shadow-layout">
            <div class="d-flex align-items-center justify-content-between p-2 text-bg-primary rounded-top-3">
                <h5 class="m-0">
                    <i class="fa fa-swatchbook me-1"></i>{{ subject.title }}
                </h5>

                <button v-if="checkPermission('subject_edit')"
                    type="button"
                    class="btn btn-icon-alt"
                    @click="editSubject()"
                >
                    <i class="fa fa-pencil-alt"></i>
                </button>
            </div>

            <div class="px-3 py-2 bg-white rounded-bottom-3">
                <strong>{{ trans('global.subject.title_singular') }}</strong>
                <p class="text-muted">{{ subject.title }}</p>

                <hr>

                <strong>{{ trans('global.subject.fields.title_short') }}</strong>
                <p class="text-muted">{{ subject.title_short }}</p>

                <div class="text-muted">{{ subject.updated_at }}</div>
            </div>
        </div>

        <Teleport to="body">
            <SubjectModal/>
        </Teleport>
    </div>
</template>
<script>
import SubjectModal from "../subject/SubjectModal.vue";

export default {
    name: "Subject",
    components: { SubjectModal },
    props: {
        subject: {
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
        this.$eventHub.on('subject-updated', () => window.location.reload());
    },
    methods: {
        editSubject(){
            this.globalStore.showModal('subject-modal', this.subject);
        },
    },
}
</script>