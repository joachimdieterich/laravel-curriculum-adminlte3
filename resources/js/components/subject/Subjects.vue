<template >
    <div class="row">
        <div id="subject-content"
             class="col-md-12 m-0">
            <IndexWidget
                v-permission="'subject_create'"
                key="'subjectCreate'"
                modelName="Subject"
                url="/subjects"
                :create=true
                :label="trans('global.subject.create')">
            </IndexWidget>
            <IndexWidget
                v-for="subject in subjects"
                :key="'subjectIndex'+subject.id"
                :model="subject"
                modelName="Subject"
                url="/subjects">
                <template v-slot:icon>
                    <i class="fa fa-swatchbook pt-2"></i>
                </template>

                <template
                    v-permission="'subject_edit, subject_delete'"
                    v-slot:dropdown>
                    <div class="dropdown-menu dropdown-menu-right"
                         style="z-index: 1050;"
                         x-placement="left-start">
                        <button
                            v-permission="'subject_edit'"
                            :name="'edit-subject-' + subject.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editSubject(subject)">
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.subject.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'subject_delete'"
                            :id="'delete-subject-' + subject.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(subject)">
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.subject.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div id="subject-datatable-wrapper"
             class="w-100 dataTablesWrapper">
            <DataTable
                id="subject-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display:none; "
            ></DataTable>
        </div>

        <Teleport to="body">
            <SubjectModal></SubjectModal>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.subject.delete')"
                :description="trans('global.subject.delete_helper')"
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
import SubjectModal from "../subject/SubjectModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import {useGlobalStore} from "../../store/global";
DataTable.use(DataTablesCore);

export default {
    setup () {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            subjects: null,
            search: '',
            showConfirm: false,
            url: '/subjects/list',
            errors: {},
            currentRole: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
                { title: 'title_short', data: 'title_short', searchable: true },
            ],
            options : this.$dtOptions,
            modalMode: 'edit'
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('subject-added', (subject) => {
            this.globalStore?.closeModal('subject-modal');
            this.subjects.push(subject);
        });

        this.$eventHub.on('subject-updated', (subject) => {
            this.globalStore?.closeModal('subject-modal');
            this.update(subject);
        });
        this.$eventHub.on('createSubject', () => {
            this.globalStore?.showModal('subject-modal', {});
        });
    },
    methods: {
        editSubject(subject){
            this.globalStore?.showModal('subject-modal', subject);
        },
        loaderEvent(){
            const dt = $('#subject-datatable').DataTable();
            dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.subjects = dt.rows({page: 'current'}).data().toArray();

                $('#subject-content').insertBefore('#subject-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                dt.search(filter).draw();
            });
        },
        confirmItemDelete(subject){
            this.currentRole = subject;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/subjects/' + this.currentRole.id)
                .then(res => {
                    let index = this.subjects.indexOf(this.currentRole);
                    this.subjects.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        update(subject) {
            const index = this.subjects.findIndex(
                vc => vc.id === subject.id
            );

            for (const [key, value] of Object.entries(subject)) {
                this.subjects[index][key] = value;
            }
        }
    },
    components: {
        ConfirmModal,
        DataTable,
        SubjectModal    ,
        IndexWidget
    },
}
</script>
