<template>
    <div >
        <div v-if="user.contact_detail"
            class="card">
            <div class="card-header">
                <div class="card-title">
                    <h5 class="m-0">
                        <i class="far fa-id-card mr-1"></i>
                        {{ contactDetail.owner.firstname }} {{ contactDetail.owner.lastname }}
                    </h5>
                </div>
                <div v-if="$userId == contactDetail.owner_id"
                     v-permission="'contactdetail_delete'"
                     class="card-tools no-print">
                    <a href="#"
                       class="link-muted"
                       @click="destroy()">
                        <i class="fas fa-trash text-danger"></i>
                    </a>
                </div>
                <div v-if="$userId == contactDetail.owner_id"
                     v-permission="'contactdetail_edit'"
                     class="card-tools mr-3 no-print">
                    <a  href="#"
                        class="link-muted"
                        @click="edit()">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                </div>

            </div>

            <div class="card-body">
                <strong>
                    <i class="fas fa-envelope mr-1"></i>
                    {{ trans('global.contactDetail.fields.email')}}
                </strong>
                <p class="text-muted">{{ contactDetail.email }}</p>

                <hr>

                <strong>
                    <i class="fas fa-phone mr-1"></i>
                    {{ trans('global.contactDetail.fields.phone')}}
                </strong>
                <p class="text-muted">{{ contactDetail.phone }}</p>

                <hr>

                <strong><i class="fa fa-mobile mr-1"></i>
                    {{ trans('global.contactDetail.fields.mobile')}}
                </strong>
                <p class="text-muted">{{ contactDetail.mobile }}</p>
                <hr>
                <strong>
                    <i class="fa fa-clipboard mr-1"></i>
                    {{ trans('global.contactDetail.fields.notes')}}
                </strong>
                <p
                    class="text-muted"
                    v-dompurify-html="contactDetail.notes"></p>
            </div>
        </div>
        <div v-else>
            <button
                v-if="$userId == user.id"
                v-permission="'contactdetail_create'"
                id="contactDetail-create"
                class="btn btn-primary"
                @click="this.showContactModal = true" >
                {{ trans('global.contactdetail.create') }}
            </button>
        </div>

        <div v-if="organization"
             class="card-footer mt-2">
            <h5>{{ organization.title }}</h5>

            <hr>

            <strong><i class="fa fa-map-marker mr-1"></i>
                {{ trans('global.place') }}
            </strong>
            <p class="text-muted">
                {{ organization.street }}<br>
                {{ organization.postcode }} {{ organization.city }}<br>
                {{ organization.state?.lang_de }}, {{ organization.country?.lang_de }}
            </p>
            <hr>

            <strong>
                <i class="fa fa-phone mr-1"></i>
                {{ trans('global.contactDetail.title_singular') }}
            </strong>
            <p class="text-muted">
                {{ trans('global.organization.fields.phone') }}: {{ organization.phone }}<br>
                {{ trans('global.organization.fields.email') }}: {{ organization.email }}
            </p>
        </div>
        <Teleport to="body">
            <ContactModal
                :show="this.showContactModal"
                @close="this.showContactModal = false"
                :params="currentContactDetail">
            </ContactModal>
        </Teleport>
    </div>
</template>

<script>
import ContactModal from "./ContactModal.vue";
import KanbanModal from "../kanban/KanbanModal.vue";

export default {
    name: "ContactDetail",
    components:{
        KanbanModal,
        ContactModal
    },
    props: {
        user: {
            default: null
        },
        contactDetail: {
            default: null
        },
        organization: {
            default:null
        }

    },
    data() {
        return {
            componentId: this._uid,
            showContactModal: false,
            currentContactDetail: {},
        }
    },
    mounted() {
        this.currentContactDetail = this.contactDetail;
        this.$eventHub.on('contactDetail-added', (contact) => {
            this.showContactModal = false;
            //this.currentContactDetail = contact;
            window.location.reload();
        });
        this.$eventHub.on('contactDetail-updated', (contact) => {
            this.showContactModal = false;
            window.location.reload();
        });
    },
    methods: {
        edit(){
            this.showContactModal = true;
        },
        destroy(){
            axios.delete('/contactDetails/' + this.currentContactDetail.id)
                .then(res => {
                    window.location.reload();
                })
                .catch(e => {
                    console.log(e);
                });
        }
    }
}

</script>

<style scoped>

</style>
