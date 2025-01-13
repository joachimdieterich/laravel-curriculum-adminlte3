<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        <span v-if="method === 'post'">
                            {{ trans('global.meeting.create') }}
                        </span>
                        <span v-if="method === 'patch'">
                            {{ trans('global.meeting.edit') }}
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
                    <ul v-if="method === 'post'"
                        class="nav nav-pills"
                    >
                        <!-- Create -->
                        <li class="nav-item">
                            <a
                                class="nav-link active show"
                                href="#create_meeting"
                                @click="activetab = 'create_meeting';"
                                data-toggle="tab"
                            >
                                {{ trans('global.meeting.create') }}
                            </a>
                        </li>
                        <!-- Import -->
                        <li class="nav-item">
                            <a
                                class="nav-link"
                                href="#import_meeting"
                                @click="activetab = 'import_meeting';"
                                data-toggle="tab"
                            >
                                {{ trans('global.meeting.import') }}
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content pt-2">
                        <div
                            class="tab-pane show"
                            :class="(activetab == 'create_meeting') ? 'active' : ''"
                            id="create_meeting"
                        >
                            <div class="form-group">
                                <label for="uid">
                                    {{ trans('global.meeting.fields.uid') }}
                                </label>
                                <input
                                    type="text"
                                    id="uid"
                                    name="uid"
                                    class="form-control"
                                    v-model.trim="form.uid"
                                    :placeholder="trans('global.meeting.fields.uid')"
                                    required
                                />
                                <p v-if="form.errors.uid"
                                    class="help-block"
                                    v-text="form.errors.uid[0]"
                                ></p>
                            </div>
                            <div class="form-group">
                                <label for="title">
                                    {{ trans('global.meeting.fields.title') }}
                                </label>
                                <input
                                    type="text"
                                    id="title"
                                    name="title"
                                    class="form-control"
                                    v-model.trim="form.title"
                                    :placeholder="trans('global.meeting.fields.title')"
                                    required
                                />
                                <p v-if="form.errors.title"
                                    class="help-block"
                                    v-text="form.errors.title[0]"
                                ></p>
                            </div>

                            <div class="form-group">
                                <label for="description">
                                    {{ trans('global.meeting.fields.description') }}
                                </label>
                                <Editor
                                    id="description"
                                    name="description"
                                    class="form-control"
                                    :init="tinyMCE"
                                    :initial-value="form.description"
                                />
                                <p v-if="form.errors.description"
                                    class="help-block"
                                    v-text="form.errors.description[0]"
                                ></p>
                            </div>

                            <div class="form-group ">
                                <label for="info">
                                    {{ trans('global.meeting.fields.info') }}
                                </label>
                                <Editor
                                    id="info"
                                    name="info"
                                    class="form-control"
                                    :init="tinyMCE"
                                    :initial-value="form.info"
                                />
                                <p v-if="form.errors.info"
                                    class="help-block"
                                    v-text="form.errors.info[0]"
                                ></p>
                            </div>

                            <div class="form-group">
                                <label for="speakers">
                                    {{ trans('global.meeting.fields.speakers') }}
                                </label>
                                <Editor
                                    id="speakers"
                                    name="speakers"
                                    class="form-control"
                                    :init="tinyMCE"
                                    :initial-value="form.speakers"
                                />
                                <p v-if="form.errors.speakers"
                                    class="help-block"
                                    v-text="form.errors.speakers[0]"
                                ></p>
                            </div>

                            <div class="form-group">
                                <label for="livestream">
                                    {{ trans('global.meeting.fields.livestream') }}
                                </label>
                                <Editor
                                    id="livestream"
                                    name="livestream"
                                    class="form-control"
                                    :init="tinyMCE"
                                    :initial-value="form.livestream"
                                />
                                <p v-if="form.errors.livestream"
                                    class="help-block"
                                    v-text="form.errors.livestream[0]"
                                ></p>
                            </div>

                            <div
                                class="card-header border-bottom"
                                data-card-widget="collapse"
                            >
                                <h5 class="card-title">{{ trans('global.display') }}</h5>
                            </div>

                            <div class="card-body pb-0">
                                <v-swatches
                                    :swatch-size="49"
                                    :trigger-style="{}"
                                    popover-to="right"
                                    v-model="this.form.color"
                                    show-fallback
                                    fallback-input-type="color"
                                    @input="(id) => {
                                        if(id.isInteger) {
                                            this.form.color = id;
                                        }
                                    }"
                                    :max-height="300"
                                />

                                <MediumForm
                                    class="pull-right"
                                    id="medium_id"
                                    :medium_id="form.medium_id"
                                    accept="image/*"
                                    :selected="this.form.medium_id"
                                    @selectedValue="(id) => {
                                        this.form.medium_id = id;
                                    }"
                                />
                            </div>
                        </div>

                        <div v-if="method == 'post'"
                            class="tab-pane "
                            :class="(activetab == 'import_meeting') ? 'active' : ''"
                            id="import_meeting"
                        >
                            <div class="form-group">
                                <label for="uid">
                                    {{ trans('global.meeting.fields.uid') }}
                                </label>
                                <div class="input-group">
                                    <input
                                        type="text"
                                        id="import_uid"
                                        name="import_uid"
                                        class="form-control"
                                        v-model.trim="form.uid"
                                        :placeholder="trans('global.meeting.fields.uid')"
                                        required
                                    />
                                    <div
                                        class="input-group-append"
                                        @click="loadImportData()"
                                    >
                                        <span class="input-group-text">
                                            <i class="fas fa-check"></i>
                                        </span>
                                    </div>
                                </div>
                                <div
                                    id="loading-event_import"
                                    class="overlay text-center"
                                    style="width:100% !important; display: none;"
                                >
                                    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p v-if="form.errors.uid"
                                    class="help-block"
                                    v-text="form.errors.uid[0]"
                                ></p>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="meeting-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            v-if="this.activetab == 'create_meeting'"
                            id="meeting-save"
                            class="btn btn-primary"
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
import MediumModal from "../media/MediumModal.vue";
import MediumForm from "../media/MediumForm.vue";
import axios from "axios";
import Editor from "@tinymce/tinymce-vue";
import Select2 from "../forms/Select2.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'meeting-modal',
    components: {
        Editor,
        MediumModal,
        Select2,
        MediumForm
    },
    props: {},
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            url: '/meetings',
            form: new Form({
                'id': '',
                'uid': '',
                'title':  '',
                'description':  '',
                'info': '',
                'speakers': '',
                'livestream': '',
                'color':'#27AF60',
                'medium_id': null,
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink link curriculummedia table lists autoresize"
                ],
                {
                    'callback': 'insertContent',
                    'callbackId': this.component_id
                }
            ),
            activetab: 'create_meeting',
        }
    },
    computed: {
        textColor: function() {
            return this.$textcolor(this.form.color, '#333333');
        }
    },
    methods: {
        submit(method) {
            if (method == 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            axios.post(this.url, this.form)
                .then(r => {
                    this.$eventHub.emit('meeting-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            console.log('update');
            axios.patch(this.url + '/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('meeting-updated', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        loadImportData() {
            $("#loading-event_import").show();
            axios.get('/meetings/getImportDataByUid?uid=' + this.form.uid)
                .then(response => {
                    this.form.populate(response.data);
                    tinyMCE.get('description').setContent(this.form.description);
                    tinyMCE.get('info').setContent(this.form.info);
                    tinyMCE.get('speakers').setContent(this.form.speakers);
                    tinyMCE.get('livestream').setContent(this.form.livestream);

                    this.method = 'patch';
                    this.activetab = 'create_meeting';
                    $("#loading-event_import").hide();
                })
                .catch(e => {
                    console.log(e);
                });
        }
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params;
                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                    this.form.description = this.htmlToText(params.description);
                    this.form.info = this.htmlToText(params.info);
                    this.form.speakers = this.htmlToText(params.speakers);
                    this.form.livestream = this.htmlToText(params.livestream);
                    if (this.form.id !== '') {
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

