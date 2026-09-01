<template>
    <Modal
        ref="modal"
        model="kanbanItem"
        modalName="kanban-item-modal"
        css="min-width: min(90vw, 450px);"
        :method="method"
        :processing="processing"
        :require-title="true"
        :show-display-section="true"
        :show-medium-field="true"
        :allow-multiple-media="true"
        :show-permission-section="hasPermissionsAccess"
        @save="(form) => submit(form)"
    >
        <template #general-extended>
            <div class="mt-3">
                <Editor
                    :id="'description_' + component_id"
                    :name="'description_' + component_id"
                    licenseKey="gpl"
                    :init="tinyMCE"
                    v-model="form.description"
                />
            </div>
        </template>

        <template #permissions>
            <VueDatePicker
                id="due_date"
                name="due_date"
                class="mb-3"
                v-model="form.due_date"
                format="dd.MM.yyyy HH:mm"
                :teleport="true"
                locale="de"
                time-picker-inline
                :start-time="{ hours: 23, minutes: 59 }"
                @cleared="form.due_date = ''"
                :select-text="trans('global.ok')"
                :cancel-text="trans('global.close')"
                :placeholder="trans('global.kanbanItem.fields.due_date')"
            />

            <span class="form-check form-switch">
                <input
                    id="kanban-item-replace-links"
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    v-model="form.replace_links"
                    switch
                />
                <label
                    class="form-check-label"
                    for="kanban-item-replace-links"
                >
                    {{ trans('global.replace_links') }}
                </label>
            </span>
            <span class="form-check form-switch">
                <input
                    id="kanban-item-movable"
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    v-model="form.movable"
                    switch
                />
                <label
                    class="form-check-label"
                    for="kanban-item-movable"
                >
                    {{ trans('global.movable') }}
                </label>
            </span>
            <span class="form-check form-switch">
                <input
                    id="kanban-item-editable"
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    v-model="form.editable"
                    switch
                />
                <label
                    class="form-check-label"
                    for="kanban-item-editable"
                >
                    {{ trans('global.editable') }}
                </label>
            </span>
            <span class="form-check form-switch">
                <input
                    id="kanban-item-visibility"
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    v-model="form.visibility"
                    switch
                />
                <label
                    class="form-check-label"
                    for="kanban-item-visibility"
                >
                    {{ trans('global.visible') }}
                </label>
            </span>

            <VueDatePicker v-if="form.visibility"
                id="visible_date"
                name="visible_date"
                class="mt-2"
                v-model="form.visible_date"
                range
                format="dd.MM.yyyy HH:mm"
                :teleport="true"
                locale="de"
                time-picker-inline
                :start-time="[{ hours: 0, minutes: 0 }, { hours: 23, minutes: 59 }]"
                @cleared="form.visible_date = ['', '']"
                :select-text="trans('global.ok')"
                :cancel-text="trans('global.close')"
                :placeholder="trans('global.visible_until_or_from_to')"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import VueDatePicker from "@vuepic/vue-datepicker";
import Editor from '@tinymce/tinymce-vue';
import {useGlobalStore} from "../../store/global";
import {useToast} from "vue-toastification";

export default {
    name: 'kanban-item-modal',
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            medium: null,
            processing: false,
            form: new Form({
                id: null,
                title: '',
                description: '',
                kanban_id: '',
                kanban_status_id: '',
                order_id: 0,
                owner_id: null,
                color: '#f4f4f4',
                media_subscriptions: [],
                due_date: '',
                movable: true, // replaces 'locked' to match shown translation
                locked: false, // actual value that gets sent to backend
                editable: true,
                replace_links: false,
                visibility: true,
                visible_date: null,
                visible_from: '',
                visible_until: '',
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink", "link", "lists", "table", "code", "autoresize",
                ],
                {
                    'callback': 'insertContent',
                    'callbackId': this.component_id
                },
                "bold underline italic | alignleft aligncenter alignright alignjustify | table",
                "bullist numlist outdent indent | mathjax link code"
            ),
        }
    },
    setup() {
        const globalStore = useGlobalStore();
        const toast = useToast();
        return {
            globalStore,
            toast,
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
                    this.form.populate(params.item);
                    this.method = params.method;
                    this.form.movable = !this.form.locked;

                    if (this.form.media_subscriptions.length > 0) this.medium = this.form.media_subscriptions[0].medium;
                    else this.medium = null; // needs to be reset

                    if (this.form.visible_from == null && this.form.visible_until != null) {
                        this.form.visible_date = [this.form.visible_until, null]; // second date needs to be null
                    } else {
                        // unset dates need to be set to empty strings, becuase null will show 1970-01-01T00:00:00.000Z
                        this.form.visible_date = [this.form.visible_from ?? '', this.form.visible_until ?? ''];
                    }
                }

                this.$refs.modal.resetForm(this.form);
            }
        });
    },
    methods: {
        close() {
            this.globalStore.closeModal(this.$options.name);
        },
        submit(formData) {
            this.form.populate(formData);
            this.form.locked = !this.form.movable;
            // parse dates to local time, so the server won't have to deal with timezones
            this.form.due_date = this.form.due_date?.toLocaleString() ?? null; // undefined will remove the field from the request
            if (this.form.visible_date[1] === null) {
                this.form.visible_until = this.form.visible_date[0].toLocaleString();
            } else {
                this.form.visible_from = this.form.visible_date[0].toLocaleString();
                this.form.visible_until = this.form.visible_date[1].toLocaleString();
            }

            this.processing = true;

            if (this.method == 'patch') {
                this.update();
            } else {
                this.form.media_subscriptions = this.form.media_subscriptions.map(m => m.medium_id);
                this.add();
            }
        },
        add() {
            axios.post('/kanbanItems', this.form)
                .then(r => {
                    this.$eventHub.emit('kanban-item-added-' + this.form.kanban_status_id, r.data);
                    this.close()
                })
                .catch(e => {
                    this.toast.error(this.errorMessage(e));
                    console.log(e);
                });
        },
        update() {
            axios.patch('/kanbanItems/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('kanban-item-updated-' + r.data.kanban_status_id, r.data);
                    this.close();
                })
                .catch(e => {
                    console.log(e);
                    this.toast.error(this.errorMessage(e));
                });
        },
    },
    computed: {
        hasPermissionsAccess() {
            return this.method == 'post'
                || this.form.owner_id == this.$userId
                || this.$parent.kanban.owner_id == this.$userId
                || this.checkPermission('is_admin');
        },
    },
    components: {
        Modal,
        Editor,
        VueDatePicker,
    },
}
</script>