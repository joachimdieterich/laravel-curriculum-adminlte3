<template>
    <div class="row">
        <div class="col-lg-4 col-sm-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div class="card-title">
                        <h5 class="m-0">
                            <i class="fa fa-university mr-1"></i>
                            {{ this.certificate.title }}
                        </h5>
                    </div>
                    <div
                        v-permission="'certificate_edit'"
                        class="card-tools pr-2">
                        <a  @click="editCertificate(certificate)">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <strong><i class="fa fa-file-alt mr-1"></i>
                        {{ trans('global.certificate.fields.description') }}
                    </strong>
                    <p class="text-muted"
                       v-dompurify-html="certificate.description"></p>
                    <hr>

                    <strong>
                        <i class="fas fa-layer-group mr-1"></i>
                        {{ trans('global.certificate.type') }}
                    </strong>
                    <p class="text-muted">
                        {{ certificate.type }}
                    </p>
                    <hr>
                    <strong>
                        <i class="fas fa-magnifying-glass mr-1"></i>
                        {{ trans('global.certificate.example') }}
                    </strong>
                    <p class="text-muted">
                        Parameter
                    </p>
                </div>

                <div class="card-footer">
                    <small class="float-right">
                        {{ certificate.updated_at }}
                    </small>
                </div>
            </div>
        </div>

            <div class="col-lg-8 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title px-1">
                            {{ trans('global.preview') }}
                        </div>
                    </div>
                    <div class="card-body"
                    style="position:relative;">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="example">
                                <div v-dompurify-html="certificate.body"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <Teleport to="body">
            <CertificateModal></CertificateModal>
        </Teleport>
    </div>
</template>

<script>
import CertificateModal from "../certificate/CertificateModal";
import {useGlobalStore} from "../../store/global";

export default {
    name: "Certificate",
    components:{
        CertificateModal
    },
    props: {
        certificate: {
            default: null
        },
        status_definitions: {
            default: null
        },
    },
    setup () {
        const globalStore = useGlobalStore();
        return {
            globalStore
        }
    },
    data() {
        return {
            componentId: this.$.uid,
        }
    },
    mounted() {
        this.$eventHub.on('certificate-updated', (certificate) => {
            this.globalStore?.closeModal('certificate-modal');
            window.location.reload();
        });
    },
    methods: {
        editCertificate(certificate){
            this.globalStore?.showModal('certificate-modal', certificate);
        },
    }
}
</script>
