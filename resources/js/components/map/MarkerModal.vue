<template>
    <Transition name="modal">
        <div v-if="show"
             class="modal-mask"
        >
        <div class="modal-container">
            <div class="card-header">
                <h3 class="card-title">
                    <span v-if="method === 'post'">
                        {{ trans('global.mapMarker.create') }}
                    </span>
                    <span v-if="method === 'patch'">
                        {{ trans('global.mapMarker.edit') }}
                    </span>
                </h3>
                <div class="card-tools">
                    <button type="button"
                            class="btn btn-tool"
                            @click="$emit('close')">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                <div class="form-group ">
                    <label for="title">
                        {{ trans('global.mapMarker.fields.title') }}
                    </label>
                    <input
                        type="text" id="title"
                        name="title"
                        class="form-control"
                        v-model="form.title"
                        placeholder="Title"
                        required
                    />
                    <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                </div>
                <div class="form-group">
                    <label for="teaser_text">
                        {{ trans('global.mapMarker.fields.teaser_text') }}
                    </label>
                    <input
                        type="text"
                        id="teaser_text"
                        name="teaser_text"
                        class="form-control"
                        v-model.trim="form.teaser_text"
                        :placeholder="trans('global.mapMarker.fields.teaser_text')"
                        required
                    />
                    <p class="help-block" v-if="form.errors?.teaser_text" v-text="form.errors?.teaser_text[0]"></p>
                </div>
                <div class="form-group">
                    <label for="description">
                        {{ trans('global.mapMarker.fields.description') }}
                    </label>
                    <Editor
                        id="description"
                        name="description"
                        :placeholder="trans('global.mapMarker.fields.description')"
                        class="form-control"
                        :init="tinyMCE"
                        :initial-value="form.description"
                    ></Editor>
                </div>
                <div class="form-group">
                    <label for="author">
                        {{ trans('global.mapMarker.fields.author') }}
                    </label>
                    <input
                        type="text"
                        id="author"
                        name="author"
                        class="form-control"
                        v-model.trim="form.author"
                        :placeholder="trans('global.mapMarker.fields.author')"
                        required
                    />
                    <p class="help-block" v-if="form.errors?.author" v-text="form.errors?.author[0]"></p>
                </div>
                <div class="form-group">
                    <label for="tags">
                        {{ trans('global.mapMarker.fields.tags') }}
                    </label>
                    <input
                        type="text"
                        id="tags"
                        name="tags"
                        class="form-control"
                        v-model.trim="form.tags"
                        :placeholder="trans('global.mapMarker.fields.tags')"
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
                >
                </Select2>
                <Select2
                    id="map_marker_category"
                    name="map_marker_category"
                    url="/mapMarkerCategories"
                    model="mapMarkerCategory"
                    :selected="this.form.category_id"
                    @selectedValue="(id) => {
                    this.form.category_id = id;
                }"
                >
                </Select2>

                <div class="form-group">
                    <label for="latitude">
                        {{ trans('global.mapMarker.fields.latitude') }}
                    </label>
                    <input
                        type="text"
                        id="latitude"
                        name="latitude"
                        class="form-control"
                        v-model.trim="form.latitude"
                        :placeholder="trans('global.mapMarker.fields.latitude')"
                        required
                    />
                    <p class="help-block" v-if="form.errors?.latitude" v-text="form.errors?.latitude[0]"></p>
                </div>
                <div class="form-group">
                    <label for="longitude">
                        {{ trans('global.mapMarker.fields.longitude') }}
                    </label>
                    <input
                        type="text"
                        id="longitude"
                        name="longitude"
                        class="form-control"
                        v-model.trim="form.longitude"
                        :placeholder="trans('global.mapMarker.fields.longitude')"
                        required
                    />
                    <p class="help-block" v-if="form.errors?.longitude" v-text="form.errors?.longitude[0]"></p>
                </div>

                <div class="form-group">
                    <label for="address">
                        {{ trans('global.mapMarker.fields.address') }}
                    </label>
                    <input
                        type="text"
                        id="address"
                        name="address"
                        class="form-control"
                        v-model.trim="form.address"
                        :placeholder="trans('global.mapMarker.fields.address')"
                        required
                    />
                    <p class="help-block" v-if="form.errors?.address" v-text="form.errors?.address[0]"></p>
                </div>

                <div class="form-group">
                    <label for="url">
                        {{ trans('global.mapMarker.fields.url') }}
                    </label>
                    <input
                        type="text"
                        id="url"
                        name="url"
                        class="form-control"
                        v-model.trim="form.url"
                        :placeholder="trans('global.mapMarker.fields.url')"
                    />
                    <p class="help-block" v-if="form.errors?.url" v-text="form.errors?.url[0]"></p>
                </div>

                <div class="form-group">
                    <label for="url_title">
                        {{ trans('global.mapMarker.fields.url_title') }}
                    </label>
                    <input
                        type="text"
                        id="url_title"
                        name="url_title"
                        class="form-control"
                        v-model.trim="form.url_title"
                        :placeholder="trans('global.mapMarker.fields.url_title')"
                    />
                    <p class="help-block" v-if="form.errors?.url_title" v-text="form.errors?.url_title[0]"></p>
                </div>
            </div>

            <div class="card-footer">
                 <span class="pull-right">
                     <button
                         id="marker-cancel"
                         type="button"
                         class="btn btn-default"
                         @click="$emit('close')">
                         {{ trans('global.cancel') }}
                     </button>
                     <button
                         id="marker-save"
                         class="btn btn-primary"
                         @click="submit(method)" >
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
    import MediumModal from "../media/MediumModal";
    import axios from "axios";
    import Editor from "@tinymce/tinymce-vue";
    import Select2 from "../forms/Select2";

    export default {
        components:{
            Editor,
            MediumModal,
            Select2
        },
        props: {
            show: {
                type: Boolean
            },
            params: {
                type: Object
            },  //{ 'modelId': curriculum.id, 'modelUrl': 'curriculum' , 'shareWithToken': true, 'canEditCheckbox': false}
            map: {
                type: Object
            }
        },
        data() {
            return {
                component_id: this._uid,
                method: 'post',
                url: '/mapMarkers',
                form: new Form({
                    'id':'',
                    'title':'',
                    'teaser_tesxt':'',
                    'description': '',
                    'author': '',
                    'type_id': 1,
                    'category_id': 1,
                    'tags': '',
                    'latitude': null,
                    'longitude': null,
                    'address': '',
                    'url': '',
                    'url_title': '',
                    'map_id': '',
                }),
                tinyMCE: this.$initTinyMCE(
                    [
                        "autolink link curriculummedia"
                    ],
                    {
                        'eventHubCallbackFunction': 'insertContent',
                        'eventHubCallbackFunctionParams': this.component_id,
                    }
                ),
            }
        },
        watch: {
            params: function(newVal, oldVal) {
                this.form.reset();
                this.form.populate(newVal);

                this.form.map_id = this.map.id;
                this.form.url = this.decodeHTMLEntities(newVal.url);

                if (this.form.id != ''){
                    this.method = 'patch';
                } else {
                    this.method = 'post';
                }
            },
        },
        methods: {
             submit(method) {
                 if (method == 'patch') {
                     this.update();
                 } else {
                     this.add();
                 }
            },
            add(){
                axios.post(this.url, this.form)
                    .then(r => {
                        this.$eventHub.emit('marker-added', r.data.marker);
                        this.$emit('close');
                    })
                    .catch(e => {
                        console.log(e.response);
                    });
            },
            update() {
                console.log('update');
                axios.patch(this.url + '/' + this.form.id, this.form)
                    .then(r => {
                        this.$eventHub.emit('marker-updated', r.data.marker);
                        this.$emit('close');
                    })
                    .catch(e => {
                        console.log(e.response);
                    });
            },
            decodeHTMLEntities(text) {
                return $("<textarea/>")
                    .html(text)
                    .text();
            }

        },
        mounted() {
        },
    }
</script>

