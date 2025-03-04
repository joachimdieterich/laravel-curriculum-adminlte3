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
                            {{ trans('global.marker.create') }}
                        </span>
                        <span v-if="method === 'patch'">
                            {{ trans('global.marker.edit') }}
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
                            <div class="form-group">
                                <input
                                    id="title"
                                    name="title"
                                    type="text"
                                    class="form-control"
                                    v-model.trim="form.title"
                                    :placeholder="trans('global.title') + ' *'"
                                    required
                                />
                                <p v-if="form.errors.title"
                                    class="help-block"
                                    v-text="form.errors.title[0]"
                                ></p>
                            </div>

                            <div class="form-group">
                                <input
                                    id="teaser_text"
                                    name="teaser_text"
                                    type="text"
                                    class="form-control"
                                    v-model.trim="form.teaser_text"
                                    :placeholder="trans('global.marker.fields.teaser_text')"
                                    required
                                />
                                <p v-if="form.errors?.teaser_text"
                                    class="help-block"
                                    v-text="form.errors?.teaser_text[0]"
                                ></p>
                            </div>

                            <div class="form-group">
                                <Editor
                                    id="description"
                                    name="description"
                                    :placeholder="trans('global.marker.fields.description')"
                                    class="form-control"
                                    :init="tinyMCE"
                                    v-model="form.description"
                                />
                            </div>

                            <div class="form-group">
                                <label for="author">
                                    {{ trans('global.marker.fields.author') }}
                                </label>
                                <input
                                    id="author"
                                    name="author"
                                    type="text"
                                    class="form-control"
                                    v-model.trim="form.author"
                                    :placeholder="trans('global.marker.fields.author')"
                                    required
                                />
                                <p v-if="form.errors?.author"
                                    class="help-block"
                                    v-text="form.errors?.author[0]"
                                ></p>
                            </div>

                            <div class="form-group">
                                <label for="tags">
                                    {{ trans('global.marker.fields.tags') }}
                                </label>
                                <input
                                    id="tags"
                                    name="tags"
                                    type="text"
                                    class="form-control"
                                    v-model.trim="form.tags"
                                    :placeholder="trans('global.marker.fields.tags')"
                                    required
                                />
                                <p v-if="form.errors?.tags"
                                    class="help-block"
                                    v-text="form.errors?.tags[0]"
                                ></p>
                            </div>

                            <Select2
                                id="map_marker_type"
                                name="map_marker_type"
                                url="/mapMarkerTypes"
                                model="mapMarkerType"
                                :selected="form.type_id"
                                @selectedValue="(id) => {
                                    this.form.type_id = id[0];
                                }"
                            />

                            <Select2
                                id="map_marker_category"
                                name="map_marker_category"
                                url="/mapMarkerCategories"
                                model="mapMarkerCategory"
                                :selected="form.category_id"
                                @selectedValue="(id) => {
                                    this.form.category_id = id[0];
                                }"
                            />

                            <div class="form-group">
                                <label for="latitude">
                                    {{ trans('global.marker.fields.latitude') }} *
                                </label>
                                <input
                                    id="latitude"
                                    name="latitude"
                                    type="number"
                                    class="form-control"
                                    v-model.trim="form.latitude"
                                    :placeholder="trans('global.marker.fields.latitude')"
                                    required
                                />
                                <p v-if="form.errors?.latitude"
                                    class="help-block"
                                    v-text="form.errors?.latitude[0]"
                                ></p>
                            </div>

                            <div class="form-group">
                                <label for="longitude">
                                    {{ trans('global.marker.fields.longitude') }} *
                                </label>
                                <input
                                    id="longitude"
                                    name="longitude"
                                    type="number"
                                    class="form-control"
                                    v-model.trim="form.longitude"
                                    :placeholder="trans('global.marker.fields.longitude')"
                                    required
                                />
                                <p v-if="form.errors?.longitude"
                                    class="help-block"
                                    v-text="form.errors?.longitude[0]"
                                ></p>
                            </div>

                            <div class="form-group">
                                <label for="address">
                                    {{ trans('global.address') }}
                                </label>
                                <input
                                    id="address"
                                    name="address"
                                    type="text"
                                    class="form-control"
                                    v-model.trim="form.address"
                                    :placeholder="trans('global.address')"
                                    required
                                />
                                <p v-if="form.errors?.address"
                                    class="help-block"
                                    v-text="form.errors?.address[0]"
                                ></p>
                            </div>
        
                            <div class="form-group">
                                <label for="url">
                                    {{ trans('global.url') }}
                                </label>
                                <input
                                    id="url"
                                    name="url"
                                    type="text"
                                    class="form-control"
                                    v-model.trim="form.url"
                                    :placeholder="trans('global.url')"
                                />
                                <p v-if="form.errors?.url"
                                    class="help-block"
                                    v-text="form.errors?.url[0]"
                                ></p>
                            </div>

                            <div>
                                <label for="url_title">
                                    {{ trans('global.url_title') }}
                                </label>
                                <input
                                    id="url_title"
                                    name="url_title"
                                    type="text"
                                    class="form-control"
                                    v-model.trim="form.url_title"
                                    :placeholder="trans('global.url_title')"
                                />
                                <p v-if="form.errors?.url_title"
                                    class="help-block"
                                    v-text="form.errors?.url_title[0]"
                                ></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="marker-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="marker-save"
                            class="btn btn-primary ml-3"
                            :disabled="!form.title || !form.latitude || !form.longitude || !form.type_id || !form.category_id"
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
import axios from "axios";
import Editor from "@tinymce/tinymce-vue";
import Select2 from "../forms/Select2.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'map-marker-modal',
    components: {
        Editor,
        Select2
    },
    props: {
        map: {
            type: Object
        }
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
            url: '/mapMarkers',
            form: new Form({
                id: '',
                title: '',
                teaser_tesxt: '',
                description: '',
                author: '',
                type_id: 1,
                category_id: 1,
                tags: '',
                latitude: null,
                longitude: null,
                address: '',
                url: '',
                url_title: '',
                map_id: '',
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
            if (this.method == 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            axios.post(this.url, this.form)
                .then(r => {
                    this.$eventHub.emit('marker-added', r.data);
                    this.globalStore?.closeModal(this.$options.name)
                })
                .catch(e => {
                    console.log(e);
                });
        },
        update() {
            axios.patch(this.url + '/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('marker-updated', r.data);
                    this.globalStore?.closeModal(this.$options.name)
                })
                .catch(e => {
                    console.log(e);
                });
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
                    this.form.url = this.$decodeHTMLEntities(params.url);
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