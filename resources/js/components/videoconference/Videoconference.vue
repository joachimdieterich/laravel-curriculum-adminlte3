<template >
    <div class="row ">
        <div class="col-12 pt-2">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="meetingName">
                            {{ trans('global.videoconference.fields.meetingName') }}
                        </label>
                        <input
                            type="text"
                            id="meetingName"
                            name="meetingName"
                            class="form-control"
                            v-model.trim="form.meetingName"
                            :placeholder="trans('global.videoconference.fields.meetingName')"
                        />
                        <p class="help-block" v-if="form.errors?.dialNumber" v-text="form.errors?.dialNumber[0]"></p>
                    </div>
                    <div class="form-group w-50 pull-left pr-1">
                        <label for="welcomeMessage">
                            {{ trans('global.videoconference.fields.welcomeMessage') }}
                        </label>
                        <textarea
                            id="welcomeMessage"
                            name="welcomeMessage"
                            :placeholder="trans('global.videoconference.fields.welcomeMessage')"
                            class="form-control description my-editor "
                            v-model.trim="form.welcomeMessage"
                        ></textarea>
                    </div>
                    <div class="form-group w-50 pull-left">
                        <label for="moderatorOnlyMessage">
                            {{ trans('global.videoconference.fields.moderatorOnlyMessage') }}
                        </label>
                        <textarea
                            id="moderatorOnlyMessage"
                            name="moderatorOnlyMessage"
                            :placeholder="trans('global.videoconference.fields.moderatorOnlyMessage')"
                            class="form-control description my-editor"
                            v-model.trim="form.moderatorOnlyMessage"
                        ></textarea>
                    </div>

                    <div class="form-group  w-50 pull-left pr-1">
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
                        <p class="help-block" >{{ trans('global.videoconference.fields.maxParticipants_helper') }}</p>
                        <p class="help-block" v-if="form.errors?.maxParticipants" v-text="form.errors?.maxParticipants[0]"></p>
                    </div>
                    <div class="form-group  w-50 pull-left">
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
                        <p class="help-block" >{{ trans('global.videoconference.fields.duration_helper') }}</p>
                        <p class="help-block" v-if="form.errors?.duration" v-text="form.errors?.duration[0]"></p>
                    </div>
                    <div class="clearfix form-group ">
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
                        <p class="help-block" >{{ trans('global.videoconference.fields.logoutUrl_helper') }}</p>
                        <p class="help-block" v-if="form.errors?.logoutUrl" v-text="form.errors?.logoutUrl[0]"></p>
                    </div>
                    <div class="form-group">
                         <span class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.record"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="record">
                            <label class="custom-control-label  font-weight-light"
                                   for="record" >
                                {{ trans('global.videoconference.fields.record') }}
                            </label>
                        </span>
                        <p class="help-block" >{{ trans('global.videoconference.fields.record_helper') }}</p>
                    </div>
                    <div class="form-group">
                         <span class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.autoStartRecording"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="autoStartRecording">
                            <label class="custom-control-label  font-weight-light"
                                   for="autoStartRecording" >
                                {{ trans('global.videoconference.fields.autoStartRecording') }}
                            </label>
                        </span>
                    </div>

<!--                    <div class="form-group">
                         <span class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.isBreakout"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="isBreakout">
                            <label class="custom-control-label  font-weight-light"
                                   for="isBreakout" >
                                {{ trans('global.videoconference.fields.isBreakout') }}
                            </label>
                        </span>
                    </div>-->


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

                    <label for="bannerColor">
                        {{ trans('global.videoconference.fields.bannerColor') }}
                    </label>
                    <color-picker-input
                        v-model="form.bannerColor">

                    </color-picker-input>
                    <div class="form-group">
                        <label for="logo">
                            {{ trans('global.videoconference.fields.logo') }}
                        </label>
                        <input
                            type="text"
                            id="logo"
                            name="logo"
                            class="form-control"
                            v-model.trim="form.logo"
                        />
                        <p class="help-block" v-if="form.errors?.logo" v-text="form.errors?.logo[0]"></p>
                    </div>
                    <div class="form-group">
                        <label for="copyright">
                            {{ trans('global.videoconference.fields.copyright') }}
                        </label>
                        <input
                            type="text"
                            id="copyright"
                            name="copyright"
                            class="form-control"
                            v-model.trim="form.copyright"
                        />
                        <p class="help-block" v-if="form.errors?.copyright" v-text="form.errors?.copyright[0]"></p>
                    </div>
                    <div class="form-group">
                        <label for="muteOnStart">
                            {{ trans('global.videoconference.fields.muteOnStart') }}
                        </label>
                         <span class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.muteOnStart"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="muteOnStart">
                            <label class="custom-control-label  font-weight-light"
                                   for="muteOnStart" >
                                {{ trans('global.videoconference.fields.muteOnStart') }}
                            </label>
                        </span>
                        <p class="help-block" >{{ trans('global.videoconference.fields.muteOnStart_helper') }}</p>
                    </div>
<!--                    <div class="form-group">
                         <span class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.allowModsToUnmuteUsers"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="allowModsToUnmuteUsers">
                            <label class="custom-control-label  font-weight-light"
                                   for="allowModsToUnmuteUsers" >
                                {{ trans('global.videoconference.fields.allowModsToUnmuteUsers') }}
                            </label>
                        </span>
                    </div>-->
                    <div class="form-group">
                         <span class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.lockSettingsDisableCam"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="lockSettingsDisableCam">
                            <label class="custom-control-label  font-weight-light"
                                   for="lockSettingsDisableCam" >
                                {{ trans('global.videoconference.fields.lockSettingsDisableCam') }}
                            </label>
                        </span>
                        <p class="help-block" >{{ trans('global.videoconference.fields.lockSettingsDisableCam_helper') }}</p>
                    </div>
                    <div class="form-group">
                         <span class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.lockSettingsDisableMic"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="lockSettingsDisableMic">
                            <label class="custom-control-label  font-weight-light"
                                   for="lockSettingsDisableMic" >
                                {{ trans('global.videoconference.fields.lockSettingsDisableMic') }}
                            </label>
                        </span>
                        <p class="help-block" >{{ trans('global.videoconference.fields.lockSettingsDisableMic_helper') }}</p>
                    </div>
                    <div class="form-group">
                         <span class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.lockSettingsDisablePrivateChat"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="lockSettingsDisablePrivateChat">
                            <label class="custom-control-label  font-weight-light"
                                   for="lockSettingsDisablePrivateChat" >
                                {{ trans('global.videoconference.fields.lockSettingsDisablePrivateChat') }}
                            </label>
                        </span>
                        <p class="help-block" >{{ trans('global.videoconference.fields.lockSettingsDisablePrivateChat_helper') }}</p>
                    </div>
                    <div class="form-group">
                         <span class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.lockSettingsDisablePublicChat"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="lockSettingsDisablePublicChat">
                            <label class="custom-control-label  font-weight-light"
                                   for="lockSettingsDisablePublicChat" >
                                {{ trans('global.videoconference.fields.lockSettingsDisablePublicChat') }}
                            </label>
                        </span>
                        <p class="help-block" >{{ trans('global.videoconference.fields.lockSettingsDisablePublicChat_helper') }}</p>
                    </div>
                    <div class="form-group">
                         <span class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.lockSettingsDisableNote"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="lockSettingsDisableNote">
                            <label class="custom-control-label font-weight-light"
                                   for="lockSettingsDisableNote" >
                                {{ trans('global.videoconference.fields.lockSettingsDisableNote') }}
                            </label>
                        </span>
                        <p class="help-block" >{{ trans('global.videoconference.fields.lockSettingsDisableNote_helper') }}</p>
                    </div>
                    <div class="form-group">
                         <span class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.lockSettingsLockedLayout"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="lockSettingsLockedLayout">
                            <label class="custom-control-label font-weight-light"
                                   for="lockSettingsLockedLayout" >
                                {{ trans('global.videoconference.fields.lockSettingsLockedLayout') }}
                            </label>
                        </span>
                    </div>
<!--                    <div class="form-group">
                         <span class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.lockSettingsLockOnJoin"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="lockSettingsLockOnJoin">
                            <label class="custom-control-label font-weight-light"
                                   for="lockSettingsLockOnJoin" >
                                {{ trans('global.videoconference.fields.lockSettingsLockOnJoin') }}
                            </label>
                        </span>
                    </div>-->
<!--                    <div class="form-group">
                         <span class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.lockSettingsLockOnJoinConfigurable"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="lockSettingsLockOnJoinConfigurable">
                            <label class="custom-control-label font-weight-light"
                                   for="lockSettingsLockOnJoinConfigurable" >
                                {{ trans('global.videoconference.fields.lockSettingsLockOnJoinConfigurable') }}
                            </label>
                        </span>
                    </div>-->
                    <div class="form-group ">
                        <label for="categorie">
                            {{ trans('global.videoconference.fields.guestPolicy') }}
                        </label>
                        <select id="guestPolicy"
                                v-model="guestPolicyConstants[form.guestPolicy]"
                                class="form-control select2"
                                style="width:100%;">
                            <option v-for="(value,index) in guestPolicyConstants"
                                    :id="index"
                                    :value="value">
                                {{ value }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group ">
                        <label for="categorie">
                            {{ trans('global.videoconference.fields.meetingLayout') }}
                        </label>
                        <select
                            id="meetingLayout"
                            v-model="meetingLayoutConstants[form.meetingLayout]"
                            class="form-control select2"
                            style="width:100%;">
                            <option v-for="(value,index) in meetingLayoutConstants"
                                    :id="index"
                                    :value="value">
                                {{ value }}
                            </option>
                        </select>
                    </div>
<!--                    <div class="form-group">
                         <span class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.meetingKeepEvents"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="meetingKeepEvents">
                            <label class="custom-control-label font-weight-light"
                                   for="meetingKeepEvents" >
                                {{ trans('global.videoconference.fields.meetingKeepEvents') }}
                            </label>
                        </span>
                    </div>-->
                    <div class="form-group">
                         <span class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.endWhenNoModerator"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="endWhenNoModerator">
                            <label class="custom-control-label font-weight-light"
                                   for="endWhenNoModerator" >
                                {{ trans('global.videoconference.fields.endWhenNoModerator') }}
                            </label>
                        </span>
                    </div>
                    <div class="form-group">
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

                    <div class="form-group">
                        <label for="categorie">
                            {{ trans('global.videoconference.fields.learningDashboardCleanupDelayInMinutes') }}
                        </label>
                        <input
                            type="number"
                            id="learningDashboardCleanupDelayInMinutes"
                            name="learningDashboardCleanupDelayInMinutes"
                            class="form-control"
                            v-model.trim="form.learningDashboardCleanupDelayInMinutes"
                        />
                        <p class="help-block" v-if="form.errors?.learningDashboardCleanupDelayInMinutes" v-text="form.errors?.learningDashboardCleanupDelayInMinutes[0]"></p>
                    </div>
                    <div class="form-group">
                         <span class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.allowModsToEjectCameras"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="allowModsToEjectCameras">
                            <label class="custom-control-label font-weight-light"
                                   for="allowModsToEjectCameras" >
                                {{ trans('global.videoconference.fields.allowModsToEjectCameras') }}
                            </label>
                        </span>
                    </div>
<!--                    <div class="form-group">
                         <span class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.allowRequestsWithoutSession"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="allowRequestsWithoutSession">
                            <label class="custom-control-label font-weight-light"
                                   for="allowRequestsWithoutSession" >
                                {{ trans('global.videoconference.fields.allowRequestsWithoutSession') }}
                            </label>
                        </span>
                    </div>-->
<!--                    <div class="form-group">
                        <input
                            type="number"
                            id="userCameraCap"
                            name="userCameraCap"
                            class="form-control"
                            v-model.trim="form.userCameraCap"
                        />
                        <p class="help-block" v-if="form.errors?.userCameraCap" v-text="form.errors?.userCameraCap[0]"></p>
                    </div>-->


<!--                    <div class="form-group">
                        <label for="dialNumber">
                            {{ trans('global.videoconference.fields.dialNumber') }}
                        </label>
                        <input
                            type="text"
                            id="dialNumber"
                            name="dialNumber"
                            class="form-control"
                            v-model.trim="form.dialNumber"
                            :placeholder="trans('global.videoconference.fields.dialNumber')"
                        />
                        <p class="help-block" v-if="form.errors?.dialNumber" v-text="form.errors?.dialNumber[0]"></p>
                    </div>-->



                    <button :name="'videoconferenceSave'"
                            class="btn btn-primary p-2 m-2"
                            @click="submit()">
                        {{ trans('global.save') }}
                    </button>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
import Form from "form-backend-validation";

const Trainings =
    () => import('../training/Trainings');

export default {
    props: {
        videoconference: {
            default: null
        },
        create: {
            default: false
        },

    },
    data() {
        return {
            component_id: this._uid,
            method: 'post',
            requestUrl: '/videoconferences',
            form: new Form({
                'meetingID':'',
                'meetingName': '',
                'attendeePW': '',
                'moderatorPW': '',
                'callbackUrl': '',
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
                'bannerColor': '#222F3D',
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
                'meetingKeepEvents': false,
                'endWhenNoModerator': false,
                'endWhenNoModeratorDelayInMinutes': 1,
                'meetingLayout': 'SMART_LAYOUT',
                'learningDashboardCleanupDelayInMinutes': 2,
                'allowModsToEjectCameras': false,
                'allowRequestsWithoutSession': false,
                'userCameraCap': 3,
            }),
            guestPolicyConstants: {
                'ALWAYS_ACCEPT': window.trans.global.videoconference.ALWAYS_ACCEPT,
                'ALWAYS_DENY': window.trans.global.videoconference.ALWAYS_DENY,
                'ASK_MODERATOR': window.trans.global.videoconference.ASK_MODERATOR
            },
            meetingLayoutConstants: {
                'SMART_LAYOUT': window.trans.global.videoconference.SMART_LAYOUT,
                'CUSTOM_LAYOUT': window.trans.global.videoconference.CUSTOM_LAYOUT,
                'PRESENTATION_FOCUS': window.trans.global.videoconference.PRESENTATION_FOCUS,
                'VIDEO_FOCUS': window.trans.global.videoconference.VIDEO_FOCUS
            },
            editor: false,
            errors: {},
        }
    },
    mounted() {

        if ( this.videoconference !== null ) {
            this.form = this.videoconference;
            this.method = 'patch';
        }

        $('#guestPolicy').select2({
            dropdownParent: $('#guestPolicy').parent(),
        }).on("select2:select", function(e){
            this.form.guestPolicy = e.params.data.element.id
        }.bind(this))
            .val(this.guestPolicyConstants[this.form.guestPolicy])
            .trigger('change');

        $('#meetingLayout').select2({
            dropdownParent: $('#meetingLayout').parent(),
        }).on("select2:select", function(e){
            this.form.meetingLayout = e.params.data.element.id
        }.bind(this))
            .val(this.meetingLayoutConstants[this.form.meetingLayout])
            .trigger('change');


        this.$initTinyMCE([
            "autolink link"
        ] );

    },
    methods: {
        /*edit() {
            this.editor = !this.editor ;

            this.$nextTick(() => {
                this.$initTinyMCE( );
            });
        },*/
        destroy(videoconference){
            axios.delete('/videoconferences/'+videoconference.id)
                .then(response => {
                    this.$eventHub.$emit("videoconference_deleted", videoconference);
                })
                .catch(e => {
                    console.log(e);
                });
        },
        submit() {
            let method = this.method.toLowerCase();
            this.form.welcomeMessage = tinyMCE.get('welcomeMessage').getContent();
            this.form.moderatorOnlyMessage = tinyMCE.get('moderatorOnlyMessage').getContent();
            if (method === 'patch') {
                axios.patch(this.requestUrl + '/' + this.form.id, this.form)
                    .then(res => { // Tell the parent component we've updated a task
                        this.$eventHub.$emit("videoconference_updated", res.data.videoconference);
                        window.location.href = "/videoconferences";
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error);
                    });
            } else {
                axios.post(this.requestUrl, this.form)
                    .then(res => { // Tell the parent component we've added a new task and include it
                        this.$eventHub.$emit("videoconference_added", res.data.videoconference);
                        window.location.href = "/videoconferences";
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error);
                    });
            }
            /*this.form.title = '';
            this.form.description = '';*/
            this.editor = false;

        },
    },

    components: {
    },
}
</script>
