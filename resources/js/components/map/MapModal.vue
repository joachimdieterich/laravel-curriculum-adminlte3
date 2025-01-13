<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
             class="modal-mask"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        <span v-if="method === 'post'">
                            {{ trans('global.map.create') }}
                        </span>
                        <span v-if="method === 'patch'">
                            {{ trans('global.map.edit') }}
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
                    <div class="input-group pb-1">
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
                        <input
                            type="text" id="title"
                            name="title"
                            class="form-control ml-3"
                            v-model="form.title"
                            placeholder="Title"
                            required
                        />
                        <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                    </div>
                    <div class="form-group">
                        <label for="subtitle">
                            {{ trans('global.map.fields.subtitle') }}
                        </label>
                        <input
                            type="text"
                            id="subtitle"
                            name="subtitle"
                            class="form-control"
                            v-model.trim="form.subtitle"
                            :placeholder="trans('global.map.fields.subtitle')"
                            required
                        />
                        <p class="help-block" v-if="form.errors?.subtitle" v-text="form.errors?.subtitle[0]"></p>
                    </div>
                    <div class="form-group">
                        <label for="description">
                            {{ trans('global.map.fields.description') }}
                        </label>
                        <Editor
                            id="description"
                            name="description"
                            :placeholder="trans('global.map.fields.description')"
                            class="form-control"
                            :init="tinyMCE"
                            :initial-value="form.description"
                        />
                    </div>
                    <div class="form-group">
                        <label for="tags">
                            {{ trans('global.map.fields.tags') }}
                        </label>
                        <input
                            type="text"
                            id="tags"
                            name="tags"
                            class="form-control"
                            v-model.trim="form.tags"
                            :placeholder="trans('global.map.fields.tags')"
                            required
                        />
                        <p class="help-block" v-if="form.errors?.tags" v-text="form.errors?.tags[0]"></p>
                    </div>

                    <Select2
                        id="map_marker_type"
                        name="map_marker_type"
                        url="/mapMarkerTypes"
                        model="mapMarkerType"
                        :selected="this.form.type_id"
                        @selectedValue="(id) => {
                            this.form.type_id = id;
                        }"
                    />
                    <Select2
                        id="map_marker_category"
                        name="map_marker_category"
                        url="/mapMarkerCategories"
                        model="mapMarkerCategory"
                        :selected="this.form.category_id"
                        @selectedValue="(id) => {
                            this.form.category_id = id;
                        }"
                    />

                    <div class="form-group">
                        <label for="map_marker_category">
                            {{ trans('global.map.fields.border_url') }}
                        </label>
                        <input
                            type="text"
                            id="border_url"
                            name="border_url"
                            class="form-control"
                            v-model.trim="form.border_url"
                            :placeholder="trans('global.map.fields.border_url')"
                            required
                        />
                        <p class="help-block" v-if="form.errors?.border_url" v-text="form.errors?.border_url[0]"></p>
                    </div>

                    <div class="form-group">
                        <label for="latitude">
                            {{ trans('global.marker.fields.latitude') }}
                        </label>
                        <input
                            type="text"
                            id="latitude"
                            name="latitude"
                            class="form-control"
                            v-model.trim="form.latitude"
                            :placeholder="trans('global.marker.fields.latitude')"
                        />
                        <p class="help-block" v-if="form.errors?.latitude" v-text="form.errors?.latitude[0]"></p>
                    </div>

                    <div class="form-group">
                        <label for="longitude">
                            {{ trans('global.marker.fields.longitude') }}
                        </label>
                        <input
                            type="text"
                            id="longitude"
                            name="longitude"
                            class="form-control"
                            v-model.trim="form.longitude"
                            :placeholder="trans('global.marker.fields.longitude')"
                        />
                        <p class="help-block" v-if="form.errors?.longitude" v-text="form.errors?.longitude[0]"></p>
                    </div>

                    <div class="form-group">
                        <label for="longitude">
                            {{ trans('global.map.fields.zoom') }}
                        </label>
                        <input
                            type="number"
                            id="zoom"
                            name="zoom"
                            class="form-control"
                            v-model.trim="form.zoom"
                            :placeholder="trans('global.map.fields.zoom')"
                            required
                        />
                        <p class="help-block" v-if="form.errors.zoom" v-text="form.errors.zoom[0]"></p>
                    </div>

                    <div v-if="form.id"
                        class="form-group"
                    >
                        <MediumModal
                            :form="form"
                            :id="component_id"
                            :medium_id="form.medium_id"
                            accept="image/*"
                        />
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="map-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="map-save"
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
import MediumModal from "../media/MediumModal.vue";
import axios from "axios";
import Editor from "@tinymce/tinymce-vue";
import Select2 from "../forms/Select2.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'map-modal',
    components:{
        Editor,
        MediumModal,
        Select2
    },
    props: {},
    setup() { //https://pinia.vuejs.org/core-concepts/getters.html#passing-arguments-to-getters
        const globalStore = useGlobalStore();
        return {
            globalStore
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            url: '/maps',
            form: new Form({
                'id':'',
                'title':'',
                'subtitle':'',
                'description': '',
                'tags': '',
                'type_id': 2,
                'category_id': 2,
                'border_url': '',
                'latitude': 49,
                'longitude': 8,
                'zoom': 10,
                'color': '#F2C511',
                'medium_id': '',
            }),
            search: '',
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
    computed: {
        textColor: function() {
            return this.$textcolor(this.form.color, '#333333');
        }
    },
    methods: {
        submit(method) {
            this.form.description = tinyMCE.get('description').getContent();
            if (method == 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            axios.post(this.url, this.form)
                .then(r => {
                    this.$eventHub.emit('map-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch(this.url + '/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('map-updated', r.data);
                })
                .catch(e => {
                    console.log(e.response);
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
                    this.form.border_url = this.$decodeHTMLEntities(params.border_url);

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

