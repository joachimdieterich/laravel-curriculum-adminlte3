<template>
    <div class="row">
        <div class="col-lg-4 col-sm-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div class="card-title">
                        <h5 class="m-0">
                            <i class="fa fa-university mr-1"></i>
                            {{ this.currentOrganization.title }}
                        </h5>
                    </div>
                    <div
                        v-permission="'organization_edit'"
                        class="card-tools pr-2">
                        <a  @click="editOrganization(false, false)">
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
                        {{ this.currentOrganization.type?.title }}
                    </p>
                    <hr>

                    <strong>
                        <i class="fa fa-map-marker mr-1"></i>
                        {{ trans('global.place') }}
                    </strong>
                    <a v-permission="'organization_edit_address'"
                        class="pull-right link-muted"
                       @click="editOrganization(true, false)" >
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <p class="text-muted">
                        {{ this.currentOrganization.street }}<br>
                        {{ this.currentOrganization.postcode }} {{ this.currentOrganization.city }}<br>
                        {{ this.currentOrganization.state?.lang_de }}, {{ this.currentOrganization.country?.lang_de }}
                    </p>
                    <hr>

                    <strong><i class="fa fa-phone mr-1"></i>
                        {{ trans('global.contactdetail.title_singular') }}
                    </strong>
                    <p class="text-muted">
                        {{ trans('global.organization.fields.phone') }}: {{ this.currentOrganization.phone }}<br>
                        {{ trans('global.organization.fields.email') }}: {{ this.currentOrganization.email }}
                    </p>
                    <hr>

                    <strong><i class="fa fa-graduation-cap mr-1"></i>
                        {{ trans('global.lms.title_singular') }}-URL
                    </strong>
                    <a v-permission="'organization_edit_address'"
                        class="pull-right link-muted"
                       @click="editOrganization(false, true)" >
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <p class="text-muted">
                        {{ this.currentOrganization.lms_url }}
                    </p>
                    <hr>

                    <strong><i class="fa fa-file-alt mr-1"></i>
                        {{ trans('global.organization.fields.description') }}
                    </strong>
                    <p class="text-muted"
                        v-dompurify-html="this.currentOrganization.description"></p>
                </div>

                <div class="card-footer">
                    <div class="float-left">
                    <span
                        class="btn-xs btn-block pull-right">
                        {{ status_definitions[this.currentOrganization.status_id]?.lang_de }}
                    </span>
                    </div>
                    <small class="float-right">
                        {{ this.currentOrganization.updated_at }}
                    </small>
                </div>
            </div>
        </div>

<!--            <div class="col-lg-8 col-sm-12">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link active show"
                                   href="#activity"
                                   data-toggle="tab">
                                    Activity
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link"
                                   href="#timeline"
                                   data-toggle="tab">
                                    Timeline
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link"
                                   href="#settings"
                                   data-toggle="tab">
                                    Settings
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="activity">

                                Activity Tab

                            </div>
                            <div class="tab-pane" id="timeline">
                                timeline
                            </div>

                            <div class="tab-pane" id="settings">
                                Organisational Settings
                            </div>
                        </div>
                    </div>
                </div>
            </div>-->

        <Teleport to="body">
            <OrganizationModal
            ></OrganizationModal>
        </Teleport>
    </div>
</template>

<script>
import OrganizationModal from "../organization/OrganizationModal.vue";
import {useGlobalStore} from "../../store/global.js";

export default {
    name: "Organization",
    components:{
        OrganizationModal
    },
    props: {
        organization: {
            default: null
        },
        status_definitions: {
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
        editOrganization(onlyAddress, onlyLmsUrl){
            this.globalStore?.showModal('organization-modal', {
                'id': this.currentOrganization.id,
                'common_name':this.currentOrganization.common_name,
                'title': this.currentOrganization.title,
                'description': this.currentOrganization.description,
                'street': this.currentOrganization.street,
                'postcode': this.currentOrganization.postcode,
                'city': this.currentOrganization.city,
                'state_id': this.currentOrganization.state_id,
                'country_id': this.currentOrganization.country_id,
                'organization_type_id': this.currentOrganization.organization_type_id,
                'phone': this.currentOrganization.phone,
                'email': this.currentOrganization.email,
                'status_id': this.currentOrganization.status_id,
                'lms_url': this.currentOrganization.lms_url,
                'onlyAddress': onlyAddress ?? this.onlyAddress,
                'onlyLmsUrl' : onlyLmsUrl ?? this.onlyLmsUrl
            });
        },
    }
}
</script>
