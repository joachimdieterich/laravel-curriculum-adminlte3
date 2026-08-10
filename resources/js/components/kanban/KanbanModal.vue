<template>
    <Modal
        ref="modal"
        model="kanban"
        modalName="kanban-modal"
        :method="method"
        :processing="processing"
        :require-title="true"
        :show-owner-field="form.id && checkPermission('is_teacher')"
        :show-medium-field="true"
        :show-permission-section="true"
        @save="(form) => submit(form)"
    >
        <template #general-extended>
            <TagMultiselect
                class="mt-3"
                type="App\Kanban"
                :model-id="form.id"
                :selectedTags="selectedTags"
                @selectedValue="(data) => form.tags = data"
                @cleared="() => form.tags = []"
                @tag-attached="(tag) => updateSelectedTags(tag.id)"
            />
        </template>
        <template #permissions>
            <div class="form-check form-switch">
                <input
                    id="kanban-commentable"
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    v-model="form.commentable"
                    switch
                />
                <label
                    class="form-check-label"
                    for="kanban-commentable"
                >
                    {{ trans('global.commentable') }}
                </label>
            </div>

            <div class="form-check form-switch">
                <input
                    id="kanban-auto-refresh"
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    v-model="form.auto_refresh"
                    switch
                />
                <label
                    class="form-check-label"
                    for="kanban-auto-refresh"
                >
                    {{ trans('global.auto_refresh') }}
                </label>
            </div>

            <div class="form-check form-switch">
                <input
                    id="kanban-only-edit-owned-items"
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    v-model="form.only_edit_owned_items"
                    switch
                />
                <label
                    class="form-check-label"
                    for="kanban-only-edit-owned-items"
                >
                    {{ trans('global.kanban.only_edit_owned_items') }}
                </label>
            </div>

            <div class="form-check form-switch">
                <input
                    id="kanban-collapse-items"
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    v-model="form.collapse_items"
                    switch
                />
                <label
                    class="form-check-label"
                    for="kanban-collapse-items"
                >
                    {{ trans('global.kanban.collapse_items') }}
                </label>
            </div>

            <div class="form-check form-switch">
                <input
                    id="kanban-allow-copy"
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    v-model="form.allow_copy"
                    switch
                />
                <label
                    class="form-check-label"
                    for="kanban-allow-copy"
                >
                    {{ trans('global.kanban.allow_copy') }}
                </label>
            </div>
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';

import Form from 'form-backend-validation';
import NewMediumForm from "../media/NewMediumForm.vue";
import Select2 from "../forms/Select2.vue";
import {useGlobalStore} from "../../store/global";
import {useToast} from "vue-toastification";
import TagMultiselect from "../tag/TagMultiselect.vue";

export default {
    name: 'kanban-modal',
    components: {
        Modal,
        TagMultiselect,
        Select2,
        NewMediumForm,
    },
    props: {
        params: {
            type: Object,
            default: null,
        },
    },
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
            method: 'post',
            processing: false,
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
            selectedTags: []
        }
    },
    computed: {
        textColor: function() {
            return this.$textcolor(this.form.color, '#333333');
        }
    },
    methods: {
        submit(formData) {
            this.form.populate(formData);
            this.processing = true;

            if (this.method == 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            axios.post('/kanbans', this.form)
                .then(r => {
                    this.$eventHub.emit('kanban-added', r.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.processing = false;
                    this.toast.error(this.errorMessage(e));
                    console.log(e.response);
                });
        },
        update() {
            axios.patch('/kanbans/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('kanban-updated', r.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.processing = false;
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
        }
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show && !state.modals[this.$options.name].lock) {
                this.globalStore.lockModal(this.$options.name);
                this.processing = false;
                this.form.reset();

                const params = state.modals[this.$options.name].params;
                if (typeof (params) !== 'undefined') {
                    params.tags = this.getSelectedTags(params.tags);
                    this.form.populate(params);
                    this.method = this.form.id ? 'patch' : 'post';
                    this.updateSelectedTags();
                }

                this.$refs.modal.resetForm(this.form);
            }
        });
    },
}
</script>