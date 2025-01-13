<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
             class="modal-mask"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                    <span v-if="method === 'post'">
                        {{ trans('global.logbook.create') }}
                    </span>
                        <span v-if="method === 'patch'">
                        {{ trans('global.logbook.edit') }}
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
                    <div
                        class="form-logbook "
                        :class="form.errors.title ? 'has-error' : ''"
                    >
                        <label for="title">{{ trans('global.logbook.fields.title') }} *</label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            class="form-control"
                            v-model="form.title"
                            :placeholder="trans('global.title')"
                            required
                        />
                        <p v-if="form.errors.title"
                            class="help-block"
                            v-text="form.errors.title[0]"
                        ></p>
                    </div>

                    <div class="form-group">
                        <label for="description">
                            {{ trans('global.logbook.fields.description') }}
                        </label>
                        <Editor
                            id="description"
                            name="description"
                            class="form-control"
                            :init="tinyMCE"
                            v-model="form.description"
                        />
                        <p v-if="form.errors.description"
                            class="help-block"
                            v-text="form.errors.description[0]"
                        ></p>
                    </div>

                    <div class="card-header border-bottom">
                        <h5 class="card-title">
                            Darstellung
                        </h5>
                    </div>
                    
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <v-swatches
                                :swatch-size="49"
                                :trigger-style="{}"
                                popover-to="right"
                                v-model="this.form.color"
                                show-fallback
                                fallback-input-type="color"
                                @input="(id) => {
                                    if(id.isInteger) {
                                        this.form.color = id;
                                    }
                                }"
                                :max-height="300"
                            />
                            <MediumForm
                                class="pull-right"
                                :id="'medium_id_' + component_id"
                                :medium_id="form.medium_id"
                                accept="image/*"
                                :selected="this.form.medium_id"
                                @selectedValue="(id) => {
                                    this.form.medium_id = id;
                                }"
                            />
                            <div class="dropdown">
                                <button
                                    class="btn btn-default"
                                    style="width: 42px; padding: 6px 0px;"
                                    type="button"
                                    data-toggle="dropdown"
                                    aria-expanded="false"
                                >
                                    <i :class="form.css_icon + ' pt-2'"></i>
                                </button>
                                <font-awesome-picker
                                    class="dropdown-menu dropdown-menu-right"
                                    style="min-width: min(385px,90vw);"
                                    :searchbox="trans('global.select_icon')"
                                    v-on:selectIcon="setIcon"
                                />
                            </div>
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
                            class="btn btn-primary"
                            @click="submit(method)"
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

export default {
    name: 'logbook-modal',
    components:{
        MediumForm, FontAwesomePicker,
        Editor,
        Select2
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
            url: '/logbooks',
            form: new Form({
                'id': '',
                'title':  '',
                'description':  '',
                'medium_id': null,
                'color':'#27AF60',
                'css_icon': 'fa fa-book',
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
        submit(method) {
            if (method === 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            axios.post(this.url, this.form)
                .then(r => {
                    this.$eventHub.emit('logbook-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch(this.url + '/' + this.form.id, this.form)
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
            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params;
                this.form.reset();
                if (typeof (params) !== 'undefined'){
                    this.form.populate(params);

                    this.form.description = this.htmlToText(params.description);
                    if (this.form.id != ''){
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
