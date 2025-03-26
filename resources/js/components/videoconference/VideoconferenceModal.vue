<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @click.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        <span v-if="method === 'post'">
                            {{ trans('global.videoconference.create') }}
                        </span>
                        <span v-if="method === 'patch'">
                            {{ trans('global.videoconference.edit') }}
                        </span>
                    </h3>

                    <div class="card-tools">
                        <button type="button"
                                class="btn btn-tool"
                                @click="globalStore?.closeModal($options.name)">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="card">
                        <div
                            class="card-header border-bottom"
                            data-card-widget="collapse"
                        >
                            <h5 class="card-title">{{ trans('global.general') }}</h5>
                        </div>

                        <div class="card-body pb-0">
                            <div class="form-group">
                                <input
                                    type="text"
                                    id="meetingName"
                                    name="meetingName"
                                    class="form-control"
                                    v-model.trim="form.meetingName"
                                    :placeholder="trans('global.videoconference.fields.meetingName') + ' *'"
                                    required
                                />
                                <p class="help-block" v-if="form.errors.meetingName" v-text="form.errors.meetingName[0]"></p>
                            </div>

                            <Select2 v-if="checkPermission('is_admin')"
                                id="user_id"
                                :label="trans('global.change_owner')"
                                model="User"
                                url="/users"
                                :selected="form.owner_id"
                                @selectedValue="(id) => this.form.owner_id = id[0]"
                            />

                            <div v-if="showExtendedSettings">
                                <Select2 v-if="servers.length"
                                    id="server"
                                    name="server"
                                    :list="servers"
                                    :label="trans('global.videoconference.fields.server')"
                                    model="videoconference"
                                    option_id="id"
                                    option_label="BBB_SERVER_NAME"
                                    selected="form.server"
                                    :placeholder="trans('global.videoconference.fields.server')"
                                    @selectedValue="(id) => {
                                        this.form.server = id;
                                    }"
                                />

                                <div class="form-group">
                                    <label for="welcomeMessage">
                                        {{ trans('global.videoconference.fields.welcomeMessage') }}
                                    </label>
                                    <Editor
                                        id="welcomeMessage"
                                        name="welcomeMessage"
                                        class="form-control"
                                        :init="tinyMCE"
                                        :initial-value="form.welcomeMessage"
                                    />
                                    <p class="help-block"
                                    v-if="form.errors.welcomeMessage"
                                    v-text="form.errors.welcomeMessage[0]"
                                    ></p>
                                </div>

                                <div class="form-group">
                                    <label for="moderatorOnlyMessage">
                                        {{ trans('global.videoconference.fields.moderatorOnlyMessage') }}
                                    </label>
                                    <Editor
                                        id="moderatorOnlyMessage"
                                        name="moderatorOnlyMessage"
                                        class="form-control"
                                        :init="tinyMCE"
                                        :initial-value="form.moderatorOnlyMessage"
                                    />
                                    <p class="help-block"
                                    v-if="form.errors.moderatorOnlyMessage"
                                    v-text="form.errors.moderatorOnlyMessage[0]"
                                    ></p>
                                </div>
                
                                <div class="form-group">
                                    <label for="maxParticipants">
                                        {{ trans('global.videoconference.fields.maxParticipants') }}
                                    </label>
                                    <input
                                        type="number"
                                        id="maxParticipants"
                                        name="maxParticipants"
                                        class="form-control"
                                        v-model.trim="form.maxParticipants"
                                    />
                                    <small class="help-block">
                                        {{ trans('global.videoconference.fields.maxParticipants_helper') }}
                                    </small>
                                    <p class="help-block"
                                    v-if="form.errors?.maxParticipants"
                                    v-text="form.errors?.maxParticipants[0]"
                                    ></p>
                                </div>

                                <div class="form-group">
                                    <label for="duration">
                                        {{ trans('global.videoconference.fields.duration') }}
                                    </label>
                                    <input
                                        type="number"
                                        id="duration"
                                        name="duration"
                                        class="form-control"
                                        v-model.trim="form.duration"
                                    />
                                    <small class="help-block">
                                        {{ trans('global.videoconference.fields.duration_helper') }}
                                    </small>
                                    <p class="help-block"
                                    v-if="form.errors?.duration"
                                    v-text="form.errors?.duration[0]"
                                    ></p>
                                </div>

                                <div class="form-group">
                                    <label for="logoutUrl">
                                        {{ trans('global.videoconference.fields.logoutUrl') }}
                                    </label>
                                    <input
                                        type="text"
                                        id="logoutUrl"
                                        name="logoutUrl"
                                        class="form-control"
                                        required
                                        v-model.trim="form.logoutUrl"
                                    />
                                    <p class="help-block">
                                        {{ trans('global.videoconference.fields.logoutUrl_helper') }}
                                    </p>
                                    <p class="help-block"
                                    v-if="form.errors?.logoutUrl"
                                    v-text="form.errors?.logoutUrl[0]"
                                    ></p>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch custom-switch-on-green">
                                        <input 
                                            id="endWhenNoModerator"
                                            type="checkbox"
                                            class="custom-control-input"
                                            v-model="form.endWhenNoModerator"
                                        />
                                        <label
                                            class="custom-control-label font-weight-light"
                                            for="endWhenNoModerator"
                                        >
                                            {{ trans('global.videoconference.fields.endWhenNoModerator') }}
                                        </label>
                                    </div>
                                </div>

                                <div v-if="form.endWhenNoModerator"
                                    class="form-group"
                                >
                                    <label for="categorie">
                                        {{ trans('global.videoconference.fields.endWhenNoModeratorDelayInMinutes') }}
                                    </label>
                                    <input
                                        type="number"
                                        id="endWhenNoModeratorDelayInMinutes"
                                        name="endWhenNoModeratorDelayInMinutes"
                                        class="form-control"
                                        v-model.trim="form.endWhenNoModeratorDelayInMinutes"
                                    />
                                    <p class="help-block" v-if="form.errors?.endWhenNoModeratorDelayInMinutes" v-text="form.errors?.endWhenNoModeratorDelayInMinutes[0]"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div
                            class="card-header border-bottom"
                            data-card-widget="collapse"
                        >
                            <h5 class="card-title">{{ trans('global.display') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <v-swatches
                                    :swatch-size="49"
                                    :trigger-style="{}"
                                    style="height: 42px;"
                                    popover-to="right"
                                    v-model="form.bannerColor"
                                    show-fallback
                                    fallback-input-type="color"
                                    @input="(id) => {
                                        this.form.bannerColor = id;
                                    }"
                                    :max-height="300"
                                />
        
                                <MediumForm v-if="form.id"
                                    :id="'medium_form' + component_id"
                                    :medium_id="form.medium_id"
                                    :subscribable_id="form.id"
                                    subscribable_type="App\Videoconference"
                                    accept="image/*"
                                    @selectedValue="(id) => {
                                        // on removal of medium, directly update the resource
                                        if (this.form.medium_id !== null && id === null) {
                                            this.$eventHub.emit('videoconference-updated', {
                                                id: this.form.id,
                                                medium_id: null,
                                            });
                                        }
                                        this.form.medium_id = id;
                                    }"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div
                            class="card-header border-bottom"
                            data-card-widget="collapse"
                        >
                            <h5 class="card-title">{{ trans('global.permissions') }}</h5>
                        </div>
                        <div class="card-body pb-0">
                            <div class="form-group">
                                <div class="custom-control custom-switch custom-switch-on-green">
                                    <input
                                        :id="'muteOnStart_' + form.id"
                                        type="checkbox"
                                        class="custom-control-input"
                                        v-model="form.muteOnStart"
                                    >
                                    <label
                                        class="custom-control-label font-weight-light"
                                        :for="'muteOnStart_'+ form.id"
                                    >
                                        {{ trans('global.videoconference.fields.muteOnStart') }}
                                    </label>
                                </div>
                            </div>
        
                            <div class="form-group">
                                <div class="custom-control custom-switch custom-switch-on-green">
                                    <input
                                        :id="'askModerator_' + form.id"
                                        type="checkbox"
                                        class="custom-control-input"
                                        v-model="askModerator"
                                    >
                                    <label
                                        class="custom-control-label font-weight-light"
                                        :for="'askModerator_' + form.id"
                                    >
                                        {{ trans('global.videoconference.ASK_MODERATOR') }}
                                    </label>
                                </div>
                            </div>
        
                            <div class="form-group">
                                <div class="custom-control custom-switch custom-switch-on-green">
                                    <input
                                        :id="'anyoneCanStart_' + form.id"
                                        type="checkbox"
                                        class="custom-control-input"
                                        v-model="form.anyoneCanStart"
                                    >
                                    <label
                                        class="custom-control-label font-weight-light"
                                        :for="'anyoneCanStart_' + form.id"
                                    >
                                        {{ trans('global.videoconference.fields.anyoneCanStart') }}
                                    </label>
                                </div>
                            </div>
        
                            <div class="form-group">
                                <div class="custom-control custom-switch custom-switch-on-green">
                                    <input
                                        v-model="form.allJoinAsModerator"
                                        type="checkbox"
                                        class="custom-control-input"
                                        :id="'allJoinAsModerator_'+ form.id">
                                    <label class="custom-control-label font-weight-light"
                                        :for="'allJoinAsModerator_'+ form.id">
                                        {{ trans('global.videoconference.fields.allJoinAsModerator') }}
                                    </label>
                                </div>
                            </div>
        
                            <Select2 v-if="showExtendedSettings"
                                id="guestPolicy"
                                name="guestPolicy"
                                :list="guestPolicyConstants"
                                model="videoconference"
                                :label="trans('global.videoconference.fields.guestPolicy')"
                                option_id="id"
                                option_label="text"
                                :selected="form.guestPolicy"
                                @selectedValue="(id) => {
                                    this.form.guestPolicy = id;
                                }"
                            />
                        </div>
                    </div>

                    <div v-if="showExtendedSettings"
                        class="card"
                    >
                        <div
                            class="card-header border-bottom"
                            data-card-widget="collapse"
                        >
                            <h5 class="card-title">{{ trans('global.videoconference.audio_video_settings') }}</h5>
                        </div>
                        <div class="card-body pb-0">
                            <div class="form-group">
                                <div class="custom-control custom-switch custom-switch-on-green">
                                    <input
                                        id="lockSettingsDisableCam"
                                        type="checkbox"
                                        class="custom-control-input"
                                        v-model="form.lockSettingsDisableCam"
                                    />
                                    <label
                                        class="custom-control-label font-weight-light"
                                        for="lockSettingsDisableCam"
                                    >
                                        {{ trans('global.videoconference.fields.lockSettingsDisableCam') }}
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch custom-switch-on-green">
                                    <input
                                        id="lockSettingsDisableMic"
                                        type="checkbox"
                                        class="custom-control-input"
                                        v-model="form.lockSettingsDisableMic"
                                    />
                                    <label
                                        class="custom-control-label font-weight-light"
                                        for="lockSettingsDisableMic"
                                    >
                                        {{ trans('global.videoconference.fields.lockSettingsDisableMic') }}
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch custom-switch-on-green">
                                    <input
                                        id="allowModsToEjectCameras"
                                        type="checkbox"
                                        class="custom-control-input"
                                        v-model="form.allowModsToEjectCameras"
                                    >
                                    <label
                                        class="custom-control-label font-weight-light"
                                        for="allowModsToEjectCameras"
                                    >
                                        {{ trans('global.videoconference.fields.allowModsToEjectCameras') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="showExtendedSettings"
                        class="card"
                    >
                        <div 
                            class="card-header border-bottom"
                            data-card-widget="collapse"
                        >
                            <h5 class="card-title">{{ trans('global.videoconference.collaboration_settings') }}</h5>
                        </div>
                        <div class="card-body pb-0">
                            <div class="form-group">
                                <div class="custom-control custom-switch custom-switch-on-green">
                                    <input 
                                        id="lockSettingsDisablePrivateChat"
                                        type="checkbox"
                                        class="custom-control-input"
                                        v-model="form.lockSettingsDisablePrivateChat"
                                    >
                                    <label
                                        class="custom-control-label font-weight-light"
                                        for="lockSettingsDisablePrivateChat"
                                    >
                                        {{ trans('global.videoconference.fields.lockSettingsDisablePrivateChat') }}
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch custom-switch-on-green">
                                    <input
                                        id="lockSettingsDisablePublicChat"
                                        v-model="form.lockSettingsDisablePublicChat"
                                        type="checkbox"
                                        class="custom-control-input"
                                    />
                                    <label
                                        class="custom-control-label font-weight-light"
                                        for="lockSettingsDisablePublicChat"
                                    >
                                        {{ trans('global.videoconference.fields.lockSettingsDisablePublicChat') }}
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch custom-switch-on-green">
                                    <input
                                        id="lockSettingsDisableNote"
                                        type="checkbox"
                                        class="custom-control-input"
                                        v-model="form.lockSettingsDisableNote"
                                    >
                                    <label
                                        class="custom-control-label font-weight-light"
                                        for="lockSettingsDisableNote"
                                    >
                                        {{ trans('global.videoconference.fields.lockSettingsDisableNote') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="showExtendedSettings"
                        class="card"
                    >
                        <div
                            class="card-header border-bottom"
                            data-card-widget="collapse"
                        >
                            <h5 class="card-title">{{ trans('global.videoconference.layout_settings') }}</h5>
                        </div>
                        <div class="card-body pb-0">
                            <Select2
                                id="meetingLayout"
                                name="meetingLayout"
                                :list="meetingLayoutConstants"
                                model="videoconference"
                                :showLabel="false"
                                option_id="id"
                                option_label="text"
                                :selected="form.meetingLayout"
                                @selectedValue="(id) => {
                                    this.form.meetingLayout = id;
                                }"
                            />
        
                            <div class="form-group">
                                <div class="custom-control custom-switch custom-switch-on-green">
                                    <input
                                        id="lockSettingsLockedLayout"
                                        type="checkbox"
                                        class="custom-control-input"
                                        v-model="form.lockSettingsLockedLayout"
                                    />
                                    <label
                                        class="custom-control-label font-weight-light"
                                        for="lockSettingsLockedLayout"
                                    >
                                        {{ trans('global.videoconference.fields.lockSettingsLockedLayout') }}
                                    </label>
                                </div>
                            </div>
        
                            <div class="form-group">
                                <label for="bannerText">
                                    {{ trans('global.videoconference.fields.bannerText') }}
                                </label>
                                <input
                                    type="text"
                                    id="bannerText"
                                    name="bannerText"
                                    class="form-control"
                                    v-model.trim="form.bannerText"
                                />
                                <p class="help-block" v-if="form.errors?.bannerText" v-text="form.errors?.bannerText[0]"></p>
                            </div>
                        </div>
                    </div>

                    <div v-if="showExtendedSettings"
                        class="card"
                    >
                        <div
                            class="card-header border-bottom"
                            data-card-widget="collapse"
                        >
                            <h5 class="card-title">{{ trans('global.videoconference.record_settings') }}</h5>
                        </div>
                        <div class="card-body pb-0">
                            <div class="form-group">
                                <div class="custom-control custom-switch custom-switch-on-green">
                                    <input
                                        id="record"
                                        type="checkbox"
                                        class="custom-control-input"
                                        v-model="form.record"
                                    />
                                    <label
                                        class="custom-control-label font-weight-light"
                                        for="record"
                                    >
                                        {{ trans('global.videoconference.fields.record') }}
                                    </label>
                                </div>
                                <small class="help-block">{{ trans('global.videoconference.fields.record_helper') }}</small>
                            </div>
                            <div v-if="form.record"
                                class="form-group"
                            >
                                <div class="custom-control custom-switch custom-switch-on-green">
                                    <input
                                        id="autoStartRecording"
                                        type="checkbox"
                                        class="custom-control-input"
                                        v-model="form.autoStartRecording"
                                    />
                                    <label
                                        class="custom-control-label font-weight-light"
                                        for="autoStartRecording"
                                    >
                                        {{ trans('global.videoconference.fields.autoStartRecording') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex align-items-center">
                    <div v-permission="'is_admin'">
                        <div class="custom-control custom-switch custom-switch-on-green mr-3">
                            <input
                                v-model="showExtendedSettings"
                                type="checkbox"
                                class="custom-control-input"
                                :id="'extendedSettings_' + form.id"
                            />
                            <label class="custom-control-label font-weight-light"
                                :for="'extendedSettings_' + form.id">
                                {{ trans('global.extendedSettings')}}
                            </label>
                        </div>
                    </div>
                    <div class="ml-auto">
                        <button
                            id="videoconference-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="videoconference-save"
                            class="btn btn-primary ml-3"
                            :disabled="!form.meetingName"
                            @click="submit()"
                        >
                            {{ trans('global.save') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>
<script>
import Form from 'form-backend-validation';
import MediumForm from "../media/MediumForm.vue";
import axios from "axios";
import Editor from "@tinymce/tinymce-vue";
import Select2 from "../forms/Select2.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'videoconference-modal',
    components: {
        Editor,
        Select2,
        MediumForm,
    },
    props: {
        params: {
            type: Object
        },
    },
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
            form: new Form({
                id: '',
                meetingID: '',
                meetingName: '',
                owner_id: null,
                attendeePW: '',
                moderatorPW: '',
                endCallbackUrl: '',
                welcomeMessage: '',
                dialNumber: null,
                maxParticipants: 0,
                logoutUrl: '',
                record: false,
                duration: 0,
                isBreakout: false,
                moderatorOnlyMessage: '',
                autoStartRecording: false,
                allowStartStopRecording: true,
                bannerText: '',
                bannerColor: '#F2C511',
                logo: null,
                copyright: '',
                muteOnStart: false,
                allowModsToUnmuteUsers: false,
                lockSettingsDisableCam: false,
                lockSettingsDisableMic: false,
                lockSettingsDisablePrivateChat: false,
                lockSettingsDisablePublicChat: false,
                lockSettingsDisableNote: false,
                lockSettingsLockedLayout: false,
                lockSettingsLockOnJoin: false,
                lockSettingsLockOnJoinConfigurable: false,
                guestPolicy: 'ALWAYS_ACCEPT',
                userName: '',
                meetingKeepEvents: false,
                endWhenNoModerator: false,
                endWhenNoModeratorDelayInMinutes: 1,
                meetingLayout: 'SMART_LAYOUT',
                learningDashboardCleanupDelayInMinutes: 2,
                allowModsToEjectCameras: false,
                allowRequestsWithoutSession: false,
                userCameraCap: 3,
                allJoinAsModerator: false,
                medium_id: null,
                webcamsOnlyForModerator: false,
                anyoneCanStart: false,
                server: 'server1',
            }),
            askModerator: true,
            servers: {},
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink link table lists autoresize",
                ],
                {
                    'callback': 'insertContent',
                    'callbackId': this.component_id,
                }
            ),
            showExtendedSettings: false,
            guestPolicyConstants: {
                0: {
                    id: 'ALWAYS_ACCEPT',
                    text: window.trans.global.videoconference.ALWAYS_ACCEPT,
                },
                1: {
                    id: 'ALWAYS_DENY',
                    text: window.trans.global.videoconference.ALWAYS_DENY,
                },
                2: {
                    id: 'ASK_MODERATOR',
                    text: window.trans.global.videoconference.ASK_MODERATOR,
                },
            },
            meetingLayoutConstants: {
                0: {
                    id: 'SMART_LAYOUT',
                    text: window.trans.global.videoconference.SMART_LAYOUT,
                },
                1: {
                    id: 'CUSTOM_LAYOUT',
                    text: window.trans.global.videoconference.CUSTOM_LAYOUT,
                },
                2: {
                    id: 'PRESENTATION_FOCUS',
                    text: window.trans.global.videoconference.PRESENTATION_FOCUS,
                },
                3: {
                    id: 'VIDEO_FOCUS',
                    text: window.trans.global.videoconference.VIDEO_FOCUS,
                },
            },
        }
    },
    computed: {
        textColor: function() {
            return this.$textcolor(this.form.color, '#333333');
        },
    },
    methods: {
        submit() {
            if (this.showExtendedSettings) {
                this.form.welcomeMessage = tinyMCE.get('welcomeMessage').getContent();
                this.form.moderatorOnlyMessage = tinyMCE.get('moderatorOnlyMessage').getContent();
            }

            if (this.askModerator) {
                this.form.guestPolicy = 'ASK_MODERATOR';
            } else {
                this.form.guestPolicy = 'ALWAYS_ACCEPT';
            }
            if (this.method === 'patch') {
                this.update();
            } else {
                this.add();
            }

            this.globalStore.closeModal(this.$options.name);
        },
        add() {
            axios.post('/videoconferences', this.form)
                .then(r => {
                    this.$eventHub.emit('videoconference-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch('/videoconferences/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('videoconference-updated', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show && !state.modals[this.$options.name].lock) {
                this.globalStore.lockModal(this.$options.name);
                const params = state.modals[this.$options.name].params;

                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                    this.form.guestPolicy = params.guestPolicy === 'ASK_MODERATOR';
                    this.askModerator = (params.guestPolicy === 'ASK_MODERATOR') ? true : false;

                    if (this.form.id !== '') {
                        this.form.welcomeMessage = this.$decodeHtml(this.form.welcomeMessage);
                        this.form.moderatorOnlyMessage = this.$decodeHtml(this.form.moderatorOnlyMessage);
                        this.method = 'patch';
                    } else {
                        this.method = 'post';
                    }
                }
            }
        });

        axios.get('/videoconferences/servers')
            .then(response => {
                this.servers = response.data;
            })
            .catch(e => {
                console.log(e);
            });
    },
}
</script>