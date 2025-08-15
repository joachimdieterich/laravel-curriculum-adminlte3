<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @mouseup.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="modal-header">
                    <span class="card-title">
                        {{ method == 'post' ? trans('global.planEntry.create') : trans('global.planEntry.edit') }}
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

                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <input
                                    id="title"
                                    name="title"
                                    type="text"
                                    class="form-control"
                                    v-model.trim="form.title"
                                    :placeholder="trans('global.planEntry.fields.title') + ' *'"
                                    required
                                />
                                <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                            </div>
        
                            <Editor
                                :id="'description_' + component_id"
                                :name="'description_' + component_id"
                                :init="tinyMCE"
                                v-model="form.description"
                            />
                        </div>
                    </div>

                    <div class="card">
                        <div
                            class="card-header border-bottom"
                            data-card-widget="collapse"
                        >
                            <span class="card-title">{{ trans('global.display') }}</span>
                        </div>
    
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <v-swatches
                                    style="height: 42px"
                                    :swatches="$swatches"
                                    row-length="5"
                                    v-model="form.color"
                                    show-fallback
                                    fallback-input-type="color"
                                    @input="(id) => {
                                        if (id.isInteger) {
                                            this.form.color = id;
                                        }
                                    }"
                                    :max-height="300"
                                />

                                <MediumForm v-if="form.id"
                                    :id="'medium_form' + component_id"
                                    :medium_id="form.medium_id"
                                    :subscribable_id="form.id"
                                    subscribable_type="App\PlanEntry"
                                    accept="image/*"
                                    @selectedValue="(id) => {
                                        // on removal of medium, directly update the resource
                                        if (this.form.medium_id !== null && id === null) {
                                            this.$eventHub.emit('plan-entry-updated', {
                                                id: this.form.id,
                                                medium_id: null,
                                            });
                                        }
                                        this.form.medium_id = id;
                                    }"
                                />

                                <div class="dropdown">
                                    <button
                                        class="btn btn-default"
                                        style="width: 42px; padding: 6px 0px;"
                                        type="button"
                                        data-toggle="dropdown"
                                        aria-expanded="false"
                                    >
                                        <i :class="form.css_icon + ' pt-2'"></i>
                                    </button>
                                    <font-awesome-picker
                                        class="dropdown-menu dropdown-menu-right"
                                        style="min-width: min(385px, 90vw);"
                                        :searchbox="trans('global.select_icon')"
                                        @select-icon="(icon) => this.form.css_icon = 'fa fa-' + icon.className"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="entry-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="entry-save"
                            class="btn btn-primary ml-3"
                            :disabled="!form.title"
                            @click="submit()"
                        >
                            {{ trans('global.save') }}
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </Transition>
</template>
<script>
import Form from 'form-backend-validation';
import Editor from "@tinymce/tinymce-vue";
import MediumForm from "../media/MediumForm.vue";
import FontAwesomePicker from "../../../views/forms/input/FontAwesomePicker.vue";
import {useGlobalStore} from "../../store/global";
import {useToast} from "vue-toastification";

export default {
    name: 'plan-entry-modal',
    props: {
        plan: {
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
            form: new Form({
                id: '',
                title: '',
                description: '',
                plan_id: this.plan.id,
                color: '#27AF60',
                css_icon: 'fa fa-calendar-day',
                order_id: 0,
                medium_id: null,
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink link lists curriculummedia code autoresize"
                ],
                {
                    callback: 'insertContent',
                    callbackId: this.component_id,
                    subscribable_type: 'App\\Plan',
                    subscribable_id: this.plan.id,
                },
                "bold underline italic | alignleft aligncenter alignright alignjustify",
                "bullist numlist outdent indent | curriculummedia link mathjax code",
            ),
        }
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show && !state.modals[this.$options.name].lock) {
                this.globalStore.lockModal(this.$options.name);
                const params = state.modals[this.$options.name].params;
                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                    this.form.date = [new Date(this.form.begin) ?? '', new Date(this.form.end) ?? ''];
                    this.form.description = this.$decodeHTMLEntities(params.description);

                    if (this.form.id != '') {
                        this.method = 'patch';
                    } else {
                        this.method = 'post';
                    }
                }
            }
        });
    },
    methods: {
        setIcon(icon) {
            this.form.css_icon = 'fa fa-' + icon.className;
        },
        submit() {
            if (this.method == 'post') {
                this.add();
            } else {
                this.update();
            }

        },
        add() {
            axios.post('/planEntries', this.form)
                .then(response => {
                    this.$eventHub.emit('plan-entry-added', response.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.toast.error(this.errorMessage(e));
                    console.log(e)
                });
        },
        update() {
            axios.patch('/planEntries/' + this.form.id, this.form)
                .then(response => {
                    this.$eventHub.emit('plan-entry-updated', response.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(error => {
                    this.toast.error(this.errorMessage(e));
                    console.log(error)
                });
        },
    },
    components: {
        Editor,
        MediumForm,
        FontAwesomePicker,
    },
}
</script>