<template>
    <div>
        <div v-if="user.contact_detail">
            <div class="d-flex align-items-center gap-2">
                <h5 class="m-0">
                    <i class="far fa-id-card me-1"></i>
                    {{ contactDetail.owner.firstname }} {{ contactDetail.owner.lastname }}
                </h5>

                <div v-if="$userId == contactDetail.owner_id || checkPermission('is_admin')"
                    class="d-print-none d-contents"
                >
                    <button
                        v-permission="'contactdetail_edit'"
                        type="button"
                        class="btn btn-icon text-secondary"
                        @click="edit()"
                    >
                        <i class="fa fa-pencil-alt"></i>
                    </button>
                    <button
                        v-permission="'contactdetail_delete'"
                        type="button"
                        class="btn btn-icon text-danger"
                        @click="destroy()"
                    >
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>

            <strong>
                <i class="fa fa-envelope me-1"></i>
                {{ trans('global.contactDetail.fields.email')}}
            </strong>
            <p class="text-muted">{{ contactDetail.email }}</p>

            <hr>

            <strong>
                <i class="fa fa-phone me-1"></i>
                {{ trans('global.contactDetail.fields.phone')}}
            </strong>
            <p class="text-muted">{{ contactDetail.phone }}</p>

            <hr>

            <strong>
                <i class="fa fa-mobile me-1"></i>
                {{ trans('global.contactDetail.fields.mobile')}}
            </strong>
            <p class="text-muted">{{ contactDetail.mobile }}</p>
            <hr>
            <strong>
                <i class="fa fa-clipboard me-1"></i>
                {{ trans('global.contactDetail.fields.notes')}}
            </strong>
            <p
                class="text-muted"
                v-html="contactDetail.notes"
            ></p>
        </div>
        <div v-else>
            <button v-if="$userId == user.id"
                v-permission="'contactdetail_create'"
                type="button"
                class="btn btn-primary"
                @click="create()"
            >
                {{ trans('global.contactDetail.create') }}
            </button>
        </div>

        <div v-if="organization"
            class="pt-2"
        >
            <h5>{{ organization.title }}</h5>

            <hr>

            <strong>
                <i class="fa fa-map-marker me-1"></i>
                {{ trans('global.place') }}
            </strong>
            <p class="text-muted">
                {{ organization.street }}<br>
                {{ organization.postcode }} {{ organization.city }}<br>
                {{ organization.state?.lang_de }}, {{ organization.country?.lang_de }}
            </p>

            <hr>

            <strong>
                <i class="fa fa-phone me-1"></i>
                {{ trans('global.contactDetail.title_singular') }}
            </strong>
            <p class="text-muted mb-0">
                {{ trans('global.organization.fields.phone') }}: {{ organization.phone }}<br>
                {{ trans('global.organization.fields.email') }}: {{ organization.email }}
            </p>
        </div>

        <Teleport to="body">
            <ContactModal/>
        </Teleport>
    </div>
</template>
<script>
import ContactModal from "./ContactModal.vue";

export default {
    name: "ContactDetail",
    components: { ContactModal },
    props: {
        user: {
            type: Object,
            default: null,
        },
        contactDetail: {
            type: Object,
            default: null,
        },
        organization: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            componentId: this.$.uid,
            currentContactDetail: {},
        }
    },
    mounted() {
        this.currentContactDetail = this.contactDetail;

        this.$eventHub.on('contactDetail-added', contact => {
            window.location.reload();
        });
        this.$eventHub.on('contactDetail-updated', contact => {
            window.location.reload();
        });
    },
    methods: {
        create() {
            this.globalStore.showModal('contact-modal', {});
        },
        edit() {
            this.globalStore.showModal('contact-modal', this.currentContactDetail);
        },
        destroy() {
            axios.delete('/contactDetails/' + this.currentContactDetail.id)
                .then(res => {
                    window.location.reload();
                })
                .catch(e => {
                    console.log(e);
                });
        },
    },
}
</script>