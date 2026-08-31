<template>
    <Modal
        ref="modal"
        model="kanbanStatus"
        modalName="kanban-status-modal"
        :method="method"
        :processing="processing"
        :require-title="true"
        :show-display-section="true"
        :allow-overflow="true"
        :show-permission-section="hasPermissionsAccess"
        @save="(form) => submit(form)"
    >
        <template #permissions>
            <div class="form-check form-switch">
                <input
                    id="kanban-status-movable"
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    v-model="form.movable"
                    switch
                />
                <label
                    class="form-check-label"
                    for="kanban-status-movable"
                >
                    {{ trans('global.movable') }}
                </label>
            </div>

            <div class="form-check form-switch">
                <input
                    id="kanban-status-editable"
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    v-model="form.editable"
                    switch
                />
                <label
                    class="form-check-label"
                    for="kanban-status-editable"
                >
                    {{ trans('global.editable') }}
                </label>
            </div>
            
            <div class="form-check form-switch">
                <input
                    id="kanban-status-visibility"
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    v-model="form.visibility"
                    switch
                />
                <label
                    class="form-check-label"
                    for="kanban-status-visibility"
                >
                    {{ trans('global.visibility') }}
                </label>
            </div>
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import {useGlobalStore} from "../../store/global";
import {useToast} from "vue-toastification";

export default {
    name: 'kanban-status-modal',
    components: { Modal },
    props: {
        kanban: {
            type: Object,
            required: true,
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
            processing: false,
            form: new Form({
                id: '',
                title: '',
                kanban_id: this.kanban.id,
                owner_id: null,
                movable: true, // replaces 'locked' to match shown translation
                locked: false, // actual value that gets sent to backend
                editable: true,
                visibility: true,
                visible_from: null,
                visible_until: null,
                color: '#f4f4f4',
            }),
        }
    },
    methods: {
        submit(formData) {
            this.form.populate(formData);
            this.form.locked = !this.form.movable;
            this.processing = true;

            if (this.method == 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            axios.post('/kanbanStatuses', this.form)
                .then(r => {
                    this.$eventHub.emit('kanban-status-created', r.data);
                    this.globalStore?.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.toast.error(this.errorMessage(e));
                    console.log(e);
                });
        },
        update() {
            axios.patch('/kanbanStatuses/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('kanban-status-updated', r.data);
                    this.globalStore?.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.toast.error(this.errorMessage(e));
                });
        },
    },
    computed: {
        hasPermissionsAccess() {
            return this.method == 'post'
                || this.form.owner_id == this.$userId
                || this.kanban.owner_id == this.$userId
                || this.checkPermission('is_admin');
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                this.processing = false;
                this.form.reset();

                const params = state.modals[this.$options.name].params;
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params.status);
                    this.method = params.method;
                    this.form.movable = !this.form.locked;
                }

                this.$refs.modal.resetForm(this.form);
            }
        });
    },
}
</script>