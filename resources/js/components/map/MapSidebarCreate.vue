<template>
    <div>
        <h1 class="sidebar-header  mb-3">
            Neu
            <span class="sidebar-close"><i class="fa fa-caret-left"></i></span>
        </h1>
        <div class="form-group">
            <label for="createMarkerTitle">
                {{ trans('global.marker.fields.title') }}
            </label>
            <input
                type="text"
                id="createMarkerTitle"
                name="title"
                class="form-control"
                v-model.trim="form.title"
                :placeholder="trans('global.marker.fields.title')"
            />
            <p class="help-block" v-if="form.errors?.title" v-text="form.errors?.title[0]"></p>
        </div>

        <div class="form-group ">
            <label for="description">
                {{ trans('global.marker.fields.description') }}
            </label>
            <textarea
                id="createMarkerDescription"
                name="description"
                :placeholder="trans('global.marker.fields.description')"
                class="form-control description my-editor "
                v-model.trim="form.description"
            ></textarea>
        </div>

        <div class="form-group ">
            <label for="map_marker_type">
                {{ trans('global.marker.fields.type') }}
            </label>
            <select
                id="map_marker_type"
                v-model="this.mapMarkerTypes[form.type_id]"
                class="form-control select2"
                style="width:100%;">
                <option v-for="(value,index) in this.mapMarkerTypes"
                        :value="value.id">
                    {{ value.title }}
                </option>
            </select>
        </div>

        <div class="form-group ">
            <label for="map_marker_category">
                {{ trans('global.category.title_singular') }}
            </label>
            <select
                id="map_marker_category"
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
            />
            <p class="help-block" v-if="form.errors?.tags" v-text="form.errors?.tags[0]"></p>
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

        <div class="form-group ">
            <label for="address">
                {{ trans('global.marker.fields.address') }}
            </label>
            <textarea
                id="address"
                name="address"
                :placeholder="trans('global.marker.fields.address')"
                class="form-control description my-editor "
                v-model.trim="form.address"
            ></textarea>
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
            <p class="help-block" v-if="form.errors?.url" v-text="form.errors?.url[0]"></p>
        </div>

    </div>

</template>
<script>
import Form from "form-backend-validation";

export default {
    name: 'MapSidebarCreate',
    props: {
        mapMarkerTypes : {
            default: null
        },
        mapMarkerCategories : {
            default: null
        }
    },
    data() {
        return {
            component_id: this._uid,
            form: new Form({
                'id':'',
                'title':'',
                'description': '',
                'type_id': '',
                'category_id': '',
                'tags': null,
                'latitude': null,
                'longitude': null,
                'address': '',
                'url': '',
            }),
        }
    },
    mounted() {
        this.$initTinyMCE([
            "autolink link"
        ] );

        $('#map_marker_type').select2({
            dropdownParent: $('#map_marker_type').parent(),
        }).on("select2:select", function(e){
            this.form.mapMarkerTypes = e.params.data.element.id
        }.bind(this))
            .val(this.mapMarkerTypes[this.form.type_id])
            .trigger('change');

        $('#map_marker_category').select2({
            dropdownParent: $('#map_marker_category').parent(),
        }).on("select2:select", function(e){
            this.form.mapMarkerCategories = e.params.data.element.id
        }.bind(this))
            .val(this.mapMarkerCategories[this.form.category_id])
            .trigger('change');
    }
}
</script>
