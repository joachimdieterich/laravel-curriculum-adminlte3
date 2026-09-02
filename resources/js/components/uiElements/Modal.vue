<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[modalName]?.show"
            class="modal-mask"
        >
            <div
                class="modal-container"
                :style="css"
            >
                <div class="modal-header">
                    <span class="modal-title">
                        {{ trans(headerTitle) }}
                    </span>
                    <button
                        type="button"
                        class="btn btn-icon text-secondary"
                        :title="trans('global.close')"
                        @click="globalStore?.closeModal(modalName)"
                    >
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div
                    class="modal-body accordion"
                    :class="allowOverflow && 'overflow-y-visible'"
                >
                    <div class="accordion-item">
                        <div v-if="showGeneralHeader"
                            class="accordion-header"
                        >
                            <span
                                class="accordion-button"
                                data-bs-toggle="collapse"
                                :data-bs-target="'#' + model + '-general'"
                                aria-expanded="true"
                                :aria-controls="model + '-general'"
                            >
                                {{ trans('global.general') }}
                            </span>
                        </div>
                        <div
                            :id="model + '-general'"
                            class="accordion-collapse collapse show"
                        >
                            <div>
                                <slot name="general">
                                    <input
                                        type="text"
                                        :id="model + '-title'"
                                        :name="model + '-title'"
                                        class="form-control"
                                        maxlength="191"
                                        v-model.trim="form.title"
                                        :placeholder="trans('global.title') + ' *'"
                                        :required="requireTitle"
                                    />
                                    <textarea v-if="showDescriptionField"
                                        :id="model + '-description'"
                                        :name="model + '-description'"
                                        class="form-control mt-3"
                                        style="max-height: 35svh;"
                                        rows="4"
                                        :placeholder="trans('global.description')"
                                        v-model.trim="form.description"
                                    ></textarea>
                                    <Select2 v-if="showOwnerField"
                                        :id="model + '-owner'"
                                        css="mt-3"
                                        :label="trans('global.change_owner')"
                                        model="User"
                                        url="/users"
                                        :selected="form.owner_id"
                                        @selectedValue="(id) => this.form.owner_id = id[0]"
                                    />
                                </slot>
                                <slot name="general-extended"></slot>
                            </div>
                        </div>
                    </div>

                    <div v-if="showDisplaySection"
                        class="accordion-item"
                    >
                        <div class="accordion-header">
                            <span
                                class="accordion-button"
                                data-bs-toggle="collapse"
                                :data-bs-target="'#' + model + '-display'"
                                aria-expanded="false"
                                :aria-controls="model + '-display'"
                            >
                                {{ trans('global.display') }}
                            </span>
                        </div>
                        <div
                            :id="model + '-display'"
                            class="accordion-collapse collapse show"
                        >
                            <div class="d-flex justify-content-between w-100">
                                <v-swatches
                                    style="height: 42px;"
                                    :swatches="$swatches"
                                    row-length="5"
                                    popover-y="top"
                                    v-model="form.color"
                                    show-fallback
                                    fallback-input-type="color"
                                />
                                <NewMediumForm v-if="showMediumField"
                                    :subscribable_id="form.id"
                                    :subscribable_type="'App\\' + model.charAt(0).toUpperCase() + model.slice(1)"
                                    :allow_fallback_on_create="true"
                                    :medium_id="form.medium_id"
                                    :multiple="allowMultipleMedia"
                                    @add="(medium) => form.medium_id = medium.id ?? null"
                                    @delete="() => form.medium_id = null"
                                />
                                <FontAwesomePicker v-if="showIconPicker"
                                    :button-icon="form.css_icon"
                                    @selectIcon="(icon) => form.css_icon = 'fa fa-' + icon.className"
                                />
                            </div>
                        </div>
                    </div>

                    <div v-if="showPermissionSection"
                        class="accordion-item"
                    >
                        <div class="accordion-header">
                            <span
                                class="accordion-button"
                                data-bs-toggle="collapse"
                                :data-bs-target="'#' + model + '-permissions'"
                                aria-expanded="false"
                                :aria-controls="model + '-permissions'"
                            >
                                {{ trans('global.permissions') }}
                            </span>
                        </div>
                        <div
                            :id="model + '-permissions'"
                            class="accordion-collapse collapse show"
                        >
                            <div>
                                <slot name="permissions"></slot>
                            </div>
                        </div>
                    </div>

                    <slot name="custom"></slot>
                </div>

                <div class="modal-footer">
                    <slot name="footer-left"></slot>
                    <span class="pull-right">
                        <button v-if="!hideCancelButton"
                            :id="model + '-cancel'"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal(modalName)"
                        >
                            {{ cancelLabel }}
                        </button>
                        <button v-if="!hideSaveButton"
                            :id="model + '-save'"
                            class="btn btn-primary ms-3"
                            :disabled="processing || (requireTitle && !form.title)"
                            @click="$emit('save', form)"
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
import Select2 from "../forms/Select2.vue";
import NewMediumForm from "../media/NewMediumForm.vue";
import FontAwesomePicker from "./FontAwesomePicker.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'Modal',
    emits: ['save'],
    expose: ['resetForm'],
    components: {
        Select2,
        NewMediumForm,
        FontAwesomePicker,
    },
    props: {
        model: {
            type: String,
            required: true,
            description: 'The singular name of the model in lowercase',
        },
        modalName: {
            type: String,
            required: true,
            description: 'The name of the modal to control visibility',
        },
        css: {
            type: String,
            description: 'Additional CSS styles for the modal container',
        },
        method: {
            type: String,
            description: 'The HTTP method for the form submission (e.g., "post" or "patch")',
        },
        title: {
            type: String,
            description: 'Translation String for the modal-header',
        },
        requireTitle: {
            type: Boolean,
            default: false,
            description: 'Indicates if the built-in title field is required for form submission',
        },
        processing: {
            type: Boolean,
            default: false,
            description: 'Indicates if the form has been submitted and is being processed',
        },
        allowOverflow: {
            type: Boolean,
            default: false,
            description: 'Allow content (e. g. color-picker) to overflow outside the modal-body',
        },
        showGeneralHeader: {
            type: Boolean,
            default: false,
            description: 'Controls the visibility and functionality of the general header section',
        },
        showDescriptionField: {
            type: Boolean,
            default: false,
            description: 'Controls the visibility of the description field in the form',
        },
        showOwnerField: {
            type: Boolean,
            default: false,
            description: 'Controls the visibility of the owner field in the form',
        },
        showDisplaySection: {
            type: Boolean,
            default: false,
            description: 'Controls the visibility of the display section in the form',
        },
        showMediumField: {
            type: Boolean,
            default: false,
            description: 'Controls the visibility of the medium field in the form',
        },
        allowMultipleMedia: {
            type: Boolean,
            default: false,
            description: 'Allows multiple media to be selected in the medium field',
        },
        showIconPicker: {
            type: Boolean,
            default: false,
            description: 'Controls the visibility of the icon picker in the form',
        },
        showPermissionSection: {
            type: Boolean,
            default: false,
            description: 'Controls the visibility of the permission section in the form',
        },
        cancelLabel: {
            type: String,
            default: window.trans.global.cancel,
        },
        hideCancelButton: {
            type: Boolean,
            default: false,
            description: 'Controls the visibility of the cancel button in the modal footer',
        },
        hideSaveButton: {
            type: Boolean,
            default: false,
            description: 'Controls the visibility of the save button in the modal footer',  
        },
    },
    setup() {
        return { globalStore: useGlobalStore() };
    },
    data() {
        return {
            // we're not using the Form-plugin, since two Form-objects can't be merged,
            // and we want to keep the form data in the parent component
            form: {},
        };
    },
    mounted() {
        this.resetForm();
    },
    methods: {
        /**
         * exposed function that should only be called from the parent component
         * @param formData populate the form
         */
        resetForm(formData = null) {
            this.form = {
                id: null,
                title: '',
                owner_id: null,
                color: '#27AF60',
                medium_id: null,
                css_icon: 'fa fa-book',
            };
            // only add description-field if the built-in textarea is used
            if (this.showDescriptionField) this.form.description = '';

            if (formData) {
                Object.keys(this.form).forEach(key => {
                    if (formData.hasOwnProperty(key)) {
                        this.form[key] = formData[key];
                    }
                });
            }
        },
    },
    computed: {
        headerTitle() {
            let title = this.title;
            if (!title) {
                title = this.method === 'post'
                    ? 'global.' + this.model + '.create'
                    : 'global.' + this.model + '.edit';
            }
            return title;
        },
    },
};
</script>