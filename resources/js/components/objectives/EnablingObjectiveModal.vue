<template>
    <Modal
        model="enablingObjective"
        modalName="enabling-objective-modal"
        :method="method"
        :processing="processing"
        :show-general-header="true"
        :show-permission-section="true"
        @save="submit"
    >
        <template #general>
            <div class="mb-3">
                <label for="description" class="form-label">{{ trans('global.title') }} *</label>
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

            <div class="mb-3">
                <CSelect
                    inputId="enabling-level"
                    :searchable="false"
                    url="/levels"
                    label="text"
                    :selected="form.level_id"
                    :handle-fetched-selected-fetch-data="data => {
                        data[0].text = data[0].title;
                        return data[0];
                    }"
                    @selectedValue="level => form.level_id = level?.id ?? null"
                >
                    <template #label>
                        <label for="enabling-level" class="form-label">
                            {{ trans('global.level.title_singular') }}
                        </label>
                    </template>
                </CSelect>
            </div>

            <div>
                <label for="time_approach" class="form-label">{{ trans('global.enablingObjective.fields.time_approach') }}</label>
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
                id="enabling-visibility"
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
import CSelect from "../forms/Select.vue";
import Switch from '../forms/Switch.vue';

export default {
    name: 'enabling-objective-modal',
    components: {
        Modal,
        CSelect,
        Editor,
        Switch,
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            processing: false,
            form: new Form({
                id: '',
                title: '',
                description: '',
                time_approach: '',
                curriculum_id: '',
                terminal_objective_id: '',
                level_id: null,
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
            axios.post('/enablingObjectives', this.form)
                .then(r => {
                    this.$eventHub.emit('enabling-objective-added', r.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    console.log(e);
                    this.processing = false;
                    this.toast.error(this.form.description.length > 65535 ? this.trans('global.error.too_long') : this.trans('global.error.default'));
                });
        },
        update() {
            axios.patch('/enablingObjectives/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('enabling-objective-updated', r.data);
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