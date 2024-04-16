<template>
    <div class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <b  v-if="form.id == ''"
                        class="modal-title">
                        {{ trans('global.map.create') }}
                    </b>
                    <b  v-else
                        class="modal-title">
                        {{ trans('global.map.edit') }}
                    </b>
                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>

                <div class="modal-body p-0">
                    <div class="card mb-0">
                        <div class="card-body pb-0">
                            <div class="input-group pb-1">
                            <color-picker-input
                              class="input-group-prepend"
                              v-model="form.color">
                            </color-picker-input>
                            <input
                            type="text"
                            id="title"
                            name="title"
                            class="form-control ml-3"
                            style="height:42px"
                            v-model.trim="form.title"
                            :placeholder="trans('global.map.fields.title')"
                            required
                            />
                            <p class="help-block" v-if="form.errors?.title" v-text="form.errors?.title[0]"></p>
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
                                <textarea
                                    id="description"
                                    name="description"
                                    :placeholder="trans('global.map.fields.description')"
                                    class="form-control description my-editor "
                                    v-model.trim="form.description"
                                ></textarea>
                            </div>
                            <div class="form-group">
                                <label for="subtitle">
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

                            <div v-if="this.mapMarkerTypes.length !== null"
                                 class="form-group">
                                <label for="map_marker_type">
                                    {{ trans('global.marker.fields.type') }}
                                </label>
                                <select
                                    id="type_id"
                                    v-model="this.mapMarkerTypes[form.type_id]"
                                    class="form-control select2"
                                    style="width:100%;">
                                    <option v-for="(value,index) in this.mapMarkerTypes"
                                            :value="value.id">
                                        {{ value.title }}
                                    </option>
                                </select>
                            </div>

                            <div v-if="this.mapMarkerCategories.length !== null"
                                class="form-group">
                                <label for="map_marker_category">
                                    {{ trans('global.category.title_singular') }}
                                </label>
                                <select
                                    id="category_id"
                                    v-model="this.mapMarkerCategories[form.category_id]"
                                    class="form-control select2"
                                    style="width:100%;">
                                    <option v-for="(value,index) in this.mapMarkerCategories"
                                            :value="value.id">
                                        {{ value.title }}
                                    </option>
                                </select>
                            </div>

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
                                 class="form-group">
                                <MediumForm
                                    :form="form"
                                    :id="component_id"
                                    :medium_id="form.medium_id"
                                    accept="image/*"/>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button"
                        class="btn btn-default"
                        data-dismiss="modal">
                        {{ trans('global.cancel') }}
                    </button>
                    <button type="button"
                        class="btn btn-primary"
                        data-dismiss="modal"
                        @click="submit()">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Form from "form-backend-validation";
import axios from "axios";
import MediumForm from "../media/MediumForm";

export default {
    name: 'mapCreate',
    components: {
        MediumForm
    },
    props: {
        map: {},
        method: ''
    },
    data() {
        return {
            component_id: this._uid,
            requestUrl: '/maps',
            form: new Form({
                'title':'',
                'subtitle':'',
                'description': '',
                'tags': '',
                'type_id': 2,
                'category_id': 2,
                'border_url': '',
                'latitude': null,
                'longitude': null,
                'zoom': 10,
                'color': '#F2C511',
                'medium_id': '',
            }),
            mapMarkerTypes : {},
            mapMarkerCategories : {}
        };
    },
    watch: {
        map: function(newVal, oldVal) {
            this.form.id = newVal.id;
            this.form.title = newVal.title;
            this.form.subtitle = newVal.subtitle;
            this.form.description = newVal.description;
            this.form.tags = newVal.tags;
            this.form.type_id = newVal.type_id;
            this.form.category_id = newVal.category_id;
            this.form.border_url = this.decodeHTMLEntities(newVal.border_url);
            this.form.latitude = newVal.latitude;
            this.form.longitude = newVal.longitude;
            this.form.zoom = newVal.zoom;
            this.form.color = newVal.color;
            this.form.medium_id = newVal.medium_id;
            this.syncSelect2();
        },
        method: function (newVal, oldVal) {
            if (newVal == 'post') {
                this.form.reset();
            }
        }

    },
    computed:{
        textColor: function(){
            return this.$textcolor(this.form.color, '#333333');
        }
    },
    methods: {
        submit() {
            let method = this.method.toLowerCase();
            if (method === 'patch') {
                axios.patch(this.requestUrl + '/' + this.form.id, this.form)
                    .then(res => { // Tell the parent component we've updated a task
                        this.$eventHub.$emit("map-updated", res.data.map);
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error);
                    });
            } else {
                axios.post(this.requestUrl, this.form)
                    .then(res => {
                        this.$eventHub.$emit("map-added", res.data.map);
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error)
                    });
            }
        },
        syncSelect2(){
            $("#type_id").select2({
                dropdownParent: $("#type_id").parent(),
                allowClear: false
            }).on('select2:select', function (e) {
                this.form.type_id = e.params.data.element.value
            }.bind(this))
                .val(this.form.type_id)
                .trigger('change');

            $("#category_id").select2({
                dropdownParent: $("#category_id").parent(),
                allowClear: false
            }).on('select2:select', function (e) {
                this.form.category_id = e.params.data.element.value
            }.bind(this))
                .val(this.form.category_id)
                .trigger('change');
        },

        decodeHTMLEntities(text) {
            return $("<textarea/>")
                .html(text)
                .text();
        }
    },

    mounted() {
        this.$initTinyMCE([
            "autolink link"
        ] );

        axios.get('/mapMarkerTypes')
            .then(res => {
                this.mapMarkerTypes = res.data.mapMarkerTypes;
                Vue.nextTick(function () {
                    this.syncSelect2();
                })

            })
            .catch(err => {
                console.log(err);
            });

        axios.get('/mapMarkerCategories')
            .then(res => {
                this.mapMarkerCategories = res.data.mapMarkerCategories;
                Vue.nextTick(function () {
                    this.syncSelect2();
                })

            })
            .catch(err => {
                console.log(err);
            });
    }

}
</script>
