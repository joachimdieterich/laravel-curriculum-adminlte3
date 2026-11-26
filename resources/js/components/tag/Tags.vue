<template>
    <div class="d-flex flex-column">
        <div
            id="tag-content"
            class="px-3"
        >
            <IndexWidget
                v-permission="'tag_create'"
                modelName="Tag"
                url="/tags"
                :create=true
                :label="trans('global.tag.create')"
            />
            <IndexWidget v-for="tag in tags"
                :key="tag.id"
                :model="tag"
                modelName="Tag"
                url="/tags"
                title-field="translation"
            >
                <template #icon>
                    <i class="fa fa-tag"></i>
                </template>

                <template #dropdown-alt>
                    <DropdownButton v-if="checkPermission('is_admin')"
                        :entries="[
                            { icon: 'fa-pencil-alt', label: 'global.tag.edit', callback: () => editTag(tag) },
                            { hr: true },
                            { icon: 'fa-trash', label: 'global.tag.delete', callback: () => confirmItemDelete(tag), class: 'text-danger' },
                        ]"
                    />
                </template>
            </IndexWidget>
        </div>

        <DataTable
            ref="datatable"
            id="tag-datatable"
            :columns="columns"
            :options="$dtOptions"
            ajax="/tags/list"
            class="d-none"
            @xhr="(e, settings, json) => tags = json.data"
        />

        <Teleport to="body">
            <TagModal/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.tag.delete')"
                :description="trans('global.tag.delete_helper')"
                @close="showConfirm = false"
                @confirm="destroy()"
            />
        </Teleport>
    </div>
</template>
<script>
import IndexWidget from "../uiElements/IndexWidget.vue";
import DropdownButton from "../uiElements/DropdownButton.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import TagModal from "./TagModal.vue";

DataTable.use(DataTablesCore);

export default {
    name: 'Tags',
    components: {
        ConfirmModal,
        DataTable,
        TagModal,
        IndexWidget,
        DropdownButton,
    },
    data() {
        return {
            component_id: this.$.uid,
            tags: null,
            showConfirm: false,
            currentTag: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'name', data: 'translation', searchable: true },
            ],
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.dt = this.$refs.datatable.dt;

        this.$eventHub.on('tag-added', tag => {
            this.tags.push(tag);
        });

        this.$eventHub.on('tag-updated', updatedTag => {
            const tag = this.tags.find(t => t.id === updatedTag.id);
            Object.assign(tag, updatedTag);
        });

        this.$eventHub.on('filter', filter => {
            this.dt.search(filter.searchString).draw();
        });
    },
    methods: {
        editTag(tag) {
            this.globalStore.showModal('tag-modal', tag);
        },
        confirmItemDelete(tag) {
            this.currentTag = tag;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/tags/' + this.currentTag.id)
                .then(res => {
                    this.showConfirm = false;
                    let index = this.tags.indexOf(this.currentTag);
                    this.tags.splice(index, 1);
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