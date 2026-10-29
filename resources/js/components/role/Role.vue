<template>
    <div class="px-3">
        <div class="col-lg-4 col-12 mb-3 rounded-3 shadow-layout">
            <div class="d-flex align-items-center justify-content-between p-2 text-bg-primary rounded-top-3">
                <h5 class="m-0">
                    <i class="fa fa-user-tag mx-1"></i>{{ role.title }}
                </h5>

                <button v-if="checkPermission('is_admin')"
                    type="button"
                    class="btn btn-icon-alt"
                    @click="editRole()"
                >
                    <i class="fa fa-pencil-alt"></i>
                </button>
            </div>

            <div class="p-2 bg-white rounded-bottom-3">
                <small>{{ role.updated_at }}</small>
            </div>
        </div>

        <div class="col-12 mb-3 bg-white rounded-3 shadow-layout">
            <div class="t-20 px-3 py-2 border-bottom">{{ trans('global.permission.title') }}</div>

            <div class="row align-items-center text-break px-3 py-1">
                <div v-for="permission in currentPermissions"
                    class="col-6 col-sm-4 col-md-3 col-lg-2 py-2"
                >
                    <button
                        type="button"
                        class="btn w-100"
                        :class="permission.checked ? 'btn-success' : 'btn-danger'"
                        @click="togglePermission(permission)"
                    >
                        {{ permission.title }}
                    </button>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <RoleModal/>
        </Teleport>
    </div>
</template>
<script>
import RoleModal from "../role/RoleModal.vue";

export default {
    name: "Role",
    components: { RoleModal },
    props: {
        role: {
            type: Object,
            default: null,
        },
        allPermissions: {
            type: Array,
            default: [],
        },
    },
    data() {
        return {
            componentId: this.$.uid,
            currentPermissions: [],
        }
    },
    mounted() {
        let counter = 0;
        let checkedPermissions = [];
        // mark permissions as checked if they are set for the current role
        for (let permission of this.allPermissions) {
            if (counter < this.role.permissions.length && this.role.permissions[counter].id === permission.id) {
                permission.checked = true;
                counter++;
            }
            checkedPermissions.push(permission);
        }

        this.currentPermissions = checkedPermissions;

        this.$eventHub.on('role-updated', () => window.location.reload());
    },
    methods: {
        editRole() {
            this.globalStore.showModal('role-modal', this.role);
        },
        togglePermission(permission) {
            axios.post('/roles/' + this.role.id + '/togglePermission/' + permission.id)
                .then(response => permission.checked = !permission.checked)
                .catch(e => console.error(e));
        },
    },
}
</script>