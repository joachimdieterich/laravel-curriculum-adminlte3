<template>
    <div class="row">
        <div class="col-lg-4 col-sm-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div class="card-title">
                        <h5 class="m-0">
                            <i class="fa fa-user mr-1"></i>
                            {{ this.user.firstname }} {{ this.user.lastname }}
                        </h5>
                    </div>
                    <div
                        v-permission="'user_edit'"
                        class="card-tools pr-2">
                        <a  @click="editUser(user)">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body box-profile">
                    <div class="text-center">
                        <avatar
                            :medium_id="this.user.medium_id"
                            :css="'clearfix'">
                        </avatar>
                    </div>

                    <h3 class="profile-username text-center">
                        {{ this.user.firstname }} {{ this.user.lastname }}
                    </h3>

                    <p class="text-muted text-center">
                        {{ this.user.username }}
                    </p>
                </div>

                <div class="card-body">
                    <strong><i class="fa fa-university mr-1"></i>
                        {{ trans('global.organization.title_singular') }}
                    </strong>
                    <ul class="pl-4">
                        <li v-for="organization in user.organizations"
                           class="small">
                            {{ organization.title}} @ {{ getRoleInOrganization(organization)[0].title }}
                        </li>
                    </ul>
                    <hr>

                    <strong><i class="fa fa-users mr-1"></i>
                        {{ trans('global.group.title_singular') }}
                    </strong>
                    <ul class="pl-4">
                        <li v-for="group in user.groups"
                            class="small">
                            {{ group.title}} @ {{ getOrganizationOfGroup(group)[0].title }}
                        </li>
                    </ul>
                    <hr>

                    <strong><i class="fas fa-user-tag mr-1"></i>
                        {{ trans('global.roles') }}
                    </strong>
                    <ul class="pl-4">
                        <li v-for="role in user.roles"
                            class="small">
                            {{ role.title}} @ {{ getOrganizationForRole(role)[0].title }}
                        </li>
                    </ul>
                </div>

                <div class="card-footer">
                    <small class="float-right">
                        {{ user.updated_at }}
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-sm-12">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active show"
                               href="#contact"
                               data-toggle="tab">
                                {{ trans('global.contactDetail.title_singular') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link show"
                               href="#notes"
                               data-toggle="tab">
                                {{ trans('global.note.title') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active show"
                             id="contact">
                            <ContactDetail
                                :user="user"
                                :contactDetail="user.contact_detail"
                                :organization="getCurrentOrganization()"
                            >
                            </ContactDetail>
                        </div>
                        <div class="tab-pane show"
                             id="notes">
                            <Notes notable_type="App\User"
                                   :notable_id="user.id"
                                   :show_tabs=false ></Notes>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <UserModal ></UserModal>
        </Teleport>
    </div>
</template>

<script>
import UserModal from "../user/UserModal.vue";
import Avatar from "../uiElements/Avatar.vue";
import Notes from "../note/Notes.vue";
import ContactDetail from "../contactDetail/ContactDetail.vue";
import {useDatatableStore} from "../../store/datatables";
import {useGlobalStore} from "../../store/global";


export default {
    name: "User",
    components:{
        ContactDetail,
        Avatar,
        UserModal,
        Notes
    },
    props: {
        user: {
            default: null
        },
        status_definitions: {
            default: null
        },
    },
    setup () {
        const globalStore = useGlobalStore();
        return {
            globalStore
        }
    },
    data() {
        return {
            componentId: this.$.uid,
        }
    },
    mounted() {
        this.$eventHub.on('user-updated', (user) => {
            this.globalStore?.closeModal('user-modal');
            window.location.reload();
        });
    },
    methods: {
        editUser(user){
            this.globalStore?.showModal('user-modal', user);
        },
        getRoleInOrganization(organization){
            //console.log(this.user.roles.filter((r) => r.pivot.organization_id == organization.id));
            return this.user.roles.filter((r) => r.pivot.organization_id == organization.id);
        },
        getOrganizationOfGroup(group){
            return this.user.organizations.filter((o) => o.id == group.organization_id);
        },
        getOrganizationForRole(role){
            return this.user.organizations.filter((o) => o.id == role.pivot.organization_id);
        },
        getCurrentOrganization(){
            return this.user.organizations.filter((o) => o.id == this.user.current_organization_id)[0];
        }
    }
}
</script>
