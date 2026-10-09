<template>
    <div class="px-3">
        <div class="col-lg-4 col-12 mb-3 rounded-3 shadow-layout">
            <div class="d-flex align-items-center justify-content-between p-2 text-bg-primary rounded-top-3">
                <h5 class="m-0">
                    <i class="fa fa-university mx-1"></i>{{ organizationType.title }}
                </h5>

                <button v-if="checkPermission('organization_type_edit')"
                    type="button"
                    class="btn btn-icon-alt"
                    @click="editOrganizationType()"
                >
                    <i class="fa fa-pencil-alt"></i>
                </button>
            </div>                
                
            <div class="px-3 py-2 bg-white rounded-bottom-3">
                <strong>
                    <i class="fa fa-link me-1"></i>
                    {{ trans('global.organizationType.fields.external_id') }}
                </strong>
                <p class="text-muted">{{ organizationType.external_id }}</p>

                <hr>

                <strong>
                    <i class="fa fa-map-marker me-1"></i>
                    {{ trans('global.place') }}
                </strong>
                <p class="text-muted">
                    {{ organizationType.state.lang_de }}
                    {{ organizationType.country.lang_de }}
                </p>

                <small class="text-muted">{{ organizationType.updated_at }}</small>
            </div>
        </div>

        <Teleport to="body">
            <OrganizationTypeModal/>
        </Teleport>
    </div>
</template>
<script>
import OrganizationTypeModal from "../organizationType/OrganizationTypeModal.vue";

export default {
    name: "OrganizationType",
    components: { OrganizationTypeModal },
    props: {
        organizationType: {
            type: Object,
            default: null,
        },
    },
    methods: {
        editOrganizationType() {
            this.globalStore.showModal('organizationtype-modal', this.organizationType);
        },
    },
}
</script>