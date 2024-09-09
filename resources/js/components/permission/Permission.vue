<template>
    <div class="row">
        <div class="col-lg-4 col-sm-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div class="card-title">
                        <h5 class="m-0">
                            <i class="fas fa-user-tag mr-1"></i>
                            {{ this.currentPermission.title }}
                        </h5>
                    </div>
                    <div
                        v-permission="'organization_edit'"
                        class="card-tools pr-2">
                        <a  @click="editPermission()">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body">

                </div>

                <div class="card-footer">
                    <small class="float-right">
                        {{ this.currentPermission.updated_at }}
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title px-1">
                        {{ trans('global.permission.title') }}
                    </div>
                </div>
                <div class="card-body"
                     style="position:relative;">
                    <div class="tab-content">
                        <div class="tab-pane active show">
                            <div class="row">
                                <div v-for="permission in permission.permissions "
                                     class="col-3">
                                    <ul class=" btn btn-block btn-secondary btn-xs">
                                        {{ permission.title }}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <Teleport to="body">
            <PermissionModal
                :show="this.showPermissionModal"
                @close="this.showPermissionModal = false"
                :params="this.currentPermission"
            ></PermissionModal>
        </Teleport>
    </div>
</template>

<script>
import PermissionModal from "../permission/PermissionModal";

export default {
    name: "permission",
    components:{
        PermissionModal
    },
    props: {
        permission: {
            default: null
        },
    },
    data() {
        return {
            componentId: this.$.uid,
            showPermissionModal: false,
            currentPermission: {},
        }
    },
    mounted() {
        this.currentPermission = this.permission;
        this.$eventHub.on('permission-updated', (permission) => {
            this.currentPermission = permission;
            this.showPermissionModal = false;
        });

    },
    methods: {
        editPermission(){
            this.showPermissionModal = true;
        },
    }
}
</script>
