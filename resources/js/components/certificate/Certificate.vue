<template>
    <div class="d-flex flex-wrap px-3 mb-lg-3">
        <div class="col-lg-4 col-12 mb-3 mb-lg-0 rounded-3 shadow-layout">
            <div class="d-flex align-items-center justify-content-between p-2 text-bg-primary rounded-top-3">
                <h5 class="m-0">
                    <i class="fa fa-university mx-1"></i>
                    {{ this.certificate.title }}
                </h5>

                <button v-if="checkPermission('certificate_edit')"
                    type="button"
                    class="btn btn-icon-alt"
                    @click="editCertificate()"
                >
                    <i class="fa fa-pencil-alt"></i>
                </button>
            </div>

            <div class="px-3 py-2 bg-white rounded-bottom-3">
                <strong>
                    <i class="fa fa-file-alt me-1"></i>
                    {{ trans('global.certificate.fields.description') }}
                </strong>
                <p class="text-muted">{{ certificate.description }}</p>

                <hr>

                <strong>
                    <i class="fas fa-layer-group me-1"></i>
                    {{ trans('global.certificate.type') }}
                </strong>
                <p class="text-muted">{{ certificate.type }}</p>

                <small class="text-muted">{{ certificate.updated_at }}</small>
            </div>
        </div>

        <div class="col-lg-8 col-12 ps-lg-3 mb-3 mb-lg-0">
            <div class="bg-white rounded-3 shadow-layout">
                <div class="fs-5 p-2 border-bottom border-dark-subtle">
                    {{ trans('global.preview') }}
                </div>

                <div class="p-3">
                    <div class="p-margin-0" v-html="certificate.body"></div>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <CertificateModal/>
        </Teleport>
    </div>
</template>
<script>
import CertificateModal from "../certificate/CertificateModal.vue";

export default {
    name: "Certificate",
    components: { CertificateModal },
    props: {
        certificate: {
            type: Object,
            default: null,
        },
    },
    mounted() {
        this.$eventHub.on('certificate-updated', () => window.location.reload());
    },
    methods: {
        editCertificate(certificate) {
            this.globalStore.showModal('certificate-modal', certificate);
        },
    },
}
</script>