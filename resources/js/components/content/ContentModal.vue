<template>
    <Modal
        ref="modal"
        model="content"
        modalName="content-modal"
        :method="method"
        :processing="processing"
        :require-title="true"
        :disable-save-button="!form.content"
        @save="(form) => submit(form)"
    >
        <template #general-extended>
            <div class="mt-3">
                <Editor
                    id="content"
                    name="content"
                    licenseKey="gpl"
                    :init="tinyMCE"
                    v-model="form.content"
                />
            </div>
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Editor from '@tinymce/tinymce-vue';
import {useGlobalStore} from "../../store/global";

export default {
    name: 'content-modal',
    components: {
        Modal,
        Editor,
    },
    setup() {
        return { globalStore: useGlobalStore() }
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            processing: false,
            form: new Form({
                id: '',
                title: '',
                content: '',
                subscribable_id: null,
                subscribable_type: null,
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink", "link", "table", "lists", "code", "autoresize",
                ],
                {
                    callback: 'insertContent',
                    callbackId: this.component_id,
                    placeholder: window.trans.global.description + ' *',
                },
                "bold underline italic | alignleft aligncenter alignright alignjustify | bullist numlist | link mathjax code",
                ""
            ),
        }
    },
    methods: {
        submit(formData) {
            this.form.populate(formData);
            this.processing = true;

            if (this.method == 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            axios.post('/contents', this.form)
                .then(r => {
                    this.$eventHub.emit('content-added', r.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.processing = false;
                    console.log(e);
                });
        },
        update() {
            axios.patch('/contents/' + this.form.id, this.form)
                .then(r => {
                    r.data.subscribable_id = this.form.subscribable_id;
                    this.$eventHub.emit('content-updated', r.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.processing = false;
                    console.log(e);
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
                    this.form.subscribable_type = params.subscribable_type;
                    this.form.subscribable_id = params.subscribable_id;
                    this.form.populate(params);
                }

                this.$refs.modal.resetForm(this.form);
            }
        });
    },
}
</script>