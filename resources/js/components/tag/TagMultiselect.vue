<template>
    <Select2
        v-permission="'tag_access'"
        id="tags"
        name="tags"
        url="/tags"
        model="tags"
        button-size-class="btn-sm"
        :button-new-line="true"
        :placeholder="trans('global.tag.please_select')"
        :label="trans('global.tag.title')"
        :multiple="true"
        :selected="selectedTags"
        @selectedValue="(data) => $emit('selectedValue', data)"
        @cleared="(data) => $emit('cleared', data)"
        @opened="(data) => $emit('opened', data)"
    >
        <template #buttons>
            <button
                type="button"
                class="btn btn-info btn-sm"
                @click="showNewTagForm = !showNewTagForm"
            >
                {{ trans("global.tag.create_new_title") }}
            </button>
        </template>
        <template #pre-dropdown>
            <drop-down-modal
                :show-title="false"
                :show-footer="false"
                :show="showNewTagForm"
                classes="mb-1"
                modal-class="position-relative mb-2"
            >
                <template #body>
                    <div class="input-group">
                        <input
                            id="name"
                            name="name"
                            type="text"
                            class="form-control"
                            maxlength="191"
                            :placeholder="trans('global.tag.name') + ' *'"
                            v-model="tag.name"
                        >
                        <button
                            type="submit"
                            class="btn btn-primary"
                            :disabled="!tag.name"
                            @click="submit()"
                        >
                            {{ trans('global.save') }}
                        </button>
                    </div>
                </template>
            </drop-down-modal>
        </template>
    </Select2>
</template>
<script>
import DropDownModal from "../uiElements/DropDownModal.vue";
import Select2 from "../forms/Select2.vue";
import {useToast} from "vue-toastification";

export default {
    name: "TagMultiselect",
    components: {
        DropDownModal,
        Select2,
    },
    props: {
        type: {
            type: String,
            required: true,
            title: "The type of the tagged model"
        },
        modelId: {
            type: [Number, null],
            required: true,
            title: "ID of the tagged model"
        },
        selectedTags: {
            type: [Object, Array],
            required: true,
            default: [],
        }
    },
    emits: ['selectedValue', 'opened', 'cleared', 'tag-attached'],
    setup() {
        return { toast: useToast() }
    },
    data() {
        return {
            showNewTagForm: false,
            tag: {
                name: '',
            }
        };
    },
    computed: {
        attachForm() {
            return {
                'name': this.tag.name,
                'type': this.type,
                'taggable_id': this.modelId,
            };
        },
    },
    methods: {
        resetNewTagForm() {
            this.showNewTagForm = false;
            this.tag.name = '';
            this.tag.global = false;
        },
        submit() {
            axios.post('/tags/attach', this.attachForm)
                .then(response => {
                    this.$emit("tag-attached", response.data);
                    this.resetNewTagForm();
                })
                .catch(e => {
                    this.toast.error(this.errorMessage(e));
                    console.log(e.response);
                });
        },
    }
}
</script>