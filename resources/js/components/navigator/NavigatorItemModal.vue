<template>
    <Modal
        ref="modal"
        model="navigatorItem"
        modalName="navigator-item-modal"
        :form="form"
        :show-display-section="form.referenceable_type == 'App\\NavigatorView'"
        :show-medium-field="true"
        @opened="preProcessing()"
    >
        <template #general>
            <ul v-if="!form.id"
                class="nav nav-pills nav-fill"
            >
                <!-- View -->
                <li class="nav-item">
                    <a
                        class="nav-link"
                        :class="
                        {
                            show: (this.form.referenceable_type ==  'App\\NavigatorView'),
                            active: (this.form.referenceable_type ==  'App\\NavigatorView')
                        }"
                        @click="setCurrentTab('NavigatorView')"
                    >
                        <i class="fa fa-map-signs me-2"></i>{{ trans('global.navigator_view.title_singular') }}
                    </a>
                </li>
                <!-- Curriculum -->
                <li class="nav-item">
                    <a
                        class="nav-link"
                        :class="
                        {
                            show: (this.form.referenceable_type ==  'App\\Curriculum'),
                            active: (this.form.referenceable_type ==  'App\\Curriculum')
                        }"
                        @click="setCurrentTab('Curriculum')"
                    >
                        <i class="fas fa-th me-2"></i>{{ trans('global.curriculum.title') }}
                    </a>
                </li>
                <!-- Content -->
                <!-- <li class="nav-item">
                    <a class="nav-link"
                    :class="
                    {
                        show: (this.form.referenceable_type ==  'App\\Content'),
                        active: (this.form.referenceable_type ==  'App\\Content')
                    }"
                    @click="setCurrentTab('Content')"
                    >
                        <i class="fa fa-align-justify me-2"></i>{{ trans('global.content.title_singular') }}
                    </a>
                </li> -->
            </ul>

            <div class="tab-content pt-2">
                <div v-if="form.referenceable_type == 'App\\Curriculum'">
                    <Select2
                        id="navigator-item-curriculum-id"
                        url="/curricula"
                        model="curriculum"
                        :selected="form.referenceable_id"
                        @selectedValue="id => form.referenceable_id = id[0]"
                    />
                </div>

                <div id="tab_navigator_view">
                    <div v-if="form.referenceable_type == 'App\\NavigatorView' || form.id"
                        class="mt-3"
                    >
                        <input
                            id="navigator-item-title"
                            type="text"
                            class="form-control"
                            maxlength="191"
                            v-model.trim="form.title"
                            :placeholder="trans('global.title') + ' *'"
                            required
                        />
                    </div>

                    <div v-if="form.referenceable_type == 'App\\NavigatorView' || form.id"
                        class="mt-3"
                    >
                        <Editor
                            id="navigator-item-description"
                            licenseKey="gpl"
                            :init="tinyMCE"
                            v-model="form.description"
                        />
                    </div>
                </div>

                <Select2
                    id="navigator-item-position"
                    css="mt-3"
                    :label="trans('global.navigator_item.fields.position')"
                    :list="[
                        {
                            id: 'content',
                            title: trans('global.content.title_singular'),
                        },
                        {
                            id: 'footer',
                            title: trans('global.footer'),
                        },
                        {
                            id: 'header',
                            title: trans('global.header'),
                        }
                    ]"
                    :selected="form.position"
                    @selectedValue="pos => this.form.position = pos[0]"
                />

                <Select2
                    id="navigator-item-css-class"
                    css="mt-3"
                    :label="trans('global.navigator_item.fields.css_class')"
                    :list="[
                        {
                            id: 'col-xs-12',
                            title: 'col-xs-12',
                        },
                        {
                            id: 'col-12',
                            title: 'col-12',
                        },
                    ]"
                    :selected="form.css_class"
                    @selectedValue="id => form.css_class = id[0]"
                />

                <Select2
                    id="navigator-item-visibility"
                    css="mt-3"
                    :label="trans('global.visibility')"
                    :list="[
                        {
                            id: '1',
                            title: trans('global.navigator_item.fields.visibility_show'),
                        },
                        {
                            id: '2',
                            title: trans('global.navigator_item.fields.visibility_hide'),
                        },
                    ]"
                    :selected="form.visibility"
                    @selectedValue="id => form.visibility = id[0]"
                />
            </div>
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";
import Editor from '@tinymce/tinymce-vue';

export default {
    name: 'navigator-item-modal',
    components: {
        Modal,
        Editor,
        Select2,
    },
    props: {
        navigator: {
            type: Object,
            default: null,
        },
        view: {
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
                description: '',
                organization_id: null,
                referenceable_type: 'App\\NavigatorView',
                referenceable_id: '',
                navigator_id: null,
                position: 'content',
                color: null,
                css_class: 'col-12',
                visibility: '1',
                medium_id: null,
                view_id: null,
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink", "link", "autoresize",
                ],
                {
                    'callback': 'insertContent',
                    'callbackId': this.component_id
                },
                null,
                ""                
            ),
        }
    },
    methods: {
        preProcessing() {
            this.form.navigator_id = this.navigator.id;
            this.form.view_id = this.view.id;
        },
        setCurrentTab(type) {
            this.form.referenceable_type = 'App\\' + type;
        },
    },
}
</script>