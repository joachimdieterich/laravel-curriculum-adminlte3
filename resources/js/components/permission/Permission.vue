<template>
    <div class="px-3">
        <div class="col-lg-4 col-12 rounded-3 shadow-layout">
            <div class="d-flex align-items-center justify-content-between p-2 text-bg-primary rounded-top-3">
                <h5 class="m-0">
                    <i class="fa fa-user-tag mx-1"></i>{{ currentPermission.title }}
                </h5>

                <button v-if="checkPermission('is_admin')"
                    type="button"
                    class="btn btn-icon-alt"
                    @click="editPermission()"
                >
                    <i class="fa fa-pencil-alt"></i>
                </button>
            </div>
            <div class="p-2 bg-white rounded-bottom-3">
                <small>{{ currentPermission.updated_at }}</small>
            </div>
        </div>

        <Teleport to="body">
            <PermissionModal/>
        </Teleport>
    </div>
</template>
<script>
import PermissionModal from "../permission/PermissionModal.vue";

export default {
    name: "permission",
    components:{ PermissionModal },
    props: {
        permission: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            componentId: this.$.uid,
            currentPermission: {},
        }
    },
    mounted() {
        this.currentPermission = this.permission;
        this.$eventHub.on('permission-updated', permission => {
            this.currentPermission = permission;
        });
    },
    methods: {
        editPermission() {
            this.globalStore.showModal('permission-modal', this.currentPermission);
        },
    },
}
</script>