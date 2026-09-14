<template>
    <div class="d-flex flex-wrap">
        <div class="col-lg-4 col-sm-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div class="card-title">
                        <h5 class="m-0">
                            <i class="fas fa-user-tag mr-1"></i>
                            {{ this.currentRole.title }}
                        </h5>
                    </div>
                    <div
                        v-permission="'is_admin'"
                        class="card-tools pr-2"
                    >
                        <a @click="editRole(this.currentRole)" role="button">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body"></div>

                <div class="card-footer">
                    <small class="float-right">
                        {{ this.currentRole.updated_at }}
                    </small>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title px-1">
                        {{ trans('global.permission.title') }}
                    </div>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active show">
                            <div class="row">
                                <div v-for="permission in currentPermissions"
                                    class="col-6 col-sm-4 col-md-3 col-lg-2 py-2"
                                >
                                    <button
                                        type="button"
                                        class="btn btn-block"
                                        :class="permission.checked ? 'btn-success' : 'btn-danger'"
                                        @click="togglePermission(permission)"
                                    >
                                        {{ permission.title }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <tag-card :tags="role.tags"/>

        <Teleport to="body">
            <RoleModal/>
        </Teleport>
    </div>
</template>
<script>
import RoleModal from "../role/RoleModal.vue";
import {useGlobalStore} from "../../store/global";
import TagCard from "../tag/TagCard.vue";

export default {
    name: "role",
    components: {
        RoleModal,
        TagCard,
    },
    props: {
        role: {
            default: null,
        },
        allPermissions: {
            type: Array,
            default: [],
        },
    },
    setup() { //use database store
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            componentId: this.$.uid,
            currentRole: {},
            currentPermissions: [],
        }
    },
    mounted() {
        this.currentRole = this.role;

        let counter = 0;
        let checkedPermissions = [];
        // mark permissions as checked if they are set for the current role
        for (let permission of this.allPermissions) {
            if (this.currentRole.permissions[counter].id === permission.id) {
                permission.checked = true;
                counter++;
            }
            checkedPermissions.push(permission);
        }

        this.currentPermissions = checkedPermissions;

        this.$eventHub.on('role-updated', (role) => {
            this.currentRole = role;
            window.location.reload(); //reload to get permissions
        });
    },
    methods: {
        editRole(role) {
            this.globalStore?.showModal('role-modal', role);
        },
        togglePermission(permission) {
            axios.post('/roles/' + this.currentRole.id + '/togglePermission/' + permission.id)
                .then(response => permission.checked = !permission.checked)
                .catch(error => {
                    console.error(error);
                });
        },
    },
}
</script>