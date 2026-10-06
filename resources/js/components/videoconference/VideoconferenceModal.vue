<template>
    <Modal
        ref="modal"
        model="videoconference"
        modalName="videoconference-modal"
        :form="form"
        :allow-overflow="!showExtendedSettings"
        :require-title="true"
        :show-general-header="showExtendedSettings"
        :show-owner-field="form.id && isAdmin"
        :show-display-section="true"
        :show-medium-field="true"
        :show-permission-section="true"
        :intercept-save="true"
        @opened="params => preProcessing(params)"
        @save="postProcessing()"
    >
        <template v-if="showExtendedSettings" #general-extended>
            <Select2 v-if="servers.length"
                id="videoconference-server"
                css="mt-3"
                :list="servers"
                label="Server"
                model="videoconference"
                option_label="BBB_SERVER_NAME"
                :selected="form.server"
                placeholder="Server"
                @selectedValue="id => form.server = id[0]"
            />

            <div class="mt-3">
                <label for="videoconference-welcome-message" class="form-label">
                    {{ trans('global.videoconference.fields.welcomeMessage') }}
                </label>
                <Editor
                    id="videoconference-welcome-message"
                    licenseKey="gpl"
                    :init="tinyMCE"
                    v-model="form.welcomeMessage"
                />
            </div>

            <div class="mt-3">
                <label for="videoconference-moderator-message" class="form-label">
                    {{ trans('global.videoconference.fields.moderatorOnlyMessage') }}
                </label>
                <Editor
                    id="videoconference-moderator-message"
                    licenseKey="gpl"
                    :init="tinyMCE"
                    v-model="form.moderatorOnlyMessage"
                />
            </div>

            <div class="mt-3">
                <label for="videoconference-max-participants" class="form-label">
                    {{ trans('global.videoconference.fields.maxParticipants') }}
                </label>
                <input
                    type="number"
                    id="videoconference-max-participants"
                    class="form-control"
                    v-model="form.maxParticipants"
                />
                <small class="help-block">{{ trans('global.videoconference.fields.maxParticipants_helper') }}</small>
            </div>

            <div class="mt-3">
                <label for="videoconference-duration" class="form-label">
                    {{ trans('global.videoconference.fields.duration') }}
                </label>
                <input
                    type="number"
                    id="videoconference-duration"
                    class="form-control"
                    v-model="form.duration"
                />
                <small class="help-block">{{ trans('global.videoconference.fields.duration_helper') }}</small>
            </div>

            <div class="mt-3">
                <label for="videoconference-logout-url" class="form-label">
                    {{ trans('global.videoconference.fields.logoutUrl') }}
                </label>
                <input
                    type="text"
                    id="videoconference-logout-url"
                    class="form-control"
                    maxlength="191"
                    v-model.trim="form.logoutUrl"
                />
                <p class="help-block">{{ trans('global.videoconference.fields.logoutUrl_helper') }}</p>
            </div>

            <Switch
                id="videoconference-end-when-no-moderator"
                label="global.videoconference.fields.endWhenNoModerator"
                v-model="form.endWhenNoModerator"
            />

            <div v-if="form.endWhenNoModerator">
                <label for="videoconference-end-when-no-moderator-delay-in-minutes" class="form-label">
                    {{ trans('global.videoconference.fields.endWhenNoModeratorDelayInMinutes') }}
                </label>
                <input
                    type="number"
                    id="videoconference-end-when-no-moderator-delay-in-minutes"
                    class="form-control"
                    min="0"
                    v-model="form.endWhenNoModeratorDelayInMinutes"
                />
            </div>
        </template>

        <template #permissions>
            <Switch
                id="videoconference-mute-on-start"
                label="global.videoconference.fields.muteOnStart"
                v-model="form.muteOnStart"
            />

            <Switch
                id="videoconference-ask-moderator"
                label="global.videoconference.ASK_MODERATOR"
                v-model="form.askModerator"
            />

            <Switch
                id="videoconference-anyone-can-start"
                label="global.videoconference.fields.anyoneCanStart"
                v-model="form.anyoneCanStart"
            />

            <Switch
                id="videoconference-all-join-as-moderator"
                label="global.videoconference.fields.allJoinAsModerator"
                v-model="form.allJoinAsModerator"
            />
        </template>

        <template v-if="showExtendedSettings" #custom>
            <div class="accordion-item">
                <div class="accordion-header">
                    <span
                        class="accordion-button"
                        data-bs-toggle="collapse"
                        data-bs-target="#videoconference-audio-video-settings"
                        aria-expanded="true"
                        aria-controls="videoconference-audio-video-settings"
                    >
                        {{ trans('global.videoconference.audio_video_settings') }}
                    </span>
                </div>
                <div
                    id="videoconference-audio-video-settings"
                    class="accordion-collapse collapse show"
                >
                    <div>
                        <Switch
                            id="videoconference-lock-settings-disable-cam"
                            label="global.videoconference.fields.lockSettingsDisableCam"
                            v-model="form.lockSettingsDisableCam"
                        />
    
                        <Switch
                            id="videoconference-lock-settings-disable-mic"
                            label="global.videoconference.fields.lockSettingsDisableMic"
                            v-model="form.lockSettingsDisableMic"
                        />
    
                        <Switch
                            id="videoconference-allow-mods-to-eject-cameras"
                            label="global.videoconference.fields.allowModsToEjectCameras"
                            v-model="form.allowModsToEjectCameras"
                        />
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <span
                        class="accordion-button"
                        data-bs-toggle="collapse"
                        data-bs-target="#videoconference-collaboration-settings"
                        aria-expanded="true"
                        aria-controls="videoconference-collaboration-settings"
                    >
                        {{ trans('global.videoconference.collaboration_settings') }}
                    </span>
                </div>
                <div
                    id="videoconference-collaboration-settings"
                    class="accordion-collapse collapse show"
                >
                    <div>
                        <Switch
                            id="videoconference-lock-settings-disable-private-chat"
                            label="global.videoconference.fields.lockSettingsDisablePrivateChat"
                            v-model="form.lockSettingsDisablePrivateChat"
                        />

                        <Switch
                            id="videoconference-lock-settings-disable-public-chat"
                            label="global.videoconference.fields.lockSettingsDisablePublicChat"
                            v-model="form.lockSettingsDisablePublicChat"
                        />

                        <Switch
                            id="videoconference-lock-settings-disable-note"
                            label="global.videoconference.fields.lockSettingsDisableNote"
                            v-model="form.lockSettingsDisableNote"
                        />
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <span
                        class="accordion-button"
                        data-bs-toggle="collapse"
                        data-bs-target="#videoconference-layout-settings"
                        aria-expanded="true"
                        aria-controls="videoconference-layout-settings"
                    >
                        {{ trans('global.videoconference.layout_settings') }}
                    </span>
                </div>
                <div
                    id="videoconference-layout-settings"
                    class="accordion-collapse collapse show"
                >
                    <div>
                        <Select2
                            id="videoconference-meeting-layout"
                            :list="meetingLayoutConstants"
                            model="videoconference"
                            :showLabel="false"
                            option_label="text"
                            :selected="form.meetingLayout"
                            @selectedValue="id => form.meetingLayout = id"
                        />

                        <Switch
                            id="videoconference-lock-settings-locked-layout"
                            label="global.videoconference.fields.lockSettingsLockedLayout"
                            v-model="form.lockSettingsLockedLayout"
                        />
    
                        <div class="mt-3">
                            <label for="videoconference-banner-text" class="form-label">
                                {{ trans('global.videoconference.fields.bannerText') }}
                            </label>
                            <input
                                type="text"
                                id="videoconference-banner-text"
                                class="form-control"
                                v-model.trim="form.bannerText"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <span
                        class="accordion-button"
                        data-bs-toggle="collapse"
                        data-bs-target="#videoconference-record-settings"
                        aria-expanded="true"
                        aria-controls="videoconference-record-settings"
                    >
                        {{ trans('global.videoconference.record_settings') }}
                    </span>
                </div>
                <div
                    id="videoconference-record-settings"
                    class="accordion-collapse collapse show"
                >
                    <div>
                        <Switch
                            id="videoconference-record"
                            label="global.videoconference.fields.record"
                            v-model="form.record"
                        />

                        <Switch v-if="form.record"
                            id="videoconference-auto-start-recording"
                            label="global.videoconference.fields.autoStartRecording"
                            v-model="form.autoStartRecording"
                        />
                    </div>
                </div>
            </div>
        </template>

        <template v-if="isAdmin" #footer-left>
            <Switch
                id="videoconference-extended-settings"
                class="me-auto"
                label="global.extendedSettings"
                v-model="showExtendedSettings"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Editor from "@tinymce/tinymce-vue";
import Select2 from "../forms/Select2.vue";
import Switch from '../forms/Switch.vue';

export default {
    name: 'videoconference-modal',
    components: {
        Modal,
        Editor,
        Select2,
        Switch,
    },
    props: {
        params: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                meetingID: null,
                meetingName: '',
                title: '',
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
                color: '#F2C511',
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
                    "autolink", "link", "table", "lists", "autoresize",
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
    methods: {
        preProcessing(params) {
            this.form.title = params.meetingName;
            this.form.color = params.bannerColor ?? this.form.bannerColor;
            this.askModerator = params.guestPolicy === 'ASK_MODERATOR';

            if (this.form.id) {
                this.form.welcomeMessage = this.$decodeHtml(this.form.welcomeMessage);
                this.form.moderatorOnlyMessage = this.$decodeHtml(this.form.moderatorOnlyMessage);
            }

            // get server-list only for admins, since its only visible under extended settings
            if (this.isAdmin) {
                axios.get('/videoconferences/servers')
                    .then(response => this.servers = response.data)
                    .catch(e => console.log(e));
            }
        },
        postProcessing() {
            this.form.meetingName = this.form.title;
            this.form.bannerColor = this.form.color;
            this.form.guestPolicy = this.askModerator ? 'ASK_MODERATOR' : 'ALWAYS_ACCEPT';

            if (this.form.id) this.$refs.modal.update();
            else this.$refs.modal.add();
        },
    },
    computed: {
        isAdmin() {
            return this.checkPermission('is_admin');
        },
    },
}
</script>