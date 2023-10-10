<template>
  <div class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-success">
          <b  v-if="form.meetingID == ''"
              class="modal-title">
            {{ trans('global.videoconference.create') }}
          </b>
            <b  v-else
                class="modal-title">
                {{ trans('global.videoconference.edit') }}
            </b>
          <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-0">
          <div class="card mb-0">
            <div class="card-body pb-0">
              <div class="input-group pb-1">
                  <color-picker-input
                      class="input-group-prepend"
                      v-model="form.bannerColor">
                  </color-picker-input>
                <input
                    type="text"
                    id="meetingName"
                    name="meetingName"
                    class="form-control ml-3"
                    style="height:42px"
                    v-model.trim="form.meetingName"
                    :placeholder="trans('global.videoconference.fields.meetingName')"
                    required
                />
                <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
              </div>
                <form>
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
                </form>


            </div>
            <!-- /.card-body -->
          </div>


        </div>
        <div class="modal-footer justify-content-between">
          <button type="button"
                  class="btn btn-default"
                  data-dismiss="modal">
            {{ trans('global.cancel') }}
          </button>
          <button type="button"
                  class="btn btn-primary"
                  data-dismiss="modal"
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

export default {
    name: 'videoconferenceCreate',
    components: {},
    props: {
        videoconference: {},
        method: ''
    },
    data() {
        return {
            component_id: this._uid,
            requestUrl: '/videoconferences',
            form: new Form({
                'meetingID':'',
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
            }),
            askModerator: true
        };
    },
    watch: {
        videoconference: function(newVal, oldVal) {
            //console.log(newVal);
            this.form.id = newVal.id;
            this.form.meetingID = newVal.meetingID;
            this.form.meetingName = newVal.meetingName;
            this.form.endCallbackUrl = newVal.endCallbackUrl;
            this.form.welcomeMessage = newVal.welcomeMessage;
            this.form.dialNumber = newVal.dialNumber;
            this.form.maxParticipants = newVal.maxParticipants;
            this.form.logoutUrl = newVal.logoutUrl;
            this.form.record = newVal.record;
            this.form.duration = newVal.duration;
            this.form.isBreakout = newVal.isBreakout;
            this.form.moderatorOnlyMessage = newVal.moderatorOnlyMessage;
            this.form.autoStartRecording = newVal.autoStartRecording;
            this.form.allowStartStopRecording = newVal.allowStartStopRecording;
            this.form.bannerText = newVal.bannerText;
            this.form.bannerColor = newVal.bannerColor;
            this.form.logo = newVal.logo;
            this.form.muteOnStart = newVal.muteOnStart;
            this.form.allowModsToUnmuteUsers = newVal.allowModsToUnmuteUsers;
            this.form.lockSettingsDisableCam = newVal.lockSettingsDisableCam;
            this.form.lockSettingsDisableMic = newVal.lockSettingsDisableMic;
            this.form.lockSettingsDisablePrivateChat = newVal.lockSettingsDisablePrivateChat;
            this.form.lockSettingsDisablePublicChat = newVal.lockSettingsDisablePublicChat;
            this.form.lockSettingsDisableNote = newVal.lockSettingsDisableNote;
            this.form.lockSettingsLockedLayout = newVal.lockSettingsLockedLayout;
            this.form.lockSettingsLockOnJoin = newVal.lockSettingsLockOnJoin;
            this.form.lockSettingsLockOnJoinConfigurable = newVal.lockSettingsLockOnJoinConfigurable;
            this.form.guestPolicy = newVal.guestPolicy == 'ASK_MODERATOR';
            this.form.userName = newVal.userName;
            this.form.meetingKeepEvents = newVal.meetingKeepEvents;
            this.form.endWhenNoModerator = newVal.endWhenNoModerator;
            this.form.endWhenNoModeratorDelayInMinutes = newVal.endWhenNoModeratorDelayInMinutes;
            this.form.meetingLayout = newVal.meetingLayout;
            this.form.learningDashboardCleanupDelayInMinutes = newVal.learningDashboardCleanupDelayInMinutes;
            this.form.allowModsToEjectCameras = newVal.allowModsToEjectCameras;
            this.form.allowRequestsWithoutSession = newVal.allowRequestsWithoutSession;
            this.form.userCameraCap = newVal.userCameraCap;
            this.form.allJoinAsModerator = newVal.allJoinAsModerator;
            this.form.medium_id = newVal.medium_id;
            this.form.webcamsOnlyForModerator = newVal.webcamsOnlyForModerator;
            this.form.anyoneCanStart = newVal.anyoneCanStart;
            this.askModerator = (newVal.guestPolicy == 'ASK_MODERATOR') ? true : false;
            this.method= 'patch';
        },
        method: function (newVal, oldVal) {
            if (newVal == 'post') {
                this.form.reset();
            }
        }
    },
    computed:{
        textColor: function(){
            return this.$textcolor(this.form.color, '#333333');
        }
    },
    methods: {
        submit() {
            let method = this.method.toLowerCase();
            if (this.askModerator == true) {
                this.form.guestPolicy = 'ASK_MODERATOR';
            } else {
                this.form.guestPolicy = 'ALWAYS_ACCEPT';
            }

            if (method === 'patch') {
                axios.patch(this.requestUrl + '/' + this.form.id, this.form)
                    .then(res => { // Tell the parent component we've updated a task
                        this.$eventHub.$emit("videoconference-updated", res.data);
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error);
                    });
            } else {
                axios.post(this.requestUrl, this.form)
                    .then(res => {
                        this.$eventHub.$emit("videoconference-added", res.data.videoconference);
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error)
                    });
            }
        },
    },
    mounted() {

    }

}
</script>
