<template>
    <Transition name="modal">
        <div v-if="show"
             class="modal-mask"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                    <span v-if="method === 'post'">
                        {{ trans('global.navigator.create') }}
                    </span>
                        <span v-if="method === 'patch'">
                        {{ trans('global.navigator.edit') }}
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
                    <ul class="nav nav-pills">
                        <!-- View -->
                        <li class="nav-item">
                            <a class="nav-link active show"
                               href="#tab_navigator_view"
                               @click="setCurrentTab('NavigatorView')"
                               data-toggle="tab">
                                <i class="fa fa-map-signs mr-2"></i>{{ trans('global.navigator_view.title_singular') }}
                            </a>
                        </li>
                        <!-- Curriculum -->
                        <li class="nav-item">
                            <a class="nav-link"
                               href="#tab_curriculum_view"
                               @click="setCurrentTab('Curriculum')"
                               data-toggle="tab">
                                <i class="fas fa-th mr-2"></i>{{ trans('global.curriculum.title') }}
                            </a>
                        </li>
                        <!-- Content -->
                        <li class="nav-item">
                            <a class="nav-link"
                               href="#organization_subscription"
                               @click="setCurrentTab('Content')"
                               data-toggle="tab">
                                <i class="fa fa-align-justify mr-2"></i>{{ trans('global.content.title_singular') }}
                            </a>
                        </li>
<!--                        <li v-if="this.form.referenceable_type = 'App\\Medium'"
                            class="nav-item">
                            <a class="nav-link" href="#token_subscription" data-toggle="tab">
                                <i class="fa fa-photo-video mr-2"></i>{{ trans('global.medium.title_singular') }}
                            </a>
                        </li>-->
                    </ul>


                    <div class="tab-content pt-2">
                        <div v-if="this.currentTab == 'Curriculum'"
                             class="tab-pane" id="tab_curriculum_view">
                            <Select2
                                id="referenceable_id"
                                name="referenceable_id"
                                url="/curricula"
                                model="curriculum"
                                :selected="this.form.referenceable_id"
                                @selectedValue="(id) => {
                                    this.form.referenceable_id = id;
                                }"
                            >
                            </Select2>
                        </div>
                        <!-- User Tab -->
                        <div v-if="this.currentTab == 'NavigatorView'"
                             id="tab_navigator_view"
                             class="tab-pane active show" >
                            <div
                                v-if="this.currentTab != 'Curriculum'"
                                class="form-group"
                                 :class="form.errors.title ? 'has-error' : ''"
                            >
                                <label for="title">{{ trans('global.navigator.fields.title') }} *</label>
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
                                <label for="body">{{ trans('global.navigator.fields.description') }}</label>
                                <Editor
                                    id="description"
                                    name="description"
                                    class="form-control"
                                    :init="tinyMCE"
                                    :initial-value="form.description"
                                />
                                <p class="help-block" v-if="form.errors.description" v-text="form.errors.description[0]"></p>

                            </div>
                        </div>

                        <Select2
                            id="position"
                            name="position"
                            model=""
                            :label="trans('global.navigator_item.fields.position')"
                            :list="[
                                        {
                                            'id':'content',
                                            'title': trans('global.content.title_singular')
                                        },
                                        {
                                            'id':'footer',
                                            'title': trans('global.footer')
                                        },
                                        {
                                            'id':'header',
                                            'title': trans('global.header')
                                        }
                                    ]"
                            :selected="this.form.position"
                        >
                        </Select2>
                        <Select2
                            id="css_class"
                            name="css_class"
                            model=""
                            :label="trans('global.navigator_item.fields.css_class')"
                            :list="[
                                        {
                                            'id':'col-xs-12',
                                            'title': 'col-xs-12'
                                        },
                                        {
                                            'id':'col-12',
                                            'title': 'col-12'
                                        },
                                    ]"
                            :selected="this.form.css_class"
                        >
                        </Select2>
                        <Select2
                            id="visibility"
                            name="visibility"
                            model=""
                            :label="trans('global.visibility')"
                            :list="[
                                        {
                                            'id':'1',
                                            'title': trans('global.navigator_item.fields.visibility_show')
                                        },
                                        {
                                            'id':'2',
                                            'title': trans('global.navigator_item.fields.visibility_hide')
                                        },
                                    ]"
                            :selected="this.form.visibility"
                        >
                        </Select2>
                    </div>

                    <div v-if="this.currentTab == 'App\\Curriculum' || 'App\\NavigatorView'"
                         class="tab-pane" id="tab_curriculum_view">
                        <MediumForm
                            class="pull-right"
                            id='navigator_item_medium'
                            :medium_id="form.medium_id"
                            accept="image/*"

                            :selected="this.form.medium_id"
                            @selectedValue="(id) => {
                                    this.form.medium_id = id;
                                }"
                        >
                        </MediumForm>
                    </div>

                </div>
                <div class="card-footer">
                 <span class="pull-right">
                     <button
                         id="navigator-cancel"
                         type="button"
                         class="btn btn-default"
                         @click="$emit('close')">
                         {{ trans('global.cancel') }}
                     </button>
                     <button
                         id="navigator-save"
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
import Select2 from "../forms/Select2.vue";
import Editor from '@tinymce/tinymce-vue';
import MediumForm from "../media/MediumForm.vue";
import {useMediumStore} from "../../store/media";



export default {
    components:{
        MediumForm,

        Select2,
        Editor
    },
    props: {
        show: {
            type: Boolean
        },
        navigator: {
            type: Object
        },
        params: {
            type: Object
        },  //{ 'modelId': curriculum.id, 'modelUrl': 'curriculum' , 'shareWithToken': true, 'canEditCheckbox': false}
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            url: '/navigatorItems',
            currentTab: 'NavigatorView',
            form: new Form({
                'id':'',
                'title': '',
                'description':  '',
                'organization_id': '',
                'referenceable_type': 'App\\NavigatorView',
                'referenceable_id': '',
                'navigator_id': '',
                'position': '',
                'css_class': '',
                'visibility': '',
                'medium_id': '',
            }),
            search: '',
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
    setup () { //use database store
        const mediumStore = useMediumStore();
        return {
            mediumStore,
        }
    },
    watch: {
        params: function(newVal, oldVal) {
            this.form.reset();
            this.method = 'post';
            this.form.populate(newVal);

            if (this.form.id != ''){
                this.method = 'patch';
            }
        },
    },

    methods: {
        submit(method) {
            this.form.navigator_id = this.navigator.id;
            this.form.description = tinyMCE.get('description').getContent();

            switch (this.currentTab) {
                case 'NavigatorView':
                    break;
            }

            if (method === 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add(){
            axios.post(this.url, this.form)
                .then(r => {
                    this.$eventHub.emit('navigator-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update(){
            axios.patch(this.url + '/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('navigator-updated', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        setCurrentTab(type){
            console.log(type);
            this.currentTab = type;
            this.referenceable_type = 'App\\' + type;
            console.log(this.referenceable_type);

        },
    },
    mounted() {},
}
</script>
