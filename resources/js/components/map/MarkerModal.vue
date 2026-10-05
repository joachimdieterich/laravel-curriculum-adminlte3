<template>
    <Modal
        model="marker"
        modalName="map-marker-modal"
        url="/mapMarkers"
        :form="form"
        :require-title="true"
        :disable-save-button="!form.latitude || !form.longitude"
    >
        <template #general-extended>
            <input
                id="marker-teaser-text"
                type="text"
                class="form-control mt-3"
                maxlength="191"
                v-model.trim="form.teaser_text"
                :placeholder="trans('global.marker.fields.teaser_text')"
            />

            <div class="mt-3">
                <Editor
                    id="marker-description"
                    licenseKey="gpl"
                    :init="tinyMCE"
                    v-model="form.description"
                />
            </div>

            <div v-if="checkPermission('is_admin')" class="mt-3">
                <label for="marker-map-id" class="form-label">Map ID</label>
                <input
                    id="marker-map-id"
                    type="number"
                    min="1"
                    class="form-control"
                    v-model="form.map_id"
                    placeholder="Map ID"
                />
            </div>

            <Select2
                id="marker-type"
                css="mt-3"
                url="/mapMarkerTypes"
                model="mapMarkerType"
                :selected="form.type_id"
                @selectedValue="id => form.type_id = id[0]"
            />

            <Select2
                id="marker-category"
                css="mt-3"
                url="/mapMarkerCategories"
                model="mapMarkerCategory"
                :selected="form.category_id"
                @selectedValue="id => form.category_id = id[0]"
            />

            <div class="mt-3">
                <label class="form-label">{{ trans('global.marker.fields.coordinates') }}</label>
                <div class="d-flex gap-2 align-items-center">
                    <button
                        type="button"
                        class="btn btn-default text-nowrap"
                        @click="triggerEvent()"
                    >
                        <i class="fa fa-location"></i>
                        {{ trans('global.select') }}
                    </button>

                    <span class="input-group">
                        <input
                            id="marker-latitude"
                            type="number"
                            class="form-control"
                            v-model.trim="form.latitude"
                            placeholder="Latitude"
                            required
                        />
                        <input
                            id="marker-longitude"
                            type="number"
                            class="form-control"
                            v-model.trim="form.longitude"
                            placeholder="Longitude"
                            required
                        />
                    </span>
                </div>
            </div>

            <div v-if="checkPermission('is_admin')" class="mt-3">
                <label for="marker-tags" class="form-label">{{ trans('global.tag.title') }}</label>
                <input
                    id="marker-tags"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model.trim="form.tags"
                    :placeholder="trans('global.tag.title')"
                />
            </div>

            <div class="mt-3">
                <label for="marker-author" class="form-label">{{ trans('global.marker.fields.author') }}</label>
                <input
                    id="marker-author"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model.trim="form.author"
                    :placeholder="trans('global.marker.fields.author')"
                />
            </div>

            <div class="mt-3">
                <label for="marker-address" class="form-label">{{ trans('global.address') }}</label>
                <input
                    id="marker-address"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model.trim="form.address"
                    :placeholder="trans('global.address')"
                />
            </div>

            <div class="mt-3">
                <label for="marker-url" class="form-label">{{ trans('global.url') }}</label>
                <input
                    id="marker-url"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model.trim="form.url"
                    :placeholder="trans('global.url')"
                />
            </div>

            <div class="mt-3">
                <label for="marker-url-title" class="form-label">{{ trans('global.url_title') }}</label>
                <input
                    id="marker-url-title"
                    type="text"
                    class="form-control"
                    maxlength="191"
                    v-model.trim="form.url_title"
                    placeholder="Link"
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
    name: 'map-marker-modal',
    components: {
        Modal,
        Editor,
        Select2,
    },
    props: {
        map: {
            type: Object,
            default: null,
        },
        clickedCoordinates: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                title: '',
                teaser_text: '',
                description: '',
                author: '',
                type_id: 1,
                category_id: 1,
                tags: null,
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
        triggerEvent() {
            this.$emit('setCoordinates');
            // trigger closing-animation
            this.$el.classList.add('modal-leave-to');
            // actually hide the modal-layer, so the map underneath can be clicked
            setTimeout(() => this.$el.classList.add('d-none'), 300);
        },
    },
    watch: {
        clickedCoordinates(newValue) {
            if (newValue) {
                this.form.latitude = newValue.lat;
                this.form.longitude = newValue.lng;
                // 're-open' the modal
                this.$el.classList.remove('d-none');
                setTimeout(() => this.$el.classList.remove('modal-leave-to'), 10);
            }
        }
    },
}
</script>