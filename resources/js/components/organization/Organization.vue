<template>
    <div class="row">
        <div class="col-lg-4 col-sm-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div class="card-title">
                        <h5 class="m-0">
                            <i class="fa fa-university mr-1"></i>
                            {{ this.organization.title }}
                        </h5>
                    </div>
                    <div
                        v-permission="'organization_edit'"
                        class="card-tools pr-2">
                        <a  @click="editOrganization(organization, false, false)">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    </div>

                </div>

                <div class="card-body">
                    <strong>
                        <i class="fas fa-signal mr-1"></i>
                        {{ trans('global.organizationtype.title_singular') }}
                    </strong>
                    <p class="text-muted">
                        {{ organization.type.title }}
                    </p>
                    <hr>

                    <strong>
                        <i class="fa fa-map-marker mr-1"></i>
                        {{ trans('global.place') }}
                    </strong>
                    <a v-permission="'organization_edit_address'"
                        class="pull-right link-muted"
                       @click="editOrganization(organization, true, false)" >
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <p class="text-muted">
                        {{ organization.street }}<br>
                        {{ organization.postcode }} {{ organization.city }}<br>
                        {{ organization.state.lang_de }}, {{ organization.country.lang_de }}
                    </p>
                    <hr>

                    <strong><i class="fa fa-phone mr-1"></i>
                        {{ trans('global.contactdetail.title_singular') }}
                    </strong>
                    <p class="text-muted">
                        {{ trans('global.organization.fields.phone') }}: {{ organization.phone }}<br>
                        {{ trans('global.organization.fields.email') }}: {{ organization.email }}
                    </p>
                    <hr>

                    <strong><i class="fa fa-graduation-cap mr-1"></i>
                        {{ trans('global.lms.title_singular') }}-URL
                    </strong>
                    <a v-permission="'organization_edit_address'"
                        class="pull-right link-muted"
                       @click="editOrganization(organization, false, true)" >
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <p class="text-muted">
                        {{ organization.lms_url }}
                    </p>
                    <hr>

                    <strong><i class="fa fa-file-alt mr-1"></i>
                        {{ trans('global.organization.fields.description') }}
                    </strong>
                    <p class="text-muted"
                        v-html="organization.description"></p>
                </div>

                <div class="card-footer">
                    <div class="float-left">
                    <span
                        class="btn-xs btn-block pull-right">
                        {{ status_definitions[organization.status_id].lang_de }}
                    </span>
                    </div>
                    <small class="float-right">
                        {{ organization.updated_at }}
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
                :show="this.showOrganizationModal"
                @close="this.showOrganizationModal = false"
                :onlyAddress="this.onlyAddress"
                :onlyLmsUrl="this.onlyLmsUrl"
                :params="currentOrganization"
            ></OrganizationModal>
        </Teleport>
    </div>
</template>

<script>
import OrganizationModal from "../organization/OrganizationModal";

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
    data() {
        return {
            componentId: this._uid,
            showOrganizationModal: false,
            currentOrganization: {},
            onlyAddress: false,
            onlyLmsUrl: false,
        }
    },
    mounted() {},
    methods: {
        editOrganization(organization, onlyAddress, onlyLmsUrl){
            this.currentOrganization = organization;
            this.onlyAddress = onlyAddress ?? this.onlyAddress;
            this.onlyLmsUrl = onlyLmsUrl ?? this.onlyLmsUrl;
            this.showOrganizationModal = true;
        },
        editAddress(){}
    }

}
</script>

<style scoped>

</style>
