<template>
    <div class="px-3">
        <div class="col-lg-4 col-sm-12">
            <div class="d-flex align-items-center justify-content-between p-2 text-bg-primary rounded-top-3">
                <h5 class="m-0">
                    <i class="fa fa-university mx-1"></i>{{ organization.title }}
                </h5>

                <button v-if="checkPermission('organization_edit')"
                    type="button"
                    class="btn btn-icon-alt"
                    @click="editOrganization()"
                >
                    <i class="fa fa-pencil-alt"></i>
                </button>
            </div>

            <div class="px-3 py-2 bg-white rounded-bottom-3">
                <strong>
                    <i class="fa fa-city me-1"></i>
                    {{ trans('global.organizationType.title_singular') }}
                </strong>
                <p class="text-muted">{{ organization.type?.title }}</p>

                <hr>

                <strong>
                    <i class="fa fa-map-marker me-1"></i>{{ trans('global.place') }}
                </strong>
                <p class="text-muted">
                    {{ organization.street }}<br/>
                    {{ organization.postcode }} {{ organization.city }}<br/>
                    {{ organization.state?.lang_de }}, {{ organization.country?.lang_de }}
                </p>

                <hr>

                <strong>
                    <i class="fa fa-phone me-1"></i>{{ trans('global.contactDetail.title_singular') }}
                </strong>
                <p class="text-muted">
                    {{ trans('global.organization.fields.phone') }}: {{ organization.phone }}<br/>
                    {{ trans('global.organization.fields.email') }}: {{ organization.email }}
                </p>

                <hr>

                <strong>
                    <i class="fa fa-graduation-cap me-1"></i>{{ trans('global.lms.title_singular') }}-URL
                </strong>
                <p class="text-muted">{{ organization.lms_url }}</p>

                <hr>

                <strong>
                    <i class="fa fa-file-alt me-1"></i>{{ trans('global.organization.fields.description') }}
                </strong>
                <div
                    class="text-muted"
                    v-html="organization.description ?? trans('global.no_description')"
                ></div>

                <div class="d-flex justify-content-between">
                    <small class="text-muted">{{ status_definitions[organization.status_id]?.lang_de }}</small>
                    <small class="text-muted">{{ organization.updated_at }}</small>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <OrganizationModal/>
        </Teleport>
    </div>
</template>
<script>
import OrganizationModal from "../organization/OrganizationModal.vue";

export default {
    name: "Organization",
    components: { OrganizationModal },
    props: {
        organization: {
            type: Object,
            default: null,
        },
        status_definitions: {
            type: Array,
            default: null,
        },
    },
    data() {
        return {
            componentId: this.$.uid,
            showOrganizationModal: false,
            onlyAddress: false,
            onlyLmsUrl: false,
        }
    },
    mounted() {
        this.$eventHub.on('organization-updated', () => window.location.reload());
    },
    methods: {
        editOrganization() {
            this.globalStore.showModal('organization-modal', this.organization);
        },
    },
}
</script>