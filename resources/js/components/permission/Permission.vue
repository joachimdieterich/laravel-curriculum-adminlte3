<template>
    <div class="d-flex flex-column">
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
                        v-permission="'is_admin'"
                        class="card-tools pr-2"
                    >
                        <a @click="editPermission()">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body"></div>

                <div class="card-footer">
                    <small class="float-right">
                        {{ this.currentPermission.updated_at }}
                    </small>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <PermissionModal></PermissionModal>
        </Teleport>
    </div>
</template>

<script>
import PermissionModal from "../permission/PermissionModal.vue";
import {useGlobalStore} from "../../store/global";

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
    setup () {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            componentId: this.$.uid,
            currentPermission: {},
        }
    },
    mounted() {
        this.currentPermission = this.permission;
        this.$eventHub.on('permission-updated', (permission) => {
            this.currentPermission = permission;
            this.globalStore?.closeModal('permission-modal');
        });

    },
    methods: {
        editPermission(){
            this.globalStore?.showModal('permission-modal', this.currentPermission);
        },
    }
}
</script>
