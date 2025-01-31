<template>
    <div class="row">
        <div class="col-lg-4 col-sm-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div class="card-title">
                        <h5 class="m-0">
                            <i class="fa fa-university mr-1"></i>
                            {{ organizationType.title }}
                        </h5>
                    </div>
                    <div
                        v-permission="'organization_type_edit'"
                        class="card-tools pr-2">
                        <a @click="editOrganizationType()">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <strong>
                        <i class="fas fa-link mr-1"></i>
                        {{ trans('global.organizationType.fields.external_id') }}
                    </strong>
                    <p class="text-muted">
                        {{ organizationType.external_id }}
                    </p>
                    <hr>

                    <strong>
                        <i class="fa fa-map-marker mr-1"></i>
                        {{ trans('global.place') }}
                    </strong>
                    <p class="text-muted">
                        {{ organizationType.state.lang_de }}
                        {{ organizationType.country.lang_de }}
                    </p>
                </div>

                <div class="card-footer">
                    <small class="float-right">
                        {{ organizationType.updated_at }}
                    </small>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <OrganizationTypeModal/>
        </Teleport>
    </div>
</template>
<script>
import OrganizationTypeModal from "../organizationType/OrganizationTypeModal.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: "OrganizationType",
    components:{
        OrganizationTypeModal,
    },
    props: {
        organizationType: {
            type: Object,
            default: null,
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
        }
    },
    mounted() {},
    methods: {
        editOrganizationType() {
            this.globalStore?.showModal('organizationtype-modal', this.organizationType);
        },
    },
}
</script>