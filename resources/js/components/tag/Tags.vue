<template>
    <div class="row">
        <div class="col-md-12 ">
            <ul class="nav nav-pills py-2"
                role="tablist"
            >
            </ul>
        </div>
        <div id="tag-content"
             class="col-md-12 m-0"
        >
            <IndexWidget
                v-permission="'tag_create'"
                modelName="Tag"
                url="/tags"
                :create=true
                :label="trans('global.tag.create')"
            />
            <IndexWidget
                v-for="tag in tags"
                :key="tag.id"
                :model="tag"
                modelName="Tag"
                url="/tags"
                title-field="translation"
            >
                <template v-slot:icon>
                    <i class="fas fa-tag"></i>
                </template>

                <template v-slot:dropdown
                          v-permission="'tag_edit, tag_delete'"
                >
                    <div
                        class="dropdown-menu dropdown-menu-right"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            v-permission="'tag_edit'"
                            :name="'edit-tag-' + tag.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editTag(tag)"
                        >
                            <i class="fa fa-pencil-alt mr-2"></i>
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
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.tag.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div
            id="tag-datatable-wrapper"
            class="w-100 dataTablesWrapper"
        >
            <DataTable
                id="tag-datatable"
                :columns="columns"
                :options="options"
                :search="search"
                width="100%"
                style="display: none;"
            />
        </div>

        <Teleport to="body">
            <TagModal/>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.tag.delete')"
                :description="trans('global.tag.delete_helper')"
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

import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import TagModal from "./TagModal.vue";
import {useGlobalStore} from "../../store/global";
import {useToast} from "vue-toastification";

DataTable.use(DataTablesCore);

export default {
    name: 'tags',
    props: {},
    setup() {
        const globalStore = useGlobalStore();
        const toast = useToast();
        return {
            globalStore,
            toast,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            tags: null,
            search: '',
            showConfirm: false,
            errors: {},
            currentTag: {},
            testVar: true,
            columns: [
                {title: 'id', data: 'id'},
                {title: 'name', data: 'translation', searchable: true},
            ],
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.loaderEvent();

        this.$eventHub.on('tag-added', (tag) => {
            this.tags.push(tag);
        });

        this.$eventHub.on('tag-updated', (tag) => {
            this.update(tag);
        });
    },
    computed: {
        options: function() {
            let options = this.$dtOptions;

            options.ajax = {
                url: '/tags/list',
            };

            return options;
        },
    },
    methods: {
        editTag(tag) {
            this.globalStore?.showModal('tag-modal', tag);
        },
        loaderEvent() {
            this.dt = $('#tag-datatable').DataTable();
            this.dt.on('draw.dt', () => { // checks if the datatable-data changes
                this.tags = this.dt.rows({page: 'current'}).data().toArray();

                $('#tag-content').insertBefore('#tag-datatable-wrapper');
            });

            this.$eventHub.on('filter', (filter) => {
                this.dt.search(filter).draw();
            });

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
                .catch(err => {
                    this.toast.error(this.trans('global.expel_error'));
                });
        },
        update(updatedTag) {
            const tag = this.tags.find(r => r.id === updatedTag.id);

            Object.assign(tag, updatedTag);
        },
    },
    components: {
        ConfirmModal,
        DataTable,
        TagModal,
        IndexWidget,
    },
}
</script>