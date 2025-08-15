<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @mouseup.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                    <span v-if="method === 'post'">
                        {{ trans('global.navigator_view.create') }}
                    </span>
                        <span v-if="method === 'patch'">
                        {{ trans('global.navigator_view.edit') }}
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
                        <div class="card-body">
                            <ul v-if="method !=  'patch'"
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
                                        <i class="fa fa-map-signs mr-2"></i>{{ trans('global.navigator_view.title_singular') }}
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
                                        <i class="fas fa-th mr-2"></i>{{ trans('global.curriculum.title') }}
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
                                        <i class="fa fa-align-justify mr-2"></i>{{ trans('global.content.title_singular') }}
                                    </a>
                                </li> -->
                            </ul>
        
                            <div class="tab-content pt-2">
                                <div v-if="form.referenceable_type == 'App\\Curriculum'">
                                    <Select2
                                        id="referenceable_id"
                                        name="referenceable_id"
                                        url="/curricula"
                                        model="curriculum"
                                        :selected="form.referenceable_id"
                                        @selectedValue="(id) => {
                                            this.form.referenceable_id = id;
                                        }"
                                    />
                                </div>
        
                                <div id="tab_navigator_view">
                                    <div v-if="form.referenceable_type ==  'App\\NavigatorView'
                                            || method == 'patch'
                                        "
                                        class="form-group"
                                    >
                                        <input
                                            id="title"
                                            type="text"
                                            name="title"
                                            class="form-control"
                                            v-model="form.title"
                                            :placeholder="trans('global.title') + ' *'"
                                            required
                                        />
                                    </div>
        
                                    <div v-if="form.referenceable_type ==  'App\\NavigatorView'
                                            || method == 'patch'
                                        "
                                        class="form-group"
                                    >
                                        <Editor
                                            id="description"
                                            name="description"
                                            class="form-control"
                                            :init="tinyMCE"
                                            v-model="form.description"
                                        />
                                    </div>
                                </div>
        
                                <Select2
                                    id="position"
                                    name="position"
                                    model=""
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
                                    @selectedValue="(id) => {
                                        this.form.position = id;
                                    }"
                                />
                                <Select2
                                    id="css_class"
                                    name="css_class"
                                    model=""
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
                                    @selectedValue="(id) => {
                                        this.form.css_class = id;
                                    }"
                                />
                                <Select2
                                    id="visibility"
                                    name="visibility"
                                    model=""
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
                                    @selectedValue="(id) => {
                                        this.form.visibility = id;
                                    }"
                                />
                            </div>
        
                            <div v-if="form.referenceable_type == 'App\\NavigatorView'"
                                id="tab_curriculum_view"
                                class="tab-pane"
                            >
                                <MediumForm
                                    class="pull-right"
                                    id='navigator_item_medium'
                                    :medium_id="form.medium_id"
                                    accept="image/*"
                                    :selected="form.medium_id"
                                    @selectedValue="(id) => {
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
                            id="navigator-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="navigator-save"
                            class="btn btn-primary ml-3"
                            @click="submit(method)"
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
import Select2 from "../forms/Select2.vue";
import Editor from '@tinymce/tinymce-vue';
import MediumForm from "../media/MediumForm.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'navigator-item-modal',
    components:{
        MediumForm,
        Select2,
        Editor
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
    setup() { //use database store
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            url: '/navigatorItems',
            form: new Form({
                id: '',
                title: '',
                description:  '',
                organization_id: '',
                referenceable_type: 'App\\NavigatorView',
                referenceable_id: '',
                navigator_id: '',
                position: 'content',
                css_class: 'col-12',
                visibility: '1',
                medium_id: null,
                view_id: null,
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink link autoresize"
                ],
                {
                    'callback': 'insertContent',
                    'callbackId': this.component_id
                }
            ),
        }
    },
    methods: {
        submit(method) {
            this.form.navigator_id = this.navigator.id;
            this.form.view_id = this.view.id;

            if (tinyMCE.get('description')) {
                this.form.description = tinyMCE.get('description').getContent();
            }

            if (method === 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            axios.post(this.url, this.form)
                .then(r => {
                    this.$eventHub.emit('navigatorItem-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch(this.url + '/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('navigatorItem-updated', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        setCurrentTab(type) {
            this.form.referenceable_type = 'App\\' + type;
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params;
                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                    if (this.form.id !== '') {
                        this.form.description = this.$decodeHTMLEntities(this.$decodeHtml(this.form.description));
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