<template>
    <Modal
        ref="modal"
        model="logbook"
        modalName="logbook-modal"
        :method="method"
        :processing="processing"
        :require-title="true"
        :show-description-field="true"
        :show-owner-field="form.id && checkPermission('is_teacher')"
        :show-display-section="true"
        :show-medium-field="true"
        :show-icon-picker="true"
        @save="(form) => submit(form)"
    ></Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import {useGlobalStore} from "../../store/global";
import {useToast} from "vue-toastification";

export default {
    name: 'logbook-modal',
    components: {
        Modal,
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
                title:  '',
                description:  '',
                owner_id: null,
                medium_id: null,
                color:'#27AF60',
                css_icon: 'fa fa-book',
            }),
        }
    },
    methods: {
        submit(formData) {
            console.log(formData);
            this.form.populate(formData);
            this.processing = true;

            if (this.method === 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            axios.post('/logbooks', this.form)
                .then(r => {
                    this.$eventHub.emit('logbook-added', r.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.processing = false;
                    this.toast.error(this.errorMessage(e));
                    console.log(e.response);
                });
        },
        update() {
            axios.patch('/logbooks/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('logbook-updated', r.data);
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
                }

                this.$refs.modal.resetForm(this.form);
            }
        });
    },
}
</script>