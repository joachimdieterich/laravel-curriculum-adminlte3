<template>
    <Transition name="modal">
        <div v-if="this.globalStore.modals['videoconference-modal']?.show"
             class="modal-mask"
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
                            @click="this.globalStore?.closeModal('videoconference-modal')">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                <div class="form-group">
                    <input
                        type="text"
                        id="meetingName"
                        name="meetingName"
                        class="form-control"
                        v-model.trim="form.meetingName"
                        :placeholder="trans('global.videoconference.fields.meetingName')"
                        required
                    />
                    <p class="help-block" v-if="form.errors.meetingName" v-text="form.errors.meetingName[0]"></p>
                </div>
                <span v-if="this.servers.length">
                    <Select2
                        v-if="showExtendedSettings"
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
                        >
                    </Select2>
                </span>


                <div v-if="showExtendedSettings"
                     class="form-group ">
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
                       v-text="form.errors.welcomeMessage[0]">

                    </p>
                </div>
                <div v-if="showExtendedSettings"
                     class="form-group ">
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
                       v-text="form.errors.moderatorOnlyMessage[0]">

                    </p>
                </div>

                <div v-if="showExtendedSettings"
                     class="form-group">
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
                    <small class="help-block" >
                        {{ trans('global.videoconference.fields.maxParticipants_helper') }}
                    </small>
                    <p class="help-block"
                       v-if="form.errors?.maxParticipants"
                       v-text="form.errors?.maxParticipants[0]">
                    </p>
                </div> <!-- maxParticipants -->
                <div v-if="showExtendedSettings"
                     class="form-group">
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
                    <small class="help-block" >
                        {{ trans('global.videoconference.fields.duration_helper') }}
                    </small>
                    <p class="help-block"
                       v-if="form.errors?.duration"
                       v-text="form.errors?.duration[0]"></p>
                </div>
                <div v-if="showExtendedSettings"
                     class="form-group ">
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
                    <p class="help-block" >
                        {{ trans('global.videoconference.fields.logoutUrl_helper') }}
                    </p>
                    <p class="help-block"
                       v-if="form.errors?.logoutUrl"
                       v-text="form.errors?.logoutUrl[0]"></p>
                </div>
                <div v-if="showExtendedSettings"
                     class="form-group">
                     <div class="custom-control custom-switch custom-switch-on-green">
                        <input  v-model="form.endWhenNoModerator"
                                type="checkbox"
                                class="custom-control-input pt-1 "
                                id="endWhenNoModerator">
                        <label class="custom-control-label font-weight-light"
                               for="endWhenNoModerator" >
                            {{ trans('global.videoconference.fields.endWhenNoModerator') }}
                        </label>
                    </div>
                </div> <!-- endWhenNoModerator -->
                <div v-if="form.endWhenNoModerator && this.showExtendedSettings == true"
                     class="form-group">
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

                <div class="card-header border-bottom"
                     data-card-widget="collapse">
                    <h5 class="card-title">
                        Darstellung
                    </h5>
                </div>
                <!-- /.card-header -->
                <div class="card-body pb-0">
                    <v-swatches
                        :swatch-size="49"
                        :trigger-style="{}"
                        popover-to="right"
                        v-model="this.form.bannerColor"
                        @input="(id) => {
                            this.form.bannerColor = id;
                        }"
                        :max-height="300"
                    ></v-swatches>

                    <MediumForm
                        class="pull-right"
                        id="medium_id"
                        :medium_id="form.medium_id"
                        accept="image/*"
                        :selected="this.form.medium_id"
                        @selectedValue="(id) => {
                            this.form.medium_id = id;
                        }"
                    >
                    </MediumForm>
                </div>

                <div class="card-header border-bottom"
                     data-card-widget="collapse">
                    <h5 class="card-title">
                        Berechtigung
                    </h5>
                </div>
                <div class="card-body pb-0">
                    <div class="form-group">
                        <div class="custom-control custom-switch custom-switch-on-green">
                            <input
                                v-model="form.muteOnStart"
                                type="checkbox"
                                class="custom-control-input pt-1 "
                                :id="'muteOnStart_'+ form.id">
                            <label class="custom-control-label font-weight-light"
                                   :for="'muteOnStart_'+ form.id">
                                {{ trans('global.videoconference.fields.muteOnStart') }}
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch custom-switch-on-green">
                            <input
                                v-model="askModerator"
                                type="checkbox"
                                class="custom-control-input pt-1 "
                                :id="'askModerator_'+ form.id">
                            <label class="custom-control-label font-weight-light"
                                   :for="'askModerator_'+ form.id">
                                {{ trans('global.videoconference.ASK_MODERATOR') }}
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch custom-switch-on-green">
                            <input
                                v-model="form.anyoneCanStart"
                                type="checkbox"
                                class="custom-control-input"
                                :id="'anyoneCanStart_'+ form.id">
                            <label class="custom-control-label font-weight-light"
                                   :for="'anyoneCanStart_'+ form.id">
                                {{ trans('global.videoconference.fields.anyoneCanStart') }}
                            </label>
                        </div>
                    </div>
                    <Select2
                        v-if="showExtendedSettings"
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
                    >
                    </Select2>
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
                </div>
                <div v-if="showExtendedSettings"
                     class="card-header border-bottom"
                     data-card-widget="collapse">
                    <h5 class="card-title">
                        Audio-/Video-Einstellungen
                    </h5>
                </div>
                <div v-if="showExtendedSettings"
                     class="card-body pb-0">
                    <div class="form-group">
                         <div class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.lockSettingsDisableCam"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="lockSettingsDisableCam">
                            <label class="custom-control-label  font-weight-light"
                                   for="lockSettingsDisableCam" >
                                {{ trans('global.videoconference.fields.lockSettingsDisableCam') }}
                            </label>
                        </div>
                        <p class="help-block" >{{ trans('global.videoconference.fields.lockSettingsDisableCam_helper') }}</p>
                    </div> <!-- lockSettingsDisableCam -->
                    <div class="form-group">
                         <div class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.lockSettingsDisableMic"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="lockSettingsDisableMic">
                            <label class="custom-control-label  font-weight-light"
                                   for="lockSettingsDisableMic" >
                                {{ trans('global.videoconference.fields.lockSettingsDisableMic') }}
                            </label>
                        </div>
                        <p class="help-block" >{{ trans('global.videoconference.fields.lockSettingsDisableMic_helper') }}</p>
                    </div> <!-- lockSettingsDisableMic -->
                    <div class="form-group">
                         <div class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.allowModsToEjectCameras"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="allowModsToEjectCameras">
                            <label class="custom-control-label font-weight-light"
                                   for="allowModsToEjectCameras" >
                                {{ trans('global.videoconference.fields.allowModsToEjectCameras') }}
                            </label>
                        </div>
                    </div>
                </div>

                <div v-if="showExtendedSettings"
                     class="card-header border-bottom"
                     data-card-widget="collapse">
                    <h5 class="card-title">
                        Kollaborations-Einstellungen
                    </h5>
                </div>
                <div v-if="showExtendedSettings"
                     class="card-body pb-0">
                    <div class="form-group">
                         <div class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.lockSettingsDisablePrivateChat"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="lockSettingsDisablePrivateChat">
                            <label class="custom-control-label  font-weight-light"
                                   for="lockSettingsDisablePrivateChat" >
                                {{ trans('global.videoconference.fields.lockSettingsDisablePrivateChat') }}
                            </label>
                        </div>
                        <p class="help-block" >{{ trans('global.videoconference.fields.lockSettingsDisablePrivateChat_helper') }}</p>
                    </div>
                    <div class="form-group">
                         <div class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.lockSettingsDisablePublicChat"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="lockSettingsDisablePublicChat">
                            <label class="custom-control-label  font-weight-light"
                                   for="lockSettingsDisablePublicChat" >
                                {{ trans('global.videoconference.fields.lockSettingsDisablePublicChat') }}
                            </label>
                        </div>
                        <p class="help-block" >{{ trans('global.videoconference.fields.lockSettingsDisablePublicChat_helper') }}</p>
                    </div>
                    <div class="form-group">
                         <div class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.lockSettingsDisableNote"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="lockSettingsDisableNote">
                            <label class="custom-control-label font-weight-light"
                                   for="lockSettingsDisableNote" >
                                {{ trans('global.videoconference.fields.lockSettingsDisableNote') }}
                            </label>
                        </div>
                        <p class="help-block" >{{ trans('global.videoconference.fields.lockSettingsDisableNote_helper') }}</p>
                    </div>
                </div>

                <div v-if="showExtendedSettings"
                    class="card-header border-bottom"
                     data-card-widget="collapse">
                    <h5 class="card-title">
                        Layout-Einstellungen
                    </h5>
                </div>
                <div v-if="showExtendedSettings"
                     class="card-body pb-0">
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
                    >
                    </Select2>
                    <div class="form-group">
                        <div class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.lockSettingsLockedLayout"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="lockSettingsLockedLayout">
                            <label class="custom-control-label font-weight-light"
                                   for="lockSettingsLockedLayout" >
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

                <div v-if="showExtendedSettings"
                     class="card-header border-bottom"
                     data-card-widget="collapse">
                    <h5 class="card-title">
                        Aufnahme-Einstellungen
                    </h5>
                </div>
                <div v-if="showExtendedSettings"
                     class="card-body pb-0">
                    <div class="form-group">
                        <div class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.record"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="record">
                            <label class="custom-control-label  font-weight-light"
                                   for="record" >
                                {{ trans('global.videoconference.fields.record') }}
                            </label>
                        </div>
                        <small class="help-block pb-2" >{{ trans('global.videoconference.fields.record_helper') }}</small>

                        <div v-if="form.record"
                             class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.autoStartRecording"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="autoStartRecording">
                            <label class="custom-control-label  font-weight-light"
                                   for="autoStartRecording" >
                                {{ trans('global.videoconference.fields.autoStartRecording') }}
                            </label>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card-footer">
                <div v-permission="'is_admin'"
                      class="pull-left">
                    <div class="custom-control custom-switch custom-switch-on-green">
                        <input
                            v-model="showExtendedSettings"
                            type="checkbox"
                            class="custom-control-input pt-1 "
                            :id="'extendedSettings_'+ form.id">
                        <label class="custom-control-label font-weight-light"
                               :for="'extendedSettings_'+ form.id">
                            {{ trans('global.extendedSettings')}}
                        </label>
                    </div>
                 </div>
                 <div class="pull-right">
                     <button
                         id="videoconference-cancel"
                         type="button"
                         class="btn btn-default mr-2"
                         @click="this.globalStore?.closeModal('videoconference-modal')">
                         {{ trans('global.cancel') }}
                     </button>
                     <button
                         id="videoconference-save"
                         class="btn btn-primary"
                         @click="submit(method)" >
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
    import MediumModal from "../media/MediumModal.vue";
    import MediumForm from "../media/MediumForm.vue";
    import axios from "axios";
    import Editor from "@tinymce/tinymce-vue";
    import Select2 from "../forms/Select2.vue";
    import {useGlobalStore} from "../../store/global";

    export default {
        components:{
            Editor,
            MediumModal,
            Select2,
            MediumForm
        },
        props: {
            params: {
                type: Object
            },  //{ 'modelId': curriculum.id, 'modelUrl': 'curriculum' , 'shareWithToken': true, 'canEditCheckbox': false}

        },
        setup () {
            const globalStore = useGlobalStore();
            return {
                globalStore,
            }
        },
        data() {
            return {
                component_id: this.$.uid,
                method: 'post',
                url: '/videoconferences',
                form: new Form({
                    'id': '',
                    'meetingID': '',
                    'meetingName': '',
                    'attendeePW': '',
                    'moderatorPW': '',
                    'endCallbackUrl': '',
                    'welcomeMessage': '',
                    'dialNumber': null,
                    'maxParticipants': 0,
                    'logoutUrl': '',
                    'record': false,
                    'duration': 0,
                    'isBreakout': false,
                    'moderatorOnlyMessage': '',
                    'autoStartRecording': false,
                    'allowStartStopRecording': true,
                    'bannerText': '',
                    'bannerColor': '#F2C511',
                    'logo': null,
                    'copyright': '',
                    'muteOnStart': false,
                    'allowModsToUnmuteUsers': false,
                    'lockSettingsDisableCam': false,
                    'lockSettingsDisableMic': false,
                    'lockSettingsDisablePrivateChat': false,
                    'lockSettingsDisablePublicChat': false,
                    'lockSettingsDisableNote': false,
                    'lockSettingsLockedLayout': false,
                    'lockSettingsLockOnJoin': false,
                    'lockSettingsLockOnJoinConfigurable': false,
                    'guestPolicy': 'ALWAYS_ACCEPT',
                    'userName': '',
                    'meetingKeepEvents': false,
                    'endWhenNoModerator': false,
                    'endWhenNoModeratorDelayInMinutes': 1,
                    'meetingLayout': 'SMART_LAYOUT',
                    'learningDashboardCleanupDelayInMinutes': 2,
                    'allowModsToEjectCameras': false,
                    'allowRequestsWithoutSession': false,
                    'userCameraCap': 3,
                    'allJoinAsModerator': false,
                    'medium_id': null,
                    'webcamsOnlyForModerator': false,
                    'anyoneCanStart': false,
                    'server': 'server1'
                }),
                askModerator: true,
                servers: {},

                tinyMCE: this.$initTinyMCE(
                    [
                        "autolink link table lists"
                    ],
                    {
                        'eventHubCallbackFunction': 'insertContent',
                        'eventHubCallbackFunctionParams': this.component_id,
                    }
                ),
                showExtendedSettings: false,
                guestPolicyConstants: {
                    0: {
                        'id': 'ALWAYS_ACCEPT',
                        'text': window.trans.global.videoconference.ALWAYS_ACCEPT,
                    },
                    1: {
                        'id': 'ALWAYS_DENY',
                        'text': window.trans.global.videoconference.ALWAYS_DENY,
                    },
                    2: {
                        'id': 'ASK_MODERATOR',
                        'text': window.trans.global.videoconference.ASK_MODERATOR
                    }
                },
                meetingLayoutConstants: {
                    0: {
                        'id': 'SMART_LAYOUT',
                        'text': window.trans.global.videoconference.SMART_LAYOUT,
                    },
                    1: {
                        'id': 'CUSTOM_LAYOUT',
                        'text': window.trans.global.videoconference.CUSTOM_LAYOUT,
                    },
                    2: {
                        'id': 'PRESENTATION_FOCUS',
                        'text': window.trans.global.videoconference.PRESENTATION_FOCUS,
                    },
                    3: {
                        'id': 'VIDEO_FOCUS',
                        'text': window.trans.global.videoconference.VIDEO_FOCUS
                    },
                }
            }
        },
        computed:{
            textColor: function(){
                return this.$textcolor(this.form.color, '#333333');
            }
        },
        methods: {
             submit(method) {
                 if (this.showExtendedSettings === true){
                     this.form.welcomeMessage = tinyMCE.get('welcomeMessage').getContent();
                     this.form.moderatorOnlyMessage = tinyMCE.get('moderatorOnlyMessage').getContent();
                 }

                 if (this.askModerator == true) {
                     this.form.guestPolicy = 'ASK_MODERATOR';
                 } else {
                     this.form.guestPolicy = 'ALWAYS_ACCEPT';
                 }
                 if (method == 'patch') {
                     this.update();
                 } else {
                     this.add();
                 }
            },
            add(){
                axios.post(this.url, this.form)
                    .then(r => {
                        this.$eventHub.emit('videoconference-added', r.data);
                    })
                    .catch(e => {
                        console.log(e.response);
                    });
            },
            update() {
                axios.patch(this.url + '/' + this.form.id, this.form)
                    .then(r => {
                        this.$eventHub.emit('videoconference-updated', r.data);
                    })
                    .catch(e => {
                        console.log(e.response);
                    });
            },
        },
        mounted() {
            this.globalStore.registerModal('videoconference-modal');
            this.globalStore.$subscribe((mutation, state) => {
                if (mutation.events.key === 'videoconference-modal'){
                    const params = state.modals['videoconference-modal'].params;

                    this.form.reset();
                    if (typeof (params) !== 'undefined'){
                        this.form.populate(params);
                        this.form.guestPolicy = params.guestPolicy == 'ASK_MODERATOR';
                        this.askModerator = (params.guestPolicy == 'ASK_MODERATOR') ? true : false;

                        if (this.form.id != ''){
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
