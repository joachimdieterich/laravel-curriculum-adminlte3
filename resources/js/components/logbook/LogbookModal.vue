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
    ></Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import {useGlobalStore} from "../../store/global";

export default {
    name: 'logbook-modal',
    components: {
        Modal,
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
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
        submit() {
            if (this.method === 'patch') {
                this.update();
            } else {
                this.add();
            }

            this.globalStore.closeModal(this.$options.name);
        },
        add() {
            axios.post('/logbooks', this.form)
                .then(r => {
                    this.$eventHub.emit('logbook-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch('/logbooks/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('logbook-updated', r.data);
                })
                .catch(e => {
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
                const params = state.modals[this.$options.name].params;
                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);

                    if (this.form.id != '') {
                        this.method = 'patch';
                    } else {
                        this.method = 'post';
                    }
                }

                this.$refs.modal.resetForm(this.form);
            }
        });
    },
}
</script>