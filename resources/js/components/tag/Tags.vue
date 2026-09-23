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
                <template #:icon>
                    <i class="fas fa-tag"></i>
                </template>

                <template #:dropdown
                          v-permission="'tag_edit, tag_delete'"
                >
                    <div
                        class="dropdown-menu dropdown-menu-end"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            v-permission="'tag_edit'"
                            :name="'edit-tag-' + tag.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editTag(tag)"
                        >
                            <i class="fa fa-pencil-alt me-2"></i>
                            {{ trans('global.tag.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'tag_delete'"
                            :id="'delete-tag-' + tag.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(tag)"
                        >
                            <i class="fa fa-trash me-2"></i>
                            {{ trans('global.tag.delete') }}
                        </button>
                    </div>
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
                @confirm="() => {
                    showConfirm = false;
                    destroy();
                }"
            />
        </Teleport>
    </div>
</template>
<script>
import IndexWidget from "../uiElements/IndexWidget.vue";
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
                    let index = this.tags.indexOf(this.currentTag);
                    this.tags.splice(index, 1);
                })
                .catch(e => {
                    console.log(e);
                    this.toast.error(this.errorMessage(e));
                });
        },
    },
}
</script>