<template>
    <Select2
        v-permission="'tag_access'"
        id="tags"
        name="tags"
        url="/tags"
        model="tags"
        :additional_query_param="typeParameter"
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
            <inline-modal :open="showNewTagForm" classes="mb-1">
                <div class="input-group">
                    <input id="name" name="name" class="form-control tag-name" type="text" v-model="tag.name" :placeholder="trans('global.tag.name') + ' *'">
                    <span class="custom-control custom-switch custom-switch-on-green my-auto">
                        <input id="global"
                               class="custom-control-input pt-1"
                               type="checkbox"
                               v-model="tag.global"
                        />
                        <label class="custom-control-label ml-2" for="global">
                            {{ trans('global.tag.create_global_question') }}
                        </label>
                    </span>
                        <button
                            id="tag-save"
                            class="btn btn-primary ml-2"
                            :disabled="!tag.name"
                            @click="submit()"
                        >
                            {{ trans('global.save') }}
                        </button>
                </div>
            </inline-modal>
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
import InlineModal from "../uiElements/InlineModal.vue";
import axios from "axios";
import {useToast} from "vue-toastification";

export default defineComponent({
    name: "TagMultiselect",
    components: {InlineModal, Select2},
    props: {
        type: {
            required: true,
            type: String,
            title: "The type of the tag and model"
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
    emits: ['selectedValue', 'cleared', 'tag-added'],
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
                global: false,
            }
        };
    },
    computed: {
        typeParameter: function () {
            return {
                'type': this.type,
            };
        },
        form: function() {
            let type = this.type;

            if (this.tag.global === true) {
                type = null;
            }

            return {
                'name': this.tag.name,
                'type': type,
                'taggable_id': this.modelId,
            };
        }
    },
    methods: {
        submit() {
            axios.post('/tags/attach', this.form)
                 .then(r => {
                     this.$emit("tag-added", r.data);
                     this.showNewTagForm = false;
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
    .tag-name {
        border-radius: 0.25rem !important;
    }
</style>