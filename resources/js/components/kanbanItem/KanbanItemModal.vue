<template>
    <Modal
        ref="modal"
        model="kanbanItem"
        modalName="kanban-item-modal"
        css="min-width: min(90vw, 450px);"
        :form="form"
        :require-title="true"
        :show-display-section="true"
        :show-medium-field="true"
        :allow-multiple-media="true"
        :show-permission-section="hasPermissionsAccess"
        :intercept-save="true"
        @opened="preProcessing()"
        @save="postProcessing()"
    >
        <template #general-extended>
            <div class="mt-3">
                <Editor
                    :id="'description_' + component_id"
                    licenseKey="gpl"
                    :init="tinyMCE"
                    v-model="form.description"
                />
            </div>
        </template>

        <template #permissions>
            <VueDatePicker
                class="mb-3"
                v-model="form.due_date"
                format="dd.MM.yyyy HH:mm"
                :teleport="true"
                time-picker-inline
                :start-time="{ hours: 23, minutes: 59 }"
                :day-names="['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So']"
                :action-row="{
                    selectBtnLabel: 'OK',
                    cancelBtnLabel: trans('global.close'),
                }"
                :placeholder="trans('global.kanbanItem.fields.due_date')"
            />

            <Switch
                id="kanban-item-replace-links"
                class="mb-2"
                label="global.replace_links"
                v-model="form.replace_links"
            />
            <Switch
                id="kanban-item-movable"
                class="mb-2"
                label="global.movable"
                v-model="form.movable"
            />
            <Switch
                id="kanban-item-editable"
                class="mb-2"
                label="global.editable"
                v-model="form.editable"
            />
            <Switch
                id="kanban-item-replace-visibility"
                label="global.visible"
                v-model="form.visibility"
            />

            <VueDatePicker v-if="form.visibility"
                class="mt-2"
                v-model="form.visible_date"
                range
                format="dd.MM.yyyy HH:mm"
                :teleport="true"
                time-picker-inline
                :start-time="[{ hours: 0, minutes: 0 }, { hours: 23, minutes: 59 }]"
                @cleared="form.visible_date = null"
                :day-names="['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So']"
                :action-row="{
                    selectBtnLabel: 'OK',
                    cancelBtnLabel: trans('global.close'),
                }"
                :placeholder="trans('global.visible_until_or_from_to')"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import { VueDatePicker } from "@vuepic/vue-datepicker";
import '@vuepic/vue-datepicker/dist/main.css';
import Editor from '@tinymce/tinymce-vue';
import Switch from '../forms/Switch.vue';

export default {
    name: 'kanban-item-modal',
    components: {
        Modal,
        Editor,
        Switch,
        VueDatePicker,
    },
    data() {
        return {
            component_id: this.$.uid,
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
                due_date: null,
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
    methods: {
        preProcessing() {
            this.form.movable = !this.form.locked;

            if (this.form.visible_from) {
                this.form.visible_date = [this.form.visible_from, this.form.visible_until];
            } else if (this.form.visible_until) {
                this.form.visible_date = [this.form.visible_until, null];
            }
        },
        postProcessing() {
            this.form.locked = !this.form.movable;

            // parse dates to local time, so the server won't have to deal with timezones
            this.form.due_date = this.form.due_date?.toLocaleString() ?? null; // undefined will remove the field from the request

            if (this.form.visible_date === null) {
                this.form.visible_from = null;
                this.form.visible_until = null;
            } else {
                if (this.form.visible_date[1] === null) {
                    this.form.visible_until = this.form.visible_date[0].toLocaleString();
                } else {
                    this.form.visible_from = this.form.visible_date[0].toLocaleString();
                    this.form.visible_until = this.form.visible_date[1].toLocaleString();
                }
            }

            // let the modal-component handle the submit-request
            if (this.form.id) this.$refs.modal.update();
            else {
                this.form.media_subscriptions = this.form.media_subscriptions.map(m => m.medium_id);
                this.$refs.modal.add();
            }
        },
    },
    computed: {
        hasPermissionsAccess() {
            return !this.form.id
                || this.form.owner_id == this.$userId
                || this.$parent.kanban.owner_id == this.$userId
                || this.checkPermission('is_admin');
        },
    },
}
</script>