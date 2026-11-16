<template>
    <Modal
        model="tag"
        modalName="tag-component-modal"
        :form="form"
        :allow-overflow="true"
        :intercept-save="true"
        @opened="preProcessing()"
        @save="update()"
    >
        <template #general>
            <tag-multiselect
                :type="modelNamespace"
                :model-id="form.id"
                :selectedTags="selectedTags"
                @selectedValue="data => form.tags = data"
                @cleared="() => form.tags = []"
                @tag-attached="tag => updateSelectedTags(tag.id)"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import TagMultiselect from "./TagMultiselect.vue";
import Form from "form-backend-validation";

export default {
    name: 'tag-component-modal',
    components: {
        Modal,
        TagMultiselect,
    },
    props: {
        eventPrefix: {
            type: String,
            required: true,
        },
        modelNamespace: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                tags: [],
                model: '',
            }),
            selectedTags: [],
        }
    },
    methods: {
        preProcessing() {
            this.form.model = this.modelNamespace;
            this.updateSelectedTags();
        },
        update() {
            axios.patch('/tags/model/', this.form)
                .then(r => {
                    this.$eventHub.emit(this.eventPrefix + '-updated', r.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.toast.error(this.errorMessage(e));
                    console.log(e.response);
                });
        },
        getSelectedTags(tags) {
            if (tags && tags[0] && tags[0]?.name){
                return tags.map(p => p.id);
            }

            return tags;
        },
        updateSelectedTags(newTag) {
            if (newTag !== undefined) {
                this.form.tags.push(newTag)
            }

            this.selectedTags = this.getSelectedTags(this.form.tags);
        },
    },
}
</script>