<template>
    <div class="row">
        <div class="col-lg-4 col-sm-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div class="card-title">
                        <h5 class="m-0">
                            <i class="fas fa-user-tag mr-1"></i>
                            {{ this.currentRole.title }}
                        </h5>
                    </div>
<!--                    <div
                        v-permission="'organization_edit'"
                        class="card-tools pr-2">
                        <a  @click="editRole()">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    </div>-->
                </div>

                <div class="card-body">

                </div>

                <div class="card-footer">
                    <small class="float-right">
                        {{ this.currentRole.updated_at }}
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
                                <div v-for="permission in role.permissions "
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
            <RoleModal
                :show="this.showRoleModal"
                @close="this.showRoleModal = false"
                :params="this.currentRole"
            ></RoleModal>
        </Teleport>
    </div>
</template>

<script>
import RoleModal from "../role/RoleModal";

export default {
    name: "role",
    components:{
        RoleModal
    },
    props: {
        role: {
            default: null
        },
    },
    data() {
        return {
            componentId: this._uid,
            showRoleModal: false,
            currentRole: {},
        }
    },
    mounted() {
        this.currentRole = this.role;
        this.$eventHub.on('role-updated', (role) => {
            this.currentRole = role;
            this.showRoleModal = false;
        });

    },
    methods: {
        editRole(){
            this.showRoleModal = true;
        },
    }
}
</script>
