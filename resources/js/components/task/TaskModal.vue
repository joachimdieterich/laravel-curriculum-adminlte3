<template>
    <Modal
        ref="modal"
        model="task"
        modalName="task-modal"
        :require-title="true"
        @save="submit(form)"
    >
        <template #general-extended>
            <div class="my-3">
                <Editor
                    id="description"
                    licenseKey="gpl"
                    :init="tinyMCE"
                    v-model="form.description"
                />
            </div>

            <div class="mb-3">
                <label for="dp-input-task-start-date" class="form-label">{{ trans('global.task.fields.start_date') }}</label>
                <VueDatePicker
                    uid="task-start-date"
                    v-model="form.start_date"
                    :teleport="true"
                    locale="de"
                    time-picker-inline
                    format="dd.MM.yyy HH:mm"
                    :select-text="trans('global.ok')"
                    :cancel-text="trans('global.close')"
                />
            </div>

            <div>
                <label for="dp-input-task-due-date" class="form-label">{{ trans('global.task.fields.due_date') }}</label>
                <VueDatePicker
                    uid="task-due-date"
                    v-model="form.due_date"
                    :teleport="true"
                    locale="de"
                    time-picker-inline
                    format="dd.MM.yyy HH:mm"
                    :select-text="trans('global.ok')"
                    :cancel-text="trans('global.close')"
                />
            </div>
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Editor from '@tinymce/tinymce-vue';
import { VueDatePicker } from "@vuepic/vue-datepicker";
import '@vuepic/vue-datepicker/dist/main.css';

export default {
    name: 'task-modal',
    components: {
        Modal,
        Editor,
        VueDatePicker,
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            processing: false,
            form: new Form({
                id: null,
                title: '',
                description: '',
                start_date: null,
                due_date: null,
                subscribable_type: null,
                subscribable_id: null,
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink", "link", "autoresize",
                ],
                {
                    'callback': 'insertContent',
                    'callbackId': this.component_id
                }
            ),
        }
    },
    methods: {
        submit(formData) {
            this.processing = true;
            this.form.populate(formData);

            if (this.method == 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            axios.post('/tasks', this.form)
                .then(r => {
                    this.$eventHub.emit('task-added', r.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    console.log(e);
                    this.processing = false;
                    this.toast.error(this.errorMessage(e));
                });
        },
        update() {
            axios.patch('/tasks/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('task-updated', r.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    console.log(e);
                    this.processing = false;
                    this.toast.error(this.errorMessage(e));
                });
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
                    this.form.populate(params);
                    this.method = this.form.id ? 'patch' : 'post';
                }

                this.$refs.modal.resetForm(this.form);
            }
        });
    },
}
</script>