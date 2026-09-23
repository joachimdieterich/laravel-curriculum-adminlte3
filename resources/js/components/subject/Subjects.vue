<template>
    <div class="d-flex flex-column">
        <div
            id="subject-content"
            class="px-3"
        >
            <IndexWidget
                v-permission="'subject_create'"
                key="'subjectCreate'"
                modelName="Subject"
                url="/subjects"
                :create=true
                :label="trans('global.subject.create')"
            />
            <IndexWidget v-for="subject in subjects"
                :key="'subjectIndex' + subject.id"
                :model="subject"
                modelName="Subject"
                url="/subjects"
            >
                <template #icon>
                    <i class="fa fa-swatchbook"></i>
                </template>

                <template #dropdown
                    v-permission="'subject_edit, subject_delete'"
                >
                    <div
                        class="dropdown-menu dropdown-menu-end"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            v-permission="'subject_edit'"
                            :name="'edit-subject-' + subject.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editSubject(subject)"
                        >
                            <i class="fa fa-pencil-alt me-2"></i>
                            {{ trans('global.subject.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'subject_delete'"
                            :id="'delete-subject-' + subject.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(subject)"
                        >
                            <i class="fa fa-trash me-2"></i>
                            {{ trans('global.subject.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <DataTable
            ref="datatable"
            id="subject-datatable"
            :columns="columns"
            :options="$dtOptions"
            ajax="/subjects/list"
            class="d-none"
            @xhr="(e, settings, json) => subjects = json.data"
        />

        <Teleport to="body">
            <SubjectModal/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.subject.delete')"
                :description="trans('global.subject.delete_helper')"
                @close="showConfirm = false"
                @confirm="() => {
                    showConfirm = false;
                    destroy();
                }"
            />
        </Teleport>
    </div>
</template>
<script>
import SubjectModal from "../subject/SubjectModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
DataTable.use(DataTablesCore);

export default {
    components: {
        ConfirmModal,
        DataTable,
        SubjectModal,
        IndexWidget,
    },
    data() {
        return {
            component_id: this.$.uid,
            subjects: null,
            showConfirm: false,
            currentRole: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
            ],
            dt: null,
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.dt = this.$refs.datatable.dt;

        this.$eventHub.on('subject-added', subject => {
            this.subjects.push(subject);
        });

        this.$eventHub.on('subject-updated', updatedSubject => {
            let subject = this.subjects.find(s => s.id === updatedSubject.id);
            Object.assign(subject, updatedSubject);
        });

        this.$eventHub.on('filter', filter => {
            this.dt.search(filter.searchString).draw();
        });
    },
    methods: {
        editSubject(subject) {
            this.globalStore.showModal('subject-modal', subject);
        },
        confirmItemDelete(subject) {
            this.currentRole = subject;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/subjects/' + this.currentRole.id)
                .then(res => {
                    let index = this.subjects.indexOf(this.currentRole);
                    this.subjects.splice(index, 1);
                })
                .catch(e => {
                    console.log(e);
                });
        },
    },
}
</script>