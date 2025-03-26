<template >
    <div class="row">
        <div
            id="certificate-content"
            class="col-md-12 m-0"
        >
            <IndexWidget
                v-permission="'certificate_create'"
                key="'certificateCreate'"
                modelName="Certificate"
                url="/certificates"
                :create=true
                :label="trans('global.certificate.create')"
            />
            <IndexWidget v-for="certificate in certificates"
                :key="'certificateIndex'+certificate.id"
                :model="certificate"
                modelName="Certificate"
                url="/certificates"
            >
                <template v-slot:icon>
                    <i class="fa fa-university"></i>
                </template>

                <template v-slot:dropdown
                    v-permission="'certificate_edit, certificate_delete'"
                >
                    <div
                        class="dropdown-menu dropdown-menu-right"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            v-permission="'certificate_edit'"
                            :name="'edit-certificate-' + certificate.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editCertificate(certificate)"
                        >
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.certificate.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'certificate_delete'"
                            :id="'delete-certificate-' + certificate.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(certificate)"
                        >
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.certificate.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <div
            id="certificate-datatable-wrapper"
            class="w-100 dataTablesWrapper"
        >
            <DataTable
                id="certificate-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display: none;"
            />
        </div>

        <Teleport to="body">
            <CertificateModal/>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.certificate.delete')"
                :description="trans('global.certificate.delete_helper')"
                @close="() => {
                    this.showConfirm = false;
                }"
                @confirm="() => {
                    this.showConfirm = false;
                    this.destroy();
                }"
            />
        </Teleport>
    </div>
</template>
<script>
import CertificateModal from "../certificate/CertificateModal.vue";
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import {useGlobalStore} from "../../store/global";
DataTable.use(DataTablesCore);

export default {
    props: {},
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            certificates: null,
            search: '',
            showConfirm: false,
            url: '/certificates/list',
            errors: {},
            currentCertificate: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
                { title: 'description', data: 'description', searchable: true },
                { title: 'body', data: 'body', searchable: true },
                { title: 'curriculum_id', data: 'curriculum_id', searchable: true },
                { title: 'organization_id', data: 'organization_id', searchable: true },
                { title: 'owner_id', data: 'owner_id', searchable: true },
            ],
            options : this.$dtOptions,
            dt: null,
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('certificate-added', (certificate) => {
            this.certificates.push(certificate);
        });

        this.$eventHub.on('certificate-updated', (updatedCertificate) => {
            let certificate = this.certificates.find(c => c.id === updatedCertificate.id);

            Object.assign(certificate, updatedCertificate);
        });

        this.$eventHub.on('filter', (filter) => {
            this.dt.search(filter).draw();
        });
    },
    methods: {
        editCertificate(certificate)  {
            this.globalStore?.showModal('certificate-modal', certificate);
        },
        loaderEvent() {
            this.dt = $('#certificate-datatable').DataTable();
            this.dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.certificates = this.dt.rows({page: 'current'}).data().toArray();

                $('#curriculum-content').insertBefore('#certificate-datatable-wrapper');
            });
        },
        confirmItemDelete(certificate) {
            this.currentCertificate = certificate;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/certificates/' + this.currentCertificate.id)
                .then(res => {
                    let index = this.certificates.indexOf(this.currentCertificate);
                    this.certificates.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
    },
    components: {
        DataTable,
        CertificateModal,
        IndexWidget,
        ConfirmModal,
    },
}
</script>