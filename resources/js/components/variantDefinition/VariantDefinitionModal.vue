<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
             class="modal-mask"
        >
        <div class="modal-container">
            <div class="card-header">
                <h3 class="card-title">
                    <span v-if="method === 'post'">
                        {{ trans('global.variantDefinition.create') }}
                    </span>
                    <span v-if="method === 'patch'">
                        {{ trans('global.variantDefinition.edit') }}
                    </span>
                </h3>
                <div class="card-tools">
                    <button type="button"
                            class="btn btn-tool"
                            @click="globalStore?.closeModal($options.name)">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <form >
                <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                    <div class="form-group "
                        :class="form.errors.title ? 'has-error' : ''"
                          >
                        <label for="title">
                            {{ trans('global.variantDefinition.fields.title') }} *
                        </label>
                        <input
                            type="text" id="title"
                            name="title"
                            class="form-control"
                            v-model="form.title"
                            placeholder="Title"
                            required
                            />
                         <p class="help-block"
                            v-if="form.errors.title"
                            v-text="form.errors.title[0]"></p>
                    </div>

                    <div class="form-group">
                        <label for="description">
                            {{ trans('global.variantDefinition.description') }}
                        </label>
                        <Editor
                            id="description"
                            name="description"
                            class="form-control"
                            licenseKey="gpl"
                            :init="tinyMCE"
                            v-model="form.description"
                        />
                        <p class="help-block"
                           v-if="form.errors.description"
                           v-text="form.errors.description[0]"
                        ></p>
                    </div>

                    <v-swatches
                        :swatch-size="49"
                        :trigger-style="{}"
                        popover-to="right"
                        v-model="this.form.color"

                        show-fallback
                        fallback-input-type="color"

                        @input="(id) => {
                                    if (id.isInteger){
                                      this.form.color = id;
                                    }
                                }"
                        :max-height="300"
                    ></v-swatches>

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
                            style="min-width: 400px;"
                            :searchbox="trans('global.select_icon')"
                            v-on:selectIcon="setIcon"
                        ></font-awesome-picker>
                    </div>

                </div>
                <div class="card-footer">
                     <span class="pull-right">
                         <button
                             id="objective-cancel"
                             type="button"
                             class="btn btn-default"
                             @click="globalStore?.closeModal($options.name)">
                             {{ trans('global.cancel') }}
                         </button>
                         <button
                             id="objective-save"
                             class="btn btn-primary"
                             @click="submit(method)" >
                             {{ trans('global.save') }}
                         </button>
                    </span>
                </div>
            </form>
        </div>
    </div>
    </Transition>
</template>
<script>
import Form from 'form-backend-validation';
import Editor from '@tinymce/tinymce-vue';
import {useGlobalStore} from "../../store/global";
import FontAwesomePicker from "../../../views/forms/input/FontAwesomePicker.vue";

export default {
    name: 'variant-definition-modal',
    components: {
        FontAwesomePicker,
        Editor
    },
    props: {},
    setup () {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            url: '/variantDefinitions',
            form: new Form({
                'id':'',
                'title': '',
                'description': '',
                'color':'#27AF60',
                'css_icon': 'fa fa-book',
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink", "link", "autoresize",
                ],
                {
                    'callback': 'insertContent',
                    'callbackId': this.component_id
                }
            ),
            search: '',
        }
    },
    methods: {
        async submit(method) {
            try {
                if (method === 'patch') {
                    this.update();
                } else {
                    this.add();
                }
            } catch (error) {
                console.log(error);
            }
        },
        add() {
            axios.post(this.url, this.form)
                .then(r => {
                    this.$eventHub.emit('variant-definition-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch(this.url + '/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('variant-definition-updated', r.data);
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

                    this.form.description = this.htmlToText(params.description);
                    if (this.form.id !== '') {
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
