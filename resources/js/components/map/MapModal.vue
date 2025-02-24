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
                    <div class="card">
                        <div
                            class="card-header"
                            data-card-widget="collapse"
                        >
                            <div class="card-title">{{ trans('global.general') }}</div>
                        </div>

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
                                <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                            </div>

                            <div class="form-group">
                                <input
                                    id="subtitle"
                                    name="subtitle"
                                    type="text"
                                    class="form-control"
                                    v-model.trim="form.subtitle"
                                    :placeholder="trans('global.map.fields.subtitle')"
                                    required
                                />
                                <p class="help-block" v-if="form.errors?.subtitle" v-text="form.errors?.subtitle[0]"></p>
                            </div>

                            <div class="form-group">
                                <Editor
                                    id="description"
                                    name="description"
                                    :placeholder="trans('global.map.fields.description')"
                                    class="form-control"
                                    :init="tinyMCE"
                                    v-model="form.description"
                                />
                            </div>

                            <Select2
                                v-permission="'is_admin'"
                                id="user_id"
                                :label="trans('global.change_owner')"
                                model="User"
                                :selected="form.owner_id"
                                url="/users"
                                style="width: 100%;"
                                :placeholder="trans('global.pleaseSelect')"
                                @selectedValue="(id) => this.form.owner_id = id[0]"
                            />

                            <div class="form-group">
                                <label for="tags">
                                    {{ trans('global.map.fields.tags') }}
                                </label>
                                <input
                                    id="tags"
                                    name="tags"
                                    type="text"
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
                                <label for="border_url">
                                    {{ trans('global.map.fields.border_url') }}
                                </label>
                                <input
                                    id="border_url"
                                    name="border_url"
                                    type="text"
                                    class="form-control"
                                    v-model.trim="form.border_url"
                                    :placeholder="trans('global.map.fields.border_url_helper')"
                                    required
                                />
                                <p class="help-block" v-if="form.errors?.border_url" v-text="form.errors?.border_url[0]"></p>
                            </div>

                            <div class="form-group">
                                <label for="latitude">
                                    {{ trans('global.map.fields.latitude') }}
                                </label>
                                <input
                                    id="latitude"
                                    name="latitude"
                                    type="text"
                                    class="form-control"
                                    v-model.trim="form.latitude"
                                    :placeholder="trans('global.map.fields.latitude')"
                                />
                                <p class="help-block" v-if="form.errors?.latitude" v-text="form.errors?.latitude[0]"></p>
                            </div>

                            <div class="form-group">
                                <label for="longitude">
                                    {{ trans('global.map.fields.longitude') }}
                                </label>
                                <input
                                    id="longitude"
                                    name="longitude"
                                    type="text"
                                    class="form-control"
                                    v-model.trim="form.longitude"
                                    :placeholder="trans('global.map.fields.longitude')"
                                />
                                <p class="help-block" v-if="form.errors?.longitude" v-text="form.errors?.longitude[0]"></p>
                            </div>

                            <div>
                                <label for="zone">
                                    {{ trans('global.map.fields.zoom') }}
                                </label>
                                <input
                                    id="zoom"
                                    name="zoom"
                                    type="number"
                                    class="form-control"
                                    v-model="form.zoom"
                                    :placeholder="trans('global.map.fields.zoom')"
                                    required
                                />
                                <p class="help-block" v-if="form.errors.zoom" v-text="form.errors.zoom[0]"></p>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div
                            class="card-header"
                            data-card-widget="collapse"
                        >
                            <div class="card-title">{{ trans('global.display') }}</div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <v-swatches
                                    :swatch-size="49"
                                    :trigger-style="{}"
                                    style="height: 42px;"
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
        
                                <MediumForm v-if="form.id"
                                    :id="'medium_form' + component_id"
                                    :medium_id="form.medium_id"
                                    :subscribable_id="form.id"
                                    subscribable_type="App\Map"
                                    accept="image/*"
                                    @selectedValue="(id) => {
                                        // on removal of medium, directly update the resource
                                        if (this.form.medium_id !== null && id === null) {
                                            this.$eventHub.emit('map-updated', {
                                                id: this.form.id,
                                                medium_id: null,
                                            });
                                        }
                                        this.form.medium_id = id;
                                    }"
                                />
                            </div>
                        </div>
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
                            class="btn btn-primary ml-3"
                            :disabled="!form.title"
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
import MediumForm from "../media/MediumForm.vue";
import axios from "axios";
import Editor from "@tinymce/tinymce-vue";
import Select2 from "../forms/Select2.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'map-modal',
    components:{
        Editor,
        MediumForm,
        Select2,
    },
    props: {},
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            form: new Form({
                id: '',
                title: '',
                subtitle: '',
                description: '',
                owner_id: null,
                tags: '',
                type_id: 2,
                category_id: 2,
                border_url: '',
                latitude: 49,
                longitude: 8,
                zoom: 10,
                color: '#F2C511',
                medium_id: '',
            }),
            search: '',
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink link autoresize"
                ],
                {
                    'callback': 'insertContent',
                    'callbackId': this.component_id
                },
                null,
                ''
            ),
        }
    },
    computed: {
        textColor: function() {
            return this.$textcolor(this.form.color, '#333333');
        }
    },
    methods: {
        submit() {
            if (this.method == 'patch') {
                this.update();
            } else {
                this.add();
            }

            this.globalStore?.closeModal(this.$options.name);
        },
        add() {
            axios.post('/maps', this.form)
                .then(r => {
                    this.$eventHub.emit('map-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch('/maps/' + this.form.id, this.form)
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
            if (state.modals[this.$options.name].show && !state.modals[this.$options.name].lock) {
                this.globalStore.lockModal(this.$options.name);
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