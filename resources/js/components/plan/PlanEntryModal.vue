<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
             class="modal-mask"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        <span v-if="method === 'post'">
                            {{ trans('global.planEntry.create') }}
                        </span>
                        <span v-else>
                            {{ trans('global.planEntry.edit') }}
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
                    <div class="form-group input-group align-items-center">
                        <v-swatches
                            :swatch-size="49"
                            :trigger-style="{}"
                            popover-to="right"
                            style="height: 42px;"
                            v-model="this.form.color"
                            show-fallback
                            fallback-input-type="color"
                            @input="(id) => {
                                if (id.isInteger) {
                                    this.form.color = id;
                                }
                            }"
                            :max-height="300"
                        ></v-swatches>
                        <input
                            id="title"
                            name="title"
                            type="text"
                            class="form-control ml-3"
                            v-model.trim="form.title"
                            :placeholder="trans('global.planEntry.fields.title') + ' *'"
                            required
                        />
                        <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                    </div>

                    <Editor
                        :id="'description_' + component_id"
                        :name="'description_' + component_id"
                        :init="tinyMCE"
                        v-model="form.description"
                    />

                    <div class="form-group mt-3 mb-0" style="max-width: 490px;">
                        <font-awesome-picker
                            :searchbox="trans('global.select_icon')"
                            @select-icon="(icon) => this.form.css_icon = 'fa fa-' + icon.className"
                        ></font-awesome-picker>
                    </div>

                    <!-- <div class="form-group">
                       <MediumForm :form="form"
                            :id="component_id"
                            :medium_id="form.medium_id"
                            accept="image/*"
                        />
                    </div> -->
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="entry-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="entry-save"
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
import Editor from "@tinymce/tinymce-vue";
import FontAwesomePicker from "../../../views/forms/input/FontAwesomePicker.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'plan-entry-modal',
    props: {
        plan: {
            type: Object,
        },
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
                title: '',
                description: '',
                plan_id: this.plan.id,
                color:'#27AF60',
                css_icon: 'fa fa-calendar-day',
                order_id: 0,
                medium_id: null,
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink link lists table code autoresize"
                ],
                {
                    'callback': 'insertContent',
                    'callbackId': this.component_id
                },
                "bold underline italic | alignleft aligncenter alignright | table",
                "bullist numlist outdent indent | mathjax link code",
            ),
        }
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params;
                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                    this.form.date = [new Date(this.form.begin) ?? '', new Date(this.form.end) ?? ''];
                    this.form.description = this.$decodeHTMLEntities(params.description);

                    if (this.form.id != '') {
                        this.method = 'patch';
                    } else {
                        this.method = 'post';
                    }
                }
            }
        });
    },
    methods: {
        setIcon(icon) {
            this.form.css_icon = 'fa fa-' + icon.className;
        },
        submit() {
            if (this.method == 'post') {
                this.add();
            } else {
                this.update();
            }

            this.globalStore.closeModal(this.$options.name);
        },
        add() {
            axios.post('/planEntries', this.form)
                .then(response => {
                    this.$eventHub.emit('plan-entry-added', response.data);
                })
                .catch(error => {
                    console.log(error)
                });
        },
        update() {
            axios.patch('/planEntries/' + this.form.id, this.form)
                .then(response => {
                    this.$eventHub.emit('plan-entry-updated', response.data);
                })
                .catch(error => {
                    console.log(error)
                });
        },
    },
    components: {
        Editor,
        FontAwesomePicker,
    },
}
</script>
