<template>
    <div class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <b v-if="form.id == undefined"
                        class="modal-title"
                    >
                        {{ trans('global.marker.create') }}
                    </b>
                    <b v-else
                        class="modal-title"
                    >
                        {{ trans('global.marker.edit') }}
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
                            <div class="form-group">
                                <label for="title">
                                    {{ trans('global.marker.fields.title') }} *
                                </label>
                                <input
                                    id="title"
                                    name="title"
                                    type="text"
                                    class="form-control"
                                    v-model.trim="form.title"
                                    :placeholder="trans('global.marker.fields.title')"
                                    required
                                />
                                <p v-if="errors.title == true" class="error-block mt-0">
                                    {{ trans('validation.required') }}
                                </p>
                            </div>
                            <div class="form-group">
                                <label for="teaser_text">
                                    {{ trans('global.marker.fields.teaser_text') }}
                                </label>
                                <input
                                    id="teaser_text"
                                    name="teaser_text"
                                    type="text"
                                    class="form-control"
                                    v-model.trim="form.teaser_text"
                                    :placeholder="trans('global.marker.fields.teaser_text')"
                                />
                            </div>
                            <div class="form-group">
                                <label for="marker_description">
                                    {{ trans('global.marker.fields.description') }}
                                </label>
                                <textarea
                                    id="marker_description"
                                    name="marker_description"
                                    :placeholder="trans('global.marker.fields.description')"
                                    class="form-control description my-editor "
                                    v-model.trim="form.description"
                                ></textarea>
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
                                />
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
                                />
                            </div>

                            <div v-if="this.mapMarkerTypes?.length != null"
                                 class="form-group"
                            >
                                <label for="marker_type_id">
                                    {{ trans('global.marker.fields.type') }} *
                                </label>
                                <select
                                    id="marker_type_id"
                                    v-model="this.mapMarkerTypes[form.type_id]"
                                    class="form-control select2"
                                    style="width:100%;"
                                >
                                    <option v-for="value in this.mapMarkerTypes"
                                            :value="value.id"
                                    >
                                        {{ value.title }}
                                    </option>
                                </select>
                                <p v-if="errors.type_id == true" class="error-block mt-0">
                                    {{ trans('validation.required') }}
                                </p>
                            </div>

                            <div v-if="this.mapMarkerCategories?.length != null"
                                class="form-group"
                            >
                                <label for="marker_category_id">
                                    {{ trans('global.category.title_singular') }} *
                                </label>
                                <select
                                    id="marker_category_id"
                                    v-model="this.mapMarkerCategories[form.category_id]"
                                    class="form-control select2"
                                    style="width:100%;"
                                >
                                    <option v-for="value in this.mapMarkerCategories"
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
                                    required
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
                                    required
                                />
                                <p v-if="errors.longitude == true" class="error-block mt-0">
                                    {{ trans('validation.required') }}
                                </p>
                            </div>

                            <div class="form-group">
                                <label for="address">
                                    {{ trans('global.marker.fields.address') }}
                                </label>
                                <input
                                    type="text"
                                    id="address"
                                    name="address"
                                    class="form-control"
                                    v-model.trim="form.address"
                                    :placeholder="trans('global.marker.fields.address')"
                                />
                            </div>

                            <div class="form-group">
                                <label for="url">
                                    {{ trans('global.marker.fields.url') }}
                                </label>
                                <input
                                    type="text"
                                    id="url"
                                    name="url"
                                    class="form-control"
                                    v-model.trim="form.url"
                                    :placeholder="trans('global.marker.fields.url')"
                                />
                            </div>

                            <div class="form-group">
                                <label for="url_title">
                                    {{ trans('global.marker.fields.url_title') }}
                                </label>
                                <input
                                    type="text"
                                    id="url_title"
                                    name="url_title"
                                    class="form-control"
                                    v-model.trim="form.url_title"
                                    :placeholder="trans('global.marker.fields.url_title')"
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

export default {
    name: 'markerCreate',
    components: {},
    props: {
        map: {},
        marker: {},
        method: '',
    },
    data() {
        return {
            component_id: this._uid,
            requestUrl: '/mapMarkers',
            form: new Form({
                'title':'',
                'teaser_text':'',
                'description': '',
                'author': '',
                'type_id': this.map.type_id,
                'category_id':  this.map.category_id,
                'tags': '',
                'latitude': null,
                'longitude': null,
                'address': '',
                'url': '',
                'url_title': '',
            }),
            mapMarkerTypes : {},
            mapMarkerCategories : {},
            errors: {
                title: false,
                type_id: false,
                category_id: false,
                latitude: false,
                longitude: false,
            },
        };
    },
    watch: {
        marker: function(newVal, oldVal) {
            this.form.id = newVal.id;
            this.form.title = newVal.title;
            this.form.teaser_text = newVal.teaser_text;
            this.form.description = newVal.description;
            this.form.author = newVal.author;
            this.form.tags = newVal.tags;
            this.form.type_id = newVal.type_id;
            this.form.category_id = newVal.category_id;
            this.form.latitude = newVal.latitude;
            this.form.longitude = newVal.longitude;
            this.form.address = newVal.address;
            this.form.url = this.decodeHTMLEntities(newVal.url);
            this.form.url_title = newVal.url_title;
            tinyMCE.get('marker_description').setContent(this.form.description);
            this.syncSelect2();
        },
        method: function (newVal, oldVal) {
            if (newVal == 'post') {
                this.form.reset();
            }
        }

    },
    methods: {
        submit() {
            if (!this.checkRequired()) return;

            let method = this.method.toLowerCase();
            this.form.description = tinyMCE.get('marker_description').getContent();
            if (method === 'patch') {
                axios.patch(this.requestUrl + '/' + this.form.id, this.form)
                    .then(res => { // Tell the parent component we've updated a task
                        this.$eventHub.$emit("marker-updated", res.data.marker);
                        this.form.reset();
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error);
                    });
            } else {
                axios.post(this.requestUrl, this.form)
                    .then(res => {
                        this.$eventHub.$emit("marker-added", res.data.marker);
                        this.form.reset();
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error)
                    });
            }

            $('#modal-marker-form').modal('hide');
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
            $("#marker_type_id").select2({
                dropdownParent: $("#marker_type_id").parent(),
                allowClear: false
            }).on('select2:select', function (e) {
                this.form.type_id = e.params.data.element.value
            }.bind(this))
                .val(this.form.type_id)
                .trigger('change');

            $("#marker_category_id").select2({
                dropdownParent: $("#marker_category_id").parent(),
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
        $('#modal-marker-form').on('shown.bs.modal', function () {
            this.syncSelect2();
        }.bind(this));


        this.$initTinyMCE([
            "autolink link code"
        ] );

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
