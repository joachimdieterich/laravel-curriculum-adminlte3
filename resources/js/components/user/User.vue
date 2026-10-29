<template>
    <div class="d-flex flex-wrap px-3 mb-lg-3">
        <div class="col-lg-4 col-12 mb-3 mb-lg-0">
            <div class="bg-white rounded-3 shadow-layout">
                <div class="d-flex align-items-center justify-content-between p-2 text-bg-primary rounded-top-3">
                    <h5 class="m-0">
                        <i class="fa fa-user mx-1"></i>{{ user.firstname }} {{ user.lastname }}
                    </h5>
    
                    <button v-if="checkPermission('user_edit')"
                        type="button"
                        class="btn btn-icon-alt"
                        @click="editUser(user)"
                    >
                        <i class="fa fa-pencil-alt"></i>
                    </button>
                </div>
    
                <div class="d-flex flex-column gap-2 p-3">
                    <div class="d-flex flex-column align-items-center">
                        <Avatar
                            :medium="user.avatar"
                            :show-popup-details="false"
                        />
    
                        <span class="fs-4">{{ user.firstname }} {{ user.lastname }}</span>
    
                        <span class="text-muted">{{ user.username }}</span>
                    </div>
    
                    <div v-if="user.organizations.length > 1"
                        class="d-contents"    
                    >
                        <Select2
                            id="user-select-organization"
                            url="/organizations"
                            model="organization"
                            :label="trans('global.organization.set')"
                            :placeholder="user.organizations.find(org => org.id === user.current_organization_id).title"
                            :list="user.organizations.map(org => { return { id: org.id, title: org.title } })"
                            @selectedValue="(id) => setCurrentOrganization(id[0])"
                        />
                        <hr class="m-0">
                    </div>
    
                    <div>
                        <strong>
                            <i class="fa fa-university me-1"></i>
                            {{ trans('global.organization.title_singular') }}
                        </strong>
                        <ul>
                            <li v-for="organization in user.organizations"
                                class="small"
                            >
                                {{ organization.title }} @ {{ getRoleInOrganization(organization)[0]?.title }}
                            </li>
                        </ul>
                        <hr class="m-0">
                    </div>
    
                    <div>
                        <strong>
                            <i class="fa fa-users me-1"></i>
                            {{ trans('global.group.title_singular') }}
                        </strong>
                        <ul>
                            <li v-for="group in user.groups"
                                class="small"
                            >
                                {{ group.title }} @ {{ getOrganizationOfGroup(group)[0]?.title }}
                            </li>
                        </ul>
                        <hr class="m-0">
                    </div>
    
                    <div>
                        <strong>
                            <i class="fa fa-user-tag me-1"></i>
                            {{ trans('global.role.title') }}
                        </strong>
                        <ul class="ps-4">
                            <li v-for="role in user.roles"
                                class="small"
                            >
                                {{ role.title }} @ {{ getOrganizationForRole(role)[0]?.title }}
                            </li>
                        </ul>
                    </div>
    
                    <small>{{ user.updated_at }}</small>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-12 ps-lg-3 mb-3 mb-lg-0">
            <div class="bg-white rounded-3 shadow-layout">
                <div class="p-2 border-bottom border-dark-subtle">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a
                                class="nav-link active show"
                                href="#contact"
                                data-bs-toggle="tab"
                            >
                                {{ trans('global.contactDetail.title_singular') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link show"
                                href="#notes"
                                data-bs-toggle="tab"
                            >
                                {{ trans('global.note.title') }}
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="tab-content p-3">
                    <div
                        id="contact"
                        class="tab-pane fade active show"
                    >
                        <ContactDetail
                            :user="user"
                            :contactDetail="user.contact_detail"
                            :organization="getCurrentOrganization()"
                        />
                    </div>
                    <div
                        id="notes"
                        class="tab-pane fade"
                    >
                        <Notes
                            notable_type="App\User"
                            :notable_id="user.id"
                            :show_tabs=false
                        />
                    </div>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <UserModal/>
        </Teleport>
    </div>
</template>
<script>
import UserModal from "../user/UserModal.vue";
import Avatar from "../uiElements/Avatar.vue";
import Notes from "../note/Notes.vue";
import Select2 from "../forms/Select2.vue";
import ContactDetail from "../contactDetail/ContactDetail.vue";

export default {
    name: "User",
    components: {
        ContactDetail,
        Avatar,
        UserModal,
        Notes,
        Select2,
    },
    props: {
        user: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            componentId: this.$.uid,
        }
    },
    mounted() {
        this.$eventHub.on('user-updated', user => {
            window.location.reload();
        });
    },
    methods: {
        editUser(user) {
            this.globalStore.showModal('user-modal', user);
        },
        setCurrentOrganization(id) {
            axios.patch('/users/setCurrentOrganization', { current_organization_id: id })
                .then(() => window.location.reload());
        },
        getRoleInOrganization(organization) {
            return this.user.roles.filter((r) => r.pivot.organization_id == organization.id);
        },
        getOrganizationOfGroup(group) {
            return this.user.organizations.filter((o) => o.id == group.organization_id);
        },
        getOrganizationForRole(role) {
            return this.user.organizations.filter((o) => o.id == role.pivot.organization_id);
        },
        getCurrentOrganization() {
            return this.user.organizations.filter((o) => o.id == this.user.current_organization_id)[0];
        },
    },
}
</script>