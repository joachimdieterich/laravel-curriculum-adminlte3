<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @mouseup.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="modal-header">
                    <span class="card-title">
                        {{ method == 'post' ? trans('global.marker.create') : trans('global.marker.edit') }}
                    </span>
                    <button
                        type="button"
                        class="btn btn-icon text-secondary"
                        :title="trans('global.close')"
                        @click="globalStore?.closeModal($options.name)"
                    >
                        <i class="fa fa-times"></i>
                    </button>
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
                            </div>

                            <div class="form-group">
                                <Editor
                                    id="description"
                                    name="description"
                                    class="form-control"
                                    licenseKey="gpl"
                                    :init="tinyMCE"
                                    v-model="form.description"
                                />
                            </div>

                            <div v-if="checkPermission('is_admin')" class="form-group">
                                <label for="map_id">Map ID</label>
                                <input
                                    id="map_id"
                                    name="map_id"
                                    type="number"
                                    min="1"
                                    class="form-control"
                                    v-model="form.map_id"
                                    placeholder="Map ID"
                                />
                            </div>

                            <Select2
                                id="map_marker_type"
                                name="map_marker_type"
                                url="/mapMarkerTypes"
                                model="mapMarkerType"
                                :selected="form.type_id"
                                @selectedValue="(id) => this.form.type_id = id[0]"
                            />

                            <Select2
                                id="map_marker_category"
                                name="map_marker_category"
                                url="/mapMarkerCategories"
                                model="mapMarkerCategory"
                                :selected="form.category_id"
                                @selectedValue="(id) => this.form.category_id = id[0]"
                            />

                            <div class="form-group">
                                <label for="latitude">{{ trans('global.marker.fields.latitude') }} *</label>
                                <input
                                    id="latitude"
                                    name="latitude"
                                    type="number"
                                    class="form-control"
                                    v-model.trim="form.latitude"
                                    :placeholder="trans('global.marker.fields.latitude')"
                                    required
                                />
                            </div>

                            <div class="form-group">
                                <label for="longitude">{{ trans('global.marker.fields.longitude') }} *</label>
                                <input
                                    id="longitude"
                                    name="longitude"
                                    type="number"
                                    class="form-control"
                                    v-model.trim="form.longitude"
                                    :placeholder="trans('global.marker.fields.longitude')"
                                    required
                                />
                            </div>

                            <div v-if="checkPermission('is_admin')" class="form-group">
                                <label for="tags">{{ trans('global.tag.title') }}</label>
                                <input
                                    id="tags"
                                    name="tags"
                                    type="text"
                                    class="form-control"
                                    v-model.trim="form.tags"
                                    :placeholder="trans('global.tag.title')"
                                    required
                                />
                            </div>

                            <div class="form-group">
                                <label for="author">{{ trans('global.marker.fields.author') }}</label>
                                <input
                                    id="author"
                                    name="author"
                                    type="text"
                                    class="form-control"
                                    v-model.trim="form.author"
                                    :placeholder="trans('global.marker.fields.author')"
                                    required
                                />
                            </div>

                            <div class="form-group">
                                <label for="address">{{ trans('global.address') }}</label>
                                <input
                                    id="address"
                                    name="address"
                                    type="text"
                                    class="form-control"
                                    v-model.trim="form.address"
                                    :placeholder="trans('global.address')"
                                    required
                                />
                            </div>
        
                            <div class="form-group">
                                <label for="url">{{ trans('global.url') }}</label>
                                <input
                                    id="url"
                                    name="url"
                                    type="text"
                                    class="form-control"
                                    v-model.trim="form.url"
                                    :placeholder="trans('global.url')"
                                />
                            </div>

                            <div>
                                <label for="url_title">{{ trans('global.url_title') }}</label>
                                <input
                                    id="url_title"
                                    name="url_title"
                                    type="text"
                                    class="form-control"
                                    v-model.trim="form.url_title"
                                    :placeholder="trans('global.url_title')"
                                />
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
            form: new Form({
                id: null,
                title: '',
                teaser_text: '',
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
                map_id: null,
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink", "link", "autoresize", "code",
                ],
                {
                    'callback': 'insertContent',
                    'callbackId': this.component_id
                },
                "bold underline italic | alignleft aligncenter alignright alignjustify | link code",
                "",
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
            axios.post('/mapMarkers', this.form)
                .then(r => {
                    this.$eventHub.emit('marker-added', r.data);
                    this.globalStore?.closeModal(this.$options.name)
                })
                .catch(e => {
                    console.log(e);
                });
        },
        update() {
            axios.patch('/mapMarkers/' + this.form.id, this.form)
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
                    if (this.form.id) {
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