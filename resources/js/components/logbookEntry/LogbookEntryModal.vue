<template>
    <Modal
        ref="modal"
        model="logbookEntry"
        modalName="logbook-entry-modal"
        :method="method"
        :processing="processing"
        :require-title="true"
        @save="(form) => submit(form)"
    >
        <template #general-extended>
            <div class="mt-3">
                <Editor
                    id="description"
                    name="description"
                    class="form-control"
                    licenseKey="gpl"
                    :init="tinyMCE"
                    v-model="form.description"
                />
            </div>
            <div class="mt-3">
                <VueDatePicker
                    id="date"
                    name="date"
                    v-model="form.date"
                    :range="{ partialRange: false }"
                    format="dd.MM.yyyy HH:mm"
                    :teleport="true"
                    locale="de"
                    :placeholder="trans('global.selectDateRange')"
                    :select-text="trans('global.ok')"
                    :cancel-text="trans('global.close')"
                    @cleared="form.date = ['', '']"
                />
            </div>        
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Editor from "@tinymce/tinymce-vue";
import VueDatePicker from "@vuepic/vue-datepicker";
import {useGlobalStore} from "../../store/global";
import {useToast} from "vue-toastification";

export default {
    name: 'logbook-entry-modal',
    components: {
        Modal,
        Editor,
        VueDatePicker,
    },
    setup() {
        return {
            globalStore: useGlobalStore(),
            toast: useToast(),
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            processing: false,
            form: new Form({
                id: '',
                logbook_id: '',
                title: '',
                description: '',
                date: null,
                begin: '',
                end: '',
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink", "link", "lists", "autoresize",
                ],
                {
                    'callback': 'insertContent',
                    'callbackId': this.component_id
                },
                "bold underline italic | alignleft aligncenter alignright alignjustify | bullist numlist | link",
                "",
            ),
        }
    },
    methods: {
        submit(formData) {
            this.form.populate(formData);
            this.processing = true;
            this.form.begin = this.form.date[0].toLocaleString();
            this.form.end = this.form.date[1].toLocaleString();

            if (this.method === 'patch') {
                this.update();
            } else {
                this.add();
            }

        },
        add() {
            axios.post('/logbookEntries', this.form)
                .then(response => {
                    this.$eventHub.emit('logbook-entry-added', response.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.processing = false;
                    this.toast.error(this.errorMessage(e));
                    console.log(e.response);
                });
        },
        update() {
            axios.patch('/logbookEntries/' + this.form.id, this.form)
                .then(response => {
                    this.$eventHub.emit('logbook-entry-updated', response.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.processing = false;
                    this.toast.error(this.errorMessage(e));
                    console.log(e.response);
                });
        },
        setIcon(selectedIcon) {
            this.form.css_icon = 'fa fa-' + selectedIcon.className;
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
                    this.form.date = [this.form.begin ?? '', this.form.end ?? ''];
                }

                this.$refs.modal.resetForm(this.form);
            }
        });
    },
}
</script>