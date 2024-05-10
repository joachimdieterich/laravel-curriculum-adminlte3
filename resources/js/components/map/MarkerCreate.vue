<template>
    <div class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <b  v-if="form.id == ''"
                        class="modal-title">
                        {{ trans('global.marker.create') }}
                    </b>
                    <b  v-else
                        class="modal-title">
                        {{ trans('global.marker.edit') }}
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
                            <div class="form-group">
                                <label for="title">
                                    {{ trans('global.marker.fields.subtitle') }}
                                </label>
                                <input
                                    type="text"
                                    id="title"
                                    name="title"
                                    class="form-control"
                                    v-model.trim="form.title"
                                    :placeholder="trans('global.marker.fields.title')"
                                    required
                                />
                                <p class="help-block" v-if="form.errors?.title" v-text="form.errors?.title[0]"></p>
                            </div>
                            <div class="form-group">
                                <label for="teaser_text">
                                    {{ trans('global.marker.fields.teaser_text') }}
                                </label>
                                <input
                                    type="text"
                                    id="teaser_text"
                                    name="teaser_text"
                                    class="form-control"
                                    v-model.trim="form.teaser_text"
                                    :placeholder="trans('global.marker.fields.teaser_text')"
                                    required
                                />
                                <p class="help-block" v-if="form.errors?.teaser_text" v-text="form.errors?.teaser_text[0]"></p>
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
                                    type="text"
                                    id="author"
                                    name="author"
                                    class="form-control"
                                    v-model.trim="form.author"
                                    :placeholder="trans('global.marker.fields.author')"
                                    required
                                />
                                <p class="help-block" v-if="form.errors?.author" v-text="form.errors?.author[0]"></p>
                            </div>
                            <div class="form-group">
                                <label for="tags">
                                    {{ trans('global.marker.fields.tags') }}
                                </label>
                                <input
                                    type="text"
                                    id="tags"
                                    name="tags"
                                    class="form-control"
                                    v-model.trim="form.tags"
                                    :placeholder="trans('global.marker.fields.tags')"
                                    required
                                />
                                <p class="help-block" v-if="form.errors?.tags" v-text="form.errors?.tags[0]"></p>
                            </div>

                            <div v-if="this.mapMarkerTypes?.length != null"
                                 class="form-group">
                                <label for="marker_type_id">
                                    {{ trans('global.marker.fields.type') }}
                                </label>
                                <select
                                    id="marker_type_id"
                                    v-model="this.mapMarkerTypes[form.type_id]"
                                    class="form-control select2"
                                    style="width:100%;">
                                    <option v-for="(value,index) in this.mapMarkerTypes"
                                            :value="value.id">
                                        {{ value.title }}
                                    </option>
                                </select>
                            </div>

                            <div v-if="this.mapMarkerCategories?.length != null"
                                class="form-group">
                                <label for="marker_category_id">
                                    {{ trans('global.category.title_singular') }}
                                </label>
                                <select
                                    id="marker_category_id"
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
                                    required
                                />
                                <p class="help-block" v-if="form.errors?.address" v-text="form.errors?.address[0]"></p>
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
                                    required
                                />
                                <p class="help-block" v-if="form.errors.url" v-text="form.errors.url[0]"></p>
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
                                    required
                                />
                                <p class="help-block" v-if="form.errors.url_title" v-text="form.errors.url_title[0]"></p>
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

export default {
    name: 'markerCreate',
    components: {},
    props: {
        map: {},
        marker: {},
        method: ''
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
            mapMarkerCategories : {}
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
        },
        syncSelect2(){
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
            "autolink link"
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
