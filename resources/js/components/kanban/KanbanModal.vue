<template>
    <Modal
        model="kanban"
        modalName="kanban-modal"
        :form="form"
        :require-title="true"
        :show-description-field="true"
        :show-owner-field="form.id && checkPermission('is_teacher')"
        :show-display-section="true"
        :show-medium-field="true"
        :show-permission-section="true"
    >
        <template #general-extended>
            <TagMultiselect
                class="mt-3"
                type="App\Kanban"
                :model-id="form.id"
                :selectedTags="form.tags"
                @selectedValue="data => form.tags = data"
                @cleared="form.tags = []"
                @tag-attached="tag => form.tags.push(tag)"
            />
        </template>
        <template #permissions>
            <Switch
                id="kanban-commentable"
                class="mb-2"
                label="global.commentable"
                v-model="form.commentable"
            />

            <Switch
                id="kanban-auto-refresh"
                class="mb-2"
                label="global.auto_refresh"
                v-model="form.auto_refresh"
            />

            <Switch
                id="kanban-only-edit-owned-items"
                class="mb-2"
                label="global.kanban.only_edit_owned_items"
                v-model="form.only_edit_owned_items"
            />

            <Switch
                id="kanban-collapse-items"
                class="mb-2"
                label="global.kanban.collapse_items"
                v-model="form.collapse_items"
            />

            <Switch
                id="kanban-allow-copy"
                label="global.kanban.allow_copy"
                v-model="form.allow_copy"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import TagMultiselect from "../tag/TagMultiselect.vue";
import Switch from '../forms/Switch.vue';

export default {
    name: 'kanban-modal',
    components: {
        Modal,
        Switch,
        TagMultiselect,
    },
    props: {
        params: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                title:  '',
                description:  '',
                owner_id: null,
                color:'#27AF60',
                medium_id: null,
                commentable: true,
                auto_refresh: false,
                only_edit_owned_items: false,
                collapse_items: false,
                allow_copy: true,
                tags: [],
            }),
        }
    },
}
</script>