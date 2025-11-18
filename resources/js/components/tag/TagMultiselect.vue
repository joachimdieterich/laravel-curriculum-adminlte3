<template>
    <Select2
        v-permission="'tag_access'"
        id="tags"
        name="tags"
        url="/tags"
        model="tags"
        :label="trans('global.tag.title')"
        :multiple="true"
        :selected="selectedTags"
        @selectedValue="(data) => {
            this.$emit('selectedValue', data);
        }"
        @cleared="(data) => {
            this.$emit('cleared', data);
        }"
    >
        <template v-slot:pre-dropdown>
            <drop-down-modal :show-title="false" :show-footer="false" :show="showNewTagForm" classes="mb-1" modal-class="mb-2" modal-css="position: relativ;">
                <template v-slot:body>
                    <div class="input-group">
                        <input id="name" name="name" class="form-control" type="text" v-model="tag.name" :placeholder="trans('global.tag.name') + ' *'">
                        <button
                            id="tag-save"
                            class="btn btn-primary tag-save-button"
                            :disabled="!tag.name"
                            @click="submit()"
                        >
                            {{ trans('global.save') }}
                        </button>
                    </div>
                </template>
            </drop-down-modal>
        </template>
        <template v-slot:buttons>
            <span class="btn btn-info btn-xs additional-button pull-right" @click="showNewTagForm = !showNewTagForm">
                {{ trans("global.tag.create_new_title") }}
            </span>
        </template>
    </Select2>
</template>

<script>
import {defineComponent} from 'vue'
import Select2 from "../forms/Select2.vue";
import axios from "axios";
import {useToast} from "vue-toastification";
import DropDownModal from "../uiElements/DropDownModal.vue";

export default defineComponent({
    name: "TagMultiselect",
    components: {DropDownModal, Select2},
    props: {
        type: {
            required: true,
            type: String,
            title: "The type of the tagged model"
        },
        modelId: {
            required: true,
            type: Number,
            title: "ID of the tagged model"
        },
        selectedTags: {
            required: true,
            type: [Object, Array],
            default: [],
        }
    },
    emits: ['selectedValue', 'cleared', 'tag-attached'],
    setup() {
        const toast = useToast();
        return {
            toast,
        }
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
        attachForm: function() {
            return {
                'name': this.tag.name,
                'type': this.type,
                'taggable_id': this.modelId,
            };
        }
    },
    methods: {
        resetNewTagForm() {
            this.showNewTagForm = false;
            this.tag.name = '';
            this.tag.global = false;
        },
        submit() {
            axios.post('/tags/attach', this.attachForm)
                 .then(r => {
                     this.$emit("tag-attached", r.data);
                     this.resetNewTagForm();
                 })
                 .catch(e => {
                     this.toast.error(this.errorMessage(e));
                     console.log(e.response);
                 });
        },
    }
})
</script>

<style>
    .tag-save-button {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        margin-left: -1px;
    }
</style>