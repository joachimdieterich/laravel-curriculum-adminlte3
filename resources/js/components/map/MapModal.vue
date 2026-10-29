<template>
    <Modal
        model="map"
        modalName="map-modal"
        :form="form"
        :show-general-header="true"
        :require-title="true"
        :show-display-section="true"
        :show-medium-field="true"
    >
        <template #general-extended>
            <input
                id="map-subtitle"
                type="text"
                class="form-control mt-3"
                maxlength="191"
                v-model.trim="form.subtitle"
                :placeholder="trans('global.map.fields.subtitle')"
                required
            />

            <div class="mt-3">
                <Editor
                    id="map-description"
                    licenseKey="gpl"
                    :init="tinyMCE"
                    v-model="form.description"
                />
            </div>

            <Select2 v-if="form.id && isAdmin"
                id="map-owner"
                css="mt-3"
                :label="trans('global.change_owner')"
                model="User"
                url="/users"
                :selected="form.owner_id"
                @selectedValue="id => form.owner_id = id[0]"
            />

            <Select2
                id="map-marker-type"
                css="mt-3"
                url="/mapMarkerTypes"
                model="mapMarkerType"
                :selected="form.type_id"
                @selectedValue="id => form.type_id = id[0]"
            />

            <Select2
                id="map-marker-category"
                css="mt-3"
                url="/mapMarkerCategories"
                model="mapMarkerCategory"
                :selected="form.category_id"
                @selectedValue="id => form.category_id = id[0]"
            />

            <div v-if="isAdmin" class="mt-3">
                <label for="map-border-url" class="form-label">{{ trans('global.map.fields.border_url') }}</label>
                <input
                    id="map-border-url"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model.trim="form.border_url"
                    :placeholder="trans('global.map.fields.border_url_helper')"
                    required
                />
            </div>

            <div v-if="isAdmin" class="mt-3">
                <label for="map-latitude" class="form-label">{{ trans('global.map.fields.latitude') }}</label>
                <input
                    id="map-latitude"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model.trim="form.latitude"
                    :placeholder="trans('global.map.fields.latitude')"
                />
            </div>

            <div v-if="isAdmin" class="mt-3">
                <label for="map-longitude" class="form-label">{{ trans('global.map.fields.longitude') }}</label>
                <input
                    id="map-longitude"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model.trim="form.longitude"
                    :placeholder="trans('global.map.fields.longitude')"
                />
            </div>

            <div v-if="isAdmin" class="mt-3">
                <label for="map-zoom" class="form-label">{{ trans('global.map.fields.zoom') }}</label>
                <input
                    id="map-zoom"
                    type="number"
                    class="form-control"
                    min="0"
                    max="15"
                    v-model="form.zoom"
                    :placeholder="trans('global.map.fields.zoom')"
                    required
                />
            </div>
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Editor from "@tinymce/tinymce-vue";
import Select2 from "../forms/Select2.vue";

export default {
    name: 'map-modal',
    components: {
        Modal,
        Editor,
        Select2,
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                title: '',
                subtitle: '',
                description: '',
                owner_id: null,
                tags: null, // currently not in use
                type_id: 2,
                category_id: 2,
                border_url: '',
                latitude: '49.9551389',
                longitude: '6.31027777',
                zoom: 10,
                color: '#F2C511',
                medium_id: null,
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
                ''
            ),
        }
    },
    computed: {
        isAdmin() {
            return this.checkPermission('is_admin');
        },
    },
}
</script>