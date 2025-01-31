<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @click.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        <span v-if="method === 'post'">
                            {{ trans('global.logbookEntry.create') }}
                        </span>
                            <span v-if="method === 'patch'">
                            {{ trans('global.logbookEntry.edit') }}
                        </span>
                    </h3>
                    <div class="card-tools">
                        <button
                            type="button"
                            class="btn btn-tool"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div
                                class="form-group"
                                :class="form.errors.title ? 'has-error' : ''"
                            >
                                <input
                                    type="text"
                                    id="title"
                                    name="title"
                                    class="form-control"
                                    v-model="form.title"
                                    :placeholder="trans('global.title') + ' *'"
                                    required
                                />
                                <p
                                    v-if="form.errors.title"
                                    class="help-block"
                                    v-text="form.errors.title[0]"
                                ></p>
                            </div>
        
                            <div class="form-group">
                                <Editor
                                    id="description"
                                    name="description"
                                    class="form-control"
                                    :init="tinyMCE"
                                    v-model="form.description"
                                />
                                <p class="help-block"
                                   v-if="form.errors.description"
                                   v-text="form.errors.description[0]"
                                ></p>
                            </div>

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
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="logbook-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="logbook-save"
                            class="btn btn-primary ml-3"
                            @click="submit()"
                        >
                            {{ trans('global.save') }}
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </Transition>
</template>
<script>
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";
import {useGlobalStore} from "../../store/global";
import Editor from "@tinymce/tinymce-vue";
import FontAwesomePicker from "../../../views/forms/input/FontAwesomePicker.vue";
import MediumForm from "../media/MediumForm.vue";
import VueDatePicker from "@vuepic/vue-datepicker";

export default {
    name: 'logbook-entry-modal',
    components: {
        VueDatePicker,
        MediumForm,
        FontAwesomePicker,
        Editor,
        Select2,
    },
    props: {},
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
                logbook_id: '',
                title: '',
                description: '',
                date: null,
                begin: '',
                end: '',
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink link curriculummedia autoresize"
                ],
                {
                    'callback': 'insertContent',
                    'callbackId': this.component_id
                }
            ),
        }
    },
    methods: {
        submit() {
            this.form.begin = this.form.date[0];
            this.form.end = this.form.date[1];

            if (this.method === 'patch') {
                this.update();
            } else {
                this.add();
            }

            this.globalStore.closeModal(this.$options.name);
        },
        add() {
            axios.post('/logbookEntries', this.form)
                .then(response => {
                    this.$eventHub.emit('logbook-entry-added', response.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch('/logbookEntries/' + this.form.id, this.form)
                .then(response => {
                    this.$eventHub.emit('logbook-entry-updated', response.data);
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
            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params;
                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                    this.form.date = [this.form.begin ?? '', this.form.end ?? ''];
                    this.form.description = this.htmlToText(params.description);
                    this.form.logbook_id = params.logbook_id;
                    if (this.form.id != '') {
                        this.method = 'patch';
                    } else {
                        this.method = 'post';
                    }
                }
            }
        });
    },
}
</script>