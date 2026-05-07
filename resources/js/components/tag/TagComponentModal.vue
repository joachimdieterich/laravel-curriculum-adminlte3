<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show" class="modal-mask">
            <div class="modal-container">
                <div class="modal-header">
                    <span class="card-title">
                        {{ trans('global.tag.title') }}
                    </span>
                    <button
                        type="button"
                        class="btn btn-icon text-secondary"
                        :title="trans('global.close')"
                        @click="globalStore?.closeModal($options.name)"
                    >
                        <i class="fa fa-times"></i>
                    </button>
                </div>


                <div id="tag-modal-body" class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <tag-multiselect
                                :type="modelNamespace"
                                :model-id="this.form.id"
                                :selectedTags="this.selectedTags"
                                @selectedValue="(data) => {
                                    this.form.tags = data;
                                }"
                                @cleared="() => {
                                    this.form.tags = [];
                                }"
                                @tag-attached="(tag) => {
                                    this.updateSelectedTags(tag.id);
                                }"
                            ></tag-multiselect>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="save"
                            class="btn btn-primary ml-3"
                            :disabled="processing"
                            @click="update()"
                        >
                            <span v-if="processing"><i class="fa fa-spinner fa-pulse fa-fw"></i></span>
                            <span v-else>{{ trans('global.save') }}</span>
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </Transition>
</template>
<script>
import {useGlobalStore} from "../../store/global.js";
import {useToast} from "vue-toastification";
import TagMultiselect from "./TagMultiselect.vue";
import Form from "form-backend-validation";
import axios from "axios";

export default {
    name: 'tag-component-modal',
    components: {TagMultiselect},
    setup() {
        const globalStore = useGlobalStore();
        const toast = useToast();
        return {
            globalStore,
            toast,
            form: new Form({
                id: '',
                tags: [],
                model: '',
            }),
        }
    },
    props: {
        eventPrefix: {
            type: String
        },
        modelNamespace: {
            type: String
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            processing: false,
            selectedTags: [],
        }
    },
    methods: {
        update() {
            this.processing = true;

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
                    this.updateSelectedTags();
                }

                this.form.model = this.modelNamespace;
            }
        });
    },
}
</script>

<style scoped>
    #tag-modal-body {
        overflow-y: visible;
    }
</style>