<template>
    <Modal
        model="terminalObjective"
        modalName="terminal-objective-modal"
        :method="method"
        :processing="processing"
        :show-general-header="true"
        :show-display-section="true"
        :show-permission-section="true"
        :disable-save-button="!form.title"
        @save="submit"
    >
        <template #general>
            <div class="mb-3">
                <label for="title" class="form-label">{{ trans('global.terminalObjective.fields.title') }} *</label>
                <Editor
                    id="title"
                    licenseKey="gpl"
                    :init="tinyMCE_title"
                    v-model="form.title"
                />
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">{{ trans('global.map.fields.description') }}</label>
                <Editor
                    id="description"
                    licenseKey="gpl"
                    :init="tinyMCE_description"
                    v-model="form.description"
                />
            </div>

            <Select2
                id="objective_type_id"
                url="/objectiveTypes"
                model="objectiveType"
                css="mb-3"
                :label="trans('global.objectiveType.title_singular') + ' *'"
                :selected="form.objective_type_id"
                @selectedValue="id => form.objective_type_id = id[0]"
            />

            <div>
                <label for="time_approach" class="form-label">{{ trans('global.terminalObjective.fields.time_approach') }}</label>
                <input
                    id="time_approach"
                    type="text"
                    class="form-control"
                    v-model="form.time_approach"
                />
            </div>
        </template>
        <template #permissions>
            <Switch
                id="terminal-visibility"
                label="global.visibility"
                v-model="form.visibility"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Editor from "@tinymce/tinymce-vue";
import Select2 from "../forms/Select2.vue";
import Switch from '../forms/Switch.vue';

export default {
    name: 'terminal-objective-modal',
    components: {
        Modal,
        Editor,
        Select2,
        Switch,
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
                color: '#008000',
                time_approach: '',
                curriculum_id: '',
                objective_type_id: 1,
                visibility: true,
            }),
            tinyMCE_title: this.$initTinyMCE(
                [
                    "autolink", "link", "table", "lists", "autoresize",
                ],
                {
                    public: 1,
                    subscribeSelected: true,
                    subscribable_id: this.form?.curriculum_id,
                    subscribable_type: 'App\\Curriculum',
                    callbackId: this.component_id,
                    placeholder: this.trans('global.objective_content'),
                },
                "bold underline italic | alignleft aligncenter alignright alignjustify",
                ""
            ),
            tinyMCE_description: this.$initTinyMCE(
                [
                    "autolink", "link", "table", "lists", "autoresize", "code", "fullscreen",
                ],
                {
                    public: 1,
                    subscribeSelected: true,
                    subscribable_id: this.form?.curriculum_id,
                    subscribable_type: 'App\\Curriculum',
                    callbackId: this.component_id,
                },
                "bold underline italic | alignleft aligncenter alignright alignjustify | bullist numlist | link code fullscreen",
                ""
            ),
        }
    },
    methods: {
        submit() {
            this.processing = true;

            if (this.method == 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            axios.post('/terminalObjectives', this.form)
                .then(r => {
                    this.$eventHub.emit('terminal-objective-added', r.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    console.log(e);
                    this.processing = false;
                    this.toast.error(this.form.description.length > 65535 ? this.trans('global.error.too_long') : this.trans('global.error.default'));
                });
        },
        update() {
            axios.patch('/terminalObjectives/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('terminal-objective-updated', r.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    console.log(e);
                    this.processing = false;
                    this.toast.error(this.form.description.length > 65535 ? this.trans('global.error.too_long') : this.trans('global.error.default'));
                });
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
                    this.form.populate(params);
                    this.method = this.form.id ? 'patch' : 'post';
                }
            }
        });
    },
}
</script>