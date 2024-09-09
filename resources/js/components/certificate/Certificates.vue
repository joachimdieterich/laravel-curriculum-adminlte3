<template >
    <div class="row">
        <div id="certificate-content"
             class="col-md-12 m-0">
                <IndexWidget
                    v-permission="'certificate_create'"
                    key="'certificateCreate'"
                    modelName="Certificate"
                    url="/certificates"
                    :create=true
                    :createLabel="trans('global.certificate.create')">
                </IndexWidget>
                <IndexWidget
                    v-for="certificate in certificates"
                    :key="'certificateIndex'+certificate.id"
                    :model="certificate"
                    modelName= "Certificate"
                    url="/certificates">
                <template v-slot:icon>
                    <i class="fa fa-university pt-2"></i>
                </template>

                <template
                    v-permission="'certificate_edit, certificate_delete'"
                    v-slot:dropdown>
                    <div class="dropdown-menu dropdown-menu-right"
                         style="z-index: 1050;"
                         x-placement="left-start">
                        <button
                            v-permission="'certificate_edit'"
                            :name="'edit-certificate-' + certificate.id"
                                class="dropdown-item text-secondary"
                                @click.prevent="editCertificate(certificate)">
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.certificate.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'certificate_delete'"
                            :id="'delete-certificate-' + certificate.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(certificate)">
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.certificate.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div id="certificate-datatable-wrapper"
        class="w-100 dataTablesWrapper">
            <DataTable
                id="certificate-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display:none; "
            ></DataTable>
        </div>

        <Teleport to="body">
            <CertificateModal></CertificateModal>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.certificate.delete')"
                :description="trans('global.certificate.delete_helper')"
                css= 'danger'
                :ok_label="trans('trans.global.ok')"
                :cancel_label="trans('trans.global.cancel')"
                @close="() => {
                    this.showConfirm = false;
                }"
                @confirm="() => {
                    this.showConfirm = false;
                    this.destroy();
                }"
            ></ConfirmModal>
        </Teleport>
    </div>
</template>


<script>
import CertificateModal from "../certificate/CertificateModal";
import ConfirmModal from "../uiElements/ConfirmModal";
import IndexWidget from "../uiElements/IndexWidget";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import {useGlobalStore} from "../../store/global";
DataTable.use(DataTablesCore);

export default {
    props: {},
    setup () {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            certificates: null,
            search: '',
            showCertificateModal: false,
            showConfirm: false,
            url: '/certificates/list',
            errors: {},
            currentCertificate: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
                { title: 'description', data: 'description', searchable: true},
                { title: 'body', data: 'body', searchable: true},
                { title: 'curriculum_id', data: 'curriculum_id', searchable: true},
                { title: 'organization_id', data: 'organization_id', searchable: true},
                { title: 'owner_id', data: 'owner_id', searchable: true},
            ],
            options : this.$dtOptions,
            modalMode: 'edit'
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('certificate-added', (certificate) => {
            this.certificates.push(certificate);
            this.globalStore?.closeModal('certificate-modal');
        });

        this.$eventHub.on('certificate-updated', (certificate) => {
            this.update(certificate);
            this.globalStore?.closeModal('certificate-modal');
        });
        this.$eventHub.on('createCertificate', () => {
            this.globalStore?.showModal('certificate-modal', {});
        });
    },
    methods: {
        editCertificate(certificate){
            this.globalStore?.showModal('certificate-modal', certificate);
        },
        loaderEvent(){
            const dt = $('#certificate-datatable').DataTable();
            dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.certificates = dt.rows({page: 'current'}).data().toArray();

                $('#curriculum-content').insertBefore('#certificate-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                dt.search(filter).draw();
            });
        },
        confirmItemDelete(certificate){
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
        update(certificate) {
            const index = this.certificates.findIndex(
                vc => vc.id === certificate.id
            );

            for (const [key, value] of Object.entries(certificate)) {
                this.certificates[index][key] = value;
            }
        }
    },
    components: {
        DataTable,
        CertificateModal,
        IndexWidget,
        ConfirmModal
    },
}
</script>
