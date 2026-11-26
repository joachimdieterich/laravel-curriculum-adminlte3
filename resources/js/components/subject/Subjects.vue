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

                <template #dropdown-alt>
                    <DropdownButton v-if="checkPermission('is_admin')"
                        :entries="[
                            { icon: 'fa-pencil-alt', label: 'global.subject.edit', callback: () => editSubject(subject) },
                            { hr: true },
                            { icon: 'fa-trash', label: 'global.subject.delete', callback: () => confirmItemDelete(subject), class: 'text-danger' },
                        ]"
                    />
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
                @confirm="destroy()"
            />
        </Teleport>
    </div>
</template>
<script>
import SubjectModal from "../subject/SubjectModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DropdownButton from "../uiElements/DropdownButton.vue";
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
        DropdownButton,
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
                    this.showConfirm = false;
                    let index = this.subjects.indexOf(this.currentRole);
                    this.subjects.splice(index, 1);
                })
                .catch(e => {
                    console.log(e);
                    this.showConfirm = false;
                    this.toast.error(this.errorMessage(e));
                });
        },
    },
}
</script>