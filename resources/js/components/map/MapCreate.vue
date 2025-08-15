<template>
    <div class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <b v-if="form.id == ''"
                        class="modal-title"
                    >
                        {{ trans('global.map.create') }}
                    </b>
                    <b v-else
                        class="modal-title"
                    >
                        {{ trans('global.map.edit') }}
                    </b>
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-0">
                    <div class="card mb-0">
                        <div class="card-body pb-0">
                            <div class="input-group pb-1">
                                <color-picker-input
                                    class="input-group-prepend"
                                    v-model="form.color"
                                ></color-picker-input>
                                <input
                                    id="title"
                                    name="title"
                                    type="text"
                                    class="form-control ml-3"
                                    style="height:42px"
                                    v-model.trim="form.title"
                                    :placeholder="trans('global.map.fields.title') + ' *'"
                                    required
                                />
                                <p v-if="errors.title == true" class="error-block mt-0">
                                    {{ trans('validation.required') }}
                                </p>
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
                                />
                            </div>

                            <div class="form-group">
                                <label for="map_description">
                                    {{ trans('global.map.fields.description') }}
                                </label>
                                <textarea
                                    id="map_description"
                                    name="map_description"
                                    :placeholder="trans('global.map.fields.description')"
                                    class="form-control description"
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
                                />
                            </div>

                            <div v-if="mapMarkerTypes?.length !== null"
                                 class="form-group"
                            >
                                <label for="map_marker_type_id">
                                    {{ trans('global.marker.fields.type') }} *
                                </label>
                                <select
                                    id="map_marker_type_id"
                                    v-model="form.type_id"
                                    class="form-control select2"
                                    style="width:100%;"
                                >
                                    <option v-for="value in mapMarkerTypes"
                                            :value="value.id"
                                    >
                                        {{ value.title }}
                                    </option>
                                </select>
                                <p v-if="errors.type_id == true" class="error-block mt-0">
                                    {{ trans('validation.required') }}
                                </p>
                            </div>

                            <div v-if="mapMarkerCategories?.length !== null"
                                class="form-group"
                            >
                                <label for="map_marker_category_id">
                                    {{ trans('global.category.title_singular') }} *
                                </label>
                                <select
                                    id="map_marker_category_id"
                                    v-model="form.category_id"
                                    class="form-control select2"
                                    style="width:100%;"
                                >
                                    <option v-for="value in mapMarkerCategories"
                                            :value="value.id"
                                    >
                                        {{ value.title }}
                                    </option>
                                </select>
                                <p v-if="errors.category_id == true" class="error-block mt-0">
                                    {{ trans('validation.required') }}
                                </p>
                            </div>

                            <div class="form-group">
                                <label for="border_url">
                                    {{ trans('global.map.fields.border_url') }}
                                </label>
                                <input
                                    type="text"
                                    id="border_url"
                                    name="border_url"
                                    class="form-control"
                                    v-model.trim="form.border_url"
                                    :placeholder="trans('global.map.fields.border_url')"
                                />
                            </div>

                            <div class="form-group">
                                <label for="latitude">
                                    {{ trans('global.marker.fields.latitude') }} *
                                </label>
                                <input
                                    id="latitude"
                                    name="latitude"
                                    type="number"
                                    step="any"
                                    class="form-control"
                                    v-model.trim="form.latitude"
                                    :placeholder="trans('global.marker.fields.latitude')"
                                />
                                <p v-if="errors.latitude == true" class="error-block mt-0">
                                    {{ trans('validation.required') }}
                                </p>
                            </div>

                            <div class="form-group">
                                <label for="longitude">
                                    {{ trans('global.marker.fields.longitude') }} *
                                </label>
                                <input
                                    id="longitude"
                                    name="longitude"
                                    type="number"
                                    step="any"
                                    class="form-control"
                                    v-model.trim="form.longitude"
                                    :placeholder="trans('global.marker.fields.longitude')"
                                />
                                <p v-if="errors.longitude == true" class="error-block mt-0">
                                    {{ trans('validation.required') }}
                                </p>
                            </div>

                            <div class="form-group">
                                <label for="zoom">
                                    {{ trans('global.map.fields.zoom') }} *
                                </label>
                                <input
                                    id="zoom"
                                    name="zoom"
                                    type="number"
                                    class="form-control"
                                    v-model.trim="form.zoom"
                                    :placeholder="trans('global.map.fields.zoom')"
                                    required
                                />
                                <p v-if="errors.zoom == true" class="error-block mt-0">
                                    {{ trans('validation.required') }}
                                </p>
                            </div>

                            <div v-if="form.id"
                                 class="form-group"
                            >
                                <MediumForm
                                    :form="form"
                                    :id="component_id"
                                    :medium_id="form.medium_id"
                                    accept="image/*"
                                />
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button"
                        class="btn btn-default"
                        data-dismiss="modal"
                    >
                        {{ trans('global.cancel') }}
                    </button>
                    <button type="button"
                        class="btn btn-primary"
                        @click="submit()"
                    >
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
                'latitude': 49,
                'longitude': 8,
                'zoom': 10,
                'color': '#F2C511',
                'medium_id': '',
            }),
            mapMarkerTypes: {},
            mapMarkerCategories: {},
            errors: {
                title: false,
                type_id: false,
                category_id: false,
                longitude: false,
                latitude: false,
                zoom: false,
            },
        };
    },
    watch: {
        map: function(newVal, oldVal) {
            this.form.id = newVal.id;
            this.form.title = newVal.title;
            this.form.subtitle = newVal.subtitle;
            this.form.description = this.htmlToText(newVal.description);
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
    computed: {
        textColor: function() {
            return this.$textcolor(this.form.color, '#333333');
        }
    },
    methods: {
        submit() {
            if (!this.checkRequired()) return;

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

            $('#modal-map-form').modal('hide');
        },
        checkRequired() {
            let filledOut = true;
            const fields = this.$el.querySelectorAll('[required]');

            for (const field of fields) {
                if (field.value.trim() === '') { // activate error-helper
                    this.errors[field.id] = true;
                    filledOut = false;
                } else { // deactivate error-helper
                    this.errors[field.id] = false;
                }
            }
            // select2 fields need to be checked separately
            // in this case they theoretically don't need to be checked, since they can't be empty
            // they also don't have a 'required' tag, because it could set 'filledOut' to 'false'
            let markerTypeIsSet = this.form.type_id !== '';
            this.errors['type_id'] = !markerTypeIsSet;
            filledOut = markerTypeIsSet ? filledOut : false;
            
            let categoryIdIsSet = this.form.category_id !== '';
            this.errors['category_id'] = !categoryIdIsSet;
            filledOut = categoryIdIsSet ? filledOut : false;
            
            return filledOut;
        },
        syncSelect2() {
            $("#map_marker_type_id").select2({
                dropdownParent: $("#map_marker_type_id").parent(),
                allowClear: false
            }).on('select2:select', function (e) {
                this.form.type_id = e.params.data.element.value
            }.bind(this))
                .val(this.form.type_id)
                .trigger('change');

            $("#map_marker_category_id").select2({
                dropdownParent: $("#map_marker_category_id").parent(),
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
        axios.get('/mapMarkerTypes')
            .then(res => {
                this.mapMarkerTypes = res.data.mapMarkerTypes;

                axios.get('/mapMarkerCategories')
                    .then(res => {
                        this.mapMarkerCategories = res.data.mapMarkerCategories;
                        this.syncSelect2();
                    })
                    .catch(err => {
                        console.log(err);
                    });

            })
            .catch(err => {
                console.log(err);
            });
    }
}
</script>
