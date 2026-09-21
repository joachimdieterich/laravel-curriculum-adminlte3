<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[modalName]?.show"
            class="modal-mask"
            :style="zIndex && { 'z-index': zIndex }"
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
                        :aria-label="trans('global.close')"
                        @click="close()"
                    >
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <slot name="modal-body">
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
                                            @selectedValue="id => form.owner_id = id[0]"
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
                                <div class="d-flex align-items-center justify-content-between w-100">
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
                                        @add="medium => form.medium_id = medium.id ?? null"
                                        @delete="form.medium_id = null"
                                    />
                                    <FontAwesomePicker v-if="showIconPicker"
                                        :button-icon="form.css_icon"
                                        @selectIcon="icon => form.css_icon = 'fa fa-' + icon.className"
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
                </slot>

                <div v-if="showFooter"
                    class="modal-footer"
                >
                    <slot name="footer-left"></slot>
                    <span class="pull-right">
                        <button v-if="showCancelButton"
                            :id="model + '-cancel'"
                            type="button"
                            class="btn btn-default"
                            @click="close()"
                        >
                            {{ cancelLabel }}
                        </button>
                        <button v-if="showSaveButton"
                            :id="model + '-save'"
                            class="btn btn-primary ms-3"
                            :disabled="disableSaveButton || processing || (requireTitle && !form.title)"
                            @click="submit()"
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
import Form from "form-backend-validation";
import Select2 from "../forms/Select2.vue";
import NewMediumForm from "../media/NewMediumForm.vue";
import FontAwesomePicker from "./FontAwesomePicker.vue";

export default {
    name: 'Modal',
    emits: ['opened', 'save'],
    expose: ['add', 'update'],
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
        form: {
            type: Form,
            description: 'This property is required, except for non-standard modals',
        },
        url: {
            type: String,
            description: "URL to POST/PATCH endpoint, defaults to '/' + this.model + 's'",
        },
        zIndex: {
            type: Number,
            description: 'Set the z-index of the modal to a specific value',
        },
        css: {
            type: String,
            description: 'Additional CSS styles for the modal container',
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
        showFooter: {
            type: Boolean,
            default: true,
        },
        showCancelButton: {
            type: Boolean,
            default: true,
            description: 'Controls the visibility of the cancel button in the modal footer',
        },
        showSaveButton: {
            type: Boolean,
            default: true,
            description: 'Controls the visibility of the save button in the modal footer',  
        },
        disableSaveButton: {
            type: Boolean,
            default: false,
            description: "Disables the save button (needed if a required non-title field isn't filled)",
        },
        interceptSave: {
            type: Boolean,
            default: false,
            description: 'stop the default submit-logic or intercept to enable post-processing of the form-data',
        },
    },
    data() {
        return {
            method: 'post',
            processing: false,
        };
    },
    mounted() {
        // non-standard modals should declare their own logic
        if (!this.form) return;

        this.globalStore.registerModal(this.modalName);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.modalName].show && !state.modals[this.modalName].lock) {
                // locking the modal means that it won't accept further state-changes
                // caused by opening another modal, while this one is still open
                this.globalStore.lockModal(this.modalName);
                this.processing = false;
                this.form.reset();
                
                const params = state.modals[this.modalName].params;
                if (params) {
                    this.form.populate(params);
                    this.method = this.form.id ? 'patch' : 'post';
                    // use this event to pre-process form-data in the parent-component
                    this.$emit('opened');
                }
            }
        });
    },
    methods: {
        close() {
            this.globalStore.closeModal(this.modalName);
        },
        submit() {
            this.processing = true;
            this.$emit('save', this.form);
            // stop/intercept the default submit-logic
            // when intercepting, the parent can still call this components add()/update() afterwards
            if (this.interceptSave || !this.form) return;

            this.method == 'post'
                ? this.add()
                : this.update();
        },
        add() {
            axios.post(this.endpoint, this.form)
                .then(response => {
                    this.$eventHub.emit(this.model + '-added', response.data);
                    this.close();
                })
                .catch(e => this.processError(e));
        },
        update() {
            axios.patch(this.endpoint + '/' + this.form.id, this.form)
                .then(response => {
                    this.$eventHub.emit(this.model + '-updated', response.data);
                    this.close();
                })
                .catch(e => this.processError(e));
        },
        processError(e) {
            console.log(e);
            this.processing = false;
            this.toast.error(this.errorMessage(e));
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
        endpoint() {
            return this.url ?? '/' + this.model + 's';
        },
    },
};
</script>