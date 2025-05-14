<template>
    <div class="row">
        <div class="col-lg-4 col-sm-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div class="card-title">
                        <h5 class="m-0">
                            <i class="fa fa-university mr-1"></i>
                            {{ currentOrganization.title }}
                        </h5>
                    </div>
                    <div
                        v-permission="'organization_edit'"
                        class="card-tools pr-2 pointer"
                    >
                        <a  @click="editOrganization()">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <strong>
                        <i class="fas fa-city mr-1"></i>
                        {{ trans('global.organizationType.title_singular') }}
                    </strong>
                    <p class="text-muted">
                        {{ currentOrganization.type?.title }}
                    </p>
                    <hr>

                    <strong>
                        <i class="fa fa-map-marker mr-1"></i>
                        {{ trans('global.place') }}
                    </strong>
                    <p class="text-muted">
                        {{ currentOrganization.street }}<br>
                        {{ currentOrganization.postcode }} {{ currentOrganization.city }}<br>
                        {{ currentOrganization.state?.lang_de }}, {{ currentOrganization.country?.lang_de }}
                    </p>
                    <hr>

                    <strong><i class="fa fa-phone mr-1"></i>
                        {{ trans('global.contactDetail.title_singular') }}
                    </strong>
                    <p class="text-muted">
                        {{ trans('global.organization.fields.phone') }}: {{ currentOrganization.phone }}<br>
                        {{ trans('global.organization.fields.email') }}: {{ currentOrganization.email }}
                    </p>
                    <hr>

                    <strong><i class="fa fa-graduation-cap mr-1"></i>
                        {{ trans('global.lms.title_singular') }}-URL
                    </strong>
                    <p class="text-muted">
                        {{ currentOrganization.lms_url }}
                    </p>
                    <hr>

                    <strong>
                        <i class="fa fa-file-alt mr-1"></i>
                        {{ trans('global.organization.fields.description') }}
                    </strong>
                    <p
                        class="text-muted"
                        v-html="currentOrganization.description ?? trans('global.no_description')"
                    ></p>
                </div>

                <div class="card-footer">
                    <div class="float-left">
                        <span class="btn-xs btn-block pull-right">
                            {{ status_definitions[currentOrganization.status_id]?.lang_de }}
                        </span>
                    </div>
                    <small class="float-right">
                        {{ currentOrganization.updated_at }}
                    </small>
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
import {useGlobalStore} from "../../store/global.js";

export default {
    name: "Organization",
    components: {
        OrganizationModal,
    },
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
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            componentId: this.$.uid,
            showOrganizationModal: false,
            currentOrganization: {},
            onlyAddress: false,
            onlyLmsUrl: false,
        }
    },
    mounted() {
        this.currentOrganization = this.organization;
        this.$eventHub.on('organization-updated', (organization) => {
            this.currentOrganization = organization;
            this.globalStore?.closeModal('organization-modal');
        });
    },
    methods: {
        editOrganization() {
            this.globalStore?.showModal('organization-modal', {
                id: this.currentOrganization.id,
                common_name:this.currentOrganization.common_name,
                title: this.currentOrganization.title,
                description: this.currentOrganization.description,
                street: this.currentOrganization.street,
                postcode: this.currentOrganization.postcode,
                city: this.currentOrganization.city,
                state_id: this.currentOrganization.state_id,
                country_id: this.currentOrganization.country_id,
                organization_type_id: this.currentOrganization.organization_type_id,
                phone: this.currentOrganization.phone,
                email: this.currentOrganization.email,
                status_id: this.currentOrganization.status_id,
                lms_url: this.currentOrganization.lms_url,
            });
        },
    },
}
</script>