<template >
    <div class="row ">
        <div v-if="editor"
             class="col-12 pt-2">
            <div class="card">
                <div class="card-body">
                    <h5>{{ trans('global.videoconference.edit') }}
                        <i v-if="videoconference != null"
                            class="fa fa-share-alt text-secondary pull-right"
                           @click="showModal()"
                        ></i>
                    </h5>
                    <hr class="bg-gray mt-0">
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
                        <p class="help-block" v-if="form.errors?.meetingName" v-text="form.errors?.meetingName[0]"></p>
                    </div> <!-- meetingName -->
                    <div v-if="videoconference == null"
                         class="form-group ">
                        <select
                            id="server"
                            v-model="form.server"
                            class="form-control select2"
                            style="width:100%;">
                            <option v-for="(value,index) in servers"
                                    :id="value.server"
                                    :value="value.server">
                                {{ value.BBB_SERVER_NAME }}
                            </option>
                        </select>
                    </div> <!-- meetingLayout -->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group ">
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
                            </div> <!-- welcomeMessage -->
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
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
                            </div> <!-- moderatorOnlyMessage -->
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
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
                                <small class="help-block" >{{ trans('global.videoconference.fields.maxParticipants_helper') }}</small>
                                <p class="help-block" v-if="form.errors?.maxParticipants" v-text="form.errors?.maxParticipants[0]"></p>
                            </div> <!-- maxParticipants -->
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
                            </div> <!-- guestPolicy -->
                            <div class="form-group">
                         <span class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.allJoinAsModerator"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="allJoinAsModerator">
                            <label class="custom-control-label  font-weight-light"
                                   for="allJoinAsModerator" >
                                {{ trans('global.videoconference.fields.allJoinAsModerator') }}
                            </label>
                        </span>
                                <p class="help-block" >{{ trans('global.videoconference.fields.allJoinAsModerator_helper') }}</p>
                            </div> <!-- allJoinAsModerator -->
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
                                <small class="help-block" >{{ trans('global.videoconference.fields.duration_helper') }}</small>
                                <p class="help-block" v-if="form.errors?.duration" v-text="form.errors?.duration[0]"></p>
                            </div> <!-- duration -->
<!--                            <div class="form-group">
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
                            </div> &lt;!&ndash; learningDashboardCleanupDelayInMinutes &ndash;&gt;-->
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group ">
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
                            <div v-if="form.meetingID"
                                class="form-group">
                                <MediumForm :form="form"
                                            :id="component_id"
                                            :medium_id="form.medium_id"
                                            accept="image/*"/>
                            </div>

                            <!-- logoutUrl -->
<!--                            <div class="form-group">
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
                            </div> &lt;!&ndash; logoUrl &ndash;&gt;-->
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
                            </div> <!-- endWhenNoModerator -->
                            <div v-if="form.endWhenNoModerator"
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
                            </div> <!--endWhenNoModeratorDelayInMinutes -->
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <h5 class="pt-4">Audio-/Video-Einstellungen</h5>
                            <hr class="bg-gray mt-0">
                            <div class="form-group">
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
                                <small class="help-block" >{{ trans('global.videoconference.fields.muteOnStart_helper') }}</small>
                            </div> <!-- muteOnStart -->
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
                            </div> <!-- lockSettingsDisableCam -->
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
                            </div> <!-- lockSettingsDisableMic -->
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
                            </div> <!-- allowModsToEjectCameras -->
                        </div>
                        <div class="col-sm-6">
                            <h5 class="pt-4">Kollaborations-Einstellungen</h5>
                            <hr class="bg-gray mt-0">
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
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <h5 class="pt-4">Layout-Einstellungen</h5>
                            <hr class="bg-gray mt-0">
                            <div class="form-group ">
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
                            </div> <!-- meetingLayout -->
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
                            </div> <!-- lockSettingsLockedLayout -->
                        </div>
                        <div class="col-sm-6">
                            <h5 class="pt-4">Aufnahme-Einstellungen</h5>
                            <hr class="bg-gray mt-0">
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
                                <small class="help-block" >{{ trans('global.videoconference.fields.record_helper') }}</small>
                            </div> <!-- record -->
                            <div class="form-group">
                         <span v-if="form.record"
                             class="custom-control custom-switch custom-switch-on-green">
                            <input  v-model="form.autoStartRecording"
                                    type="checkbox"
                                    class="custom-control-input pt-1 "
                                    id="autoStartRecording">
                            <label class="custom-control-label  font-weight-light"
                                   for="autoStartRecording" >
                                {{ trans('global.videoconference.fields.autoStartRecording') }}
                            </label>
                        </span>
                            </div> <!-- autoStartRecording -->
                        </div>
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
                    <!--                    <div class="form-group">
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
                                        </div>-->
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
                    <button :name="'videoconferenceSave'"
                            class="btn btn-primary p-2 m-2"
                            @click="submit()">
                        {{ trans('global.save') }}
                    </button>
                </div>

            </div>
        </div>
        <div v-else class="col-12 pt-2">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-body">
                            <h5>
                                {{ videoconference.meetingName }}
                                <span v-if="videoconference.owner_id == this.$userId">
                                    <i class="fa fa-share-alt text-secondary pl-2"
                                       @click="showModal()"
                                    ></i>
                                    <a
                                    :href="'/videoconferences/' + videoconference.id + '/edit'">
                                        <i class="fa fa-pencil-alt text-secondary pull-right"></i>
                                    </a>

                                </span>

                            </h5>
                            <hr class="bg-gray mt-0">
                            <div v-if="loading"
                                 class=" text-center ">
                                <i class="fas fa-2x fa-spinner fa-spin"></i>
                                <p> {{ loadingMessage }} {{ timerCount }}</p>

                                <button @click="toggleTimer()"
                                        class="btn btn-primary pt-2">
                                    {{ trans('global.cancel') }}
                                </button>
                            </div>
                            <div v-else
                            class="row">
                                <div class="col-6">
                                    {{ videoconference.owner.firstname }} {{ videoconference.owner.lastname }} (Initiator)
                                </div>
                                <div class="col-6 input-group">
                                    <input
                                        type="text"
                                        id="userName"
                                        name="userName"
                                        class="form-control"
                                        v-model.trim="form.userName"
                                        placeholder="Bitte Vor- und Nachnamen eingeben..."
                                    />
                                    <span class="input-group-append"
                                          v-if="form.userName"
                                          @click="startVideoconference()">
                                        <button
                                            v-if="isRunning"
                                            type="button" class="btn btn-info">
                                            {{ trans('global.videoconference.join') }}
                                        </button>
                                        <button
                                            v-else
                                            type="button" class="btn btn-primary">
                                            {{ trans('global.videoconference.start') }}
                                        </button>
                                    </span>
                                </div> <!-- guestName -->

                                <div v-if="videoconference.owner_id == this.$userId || videoconference.moderatorPW == this.urlParamModeratorPW"
                                     class="pt-4 col-12"
                                >
                                    <a @click="copyToClipboard('attendee')"
                                       class="btn btn-light pt-2 pull-right">
                                        <i class="fa fa-copy"></i>
                                        Link für Teilnehmer:innen
                                    </a>
                                    <a @click="copyToClipboard('moderator')"
                                            class="btn btn-light pt-2 mr-2 pull-right">
                                        <i class="fa fa-copy"></i>
                                        Link für Moderator:innen
                                    </a>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
<!--                <div class="col-12">
                    <div v-if="videoconference.owner_id == this.$userId"
                         class="col-12">
                        <h5 class="pt-4">Präsentationen</h5>
                        <hr class="bg-gray mt-0">
                        <VideoconferenceMedia :model="videoconference" ></VideoconferenceMedia>
                    </div>
                </div>-->
            </div>
        </div>
    </div>
</template>

<script>
import Form from "form-backend-validation";
import MediumForm from "../media/MediumForm";
import VideoconferenceMedia from "./VideoconferenceMedia";
const Trainings =
    () => import('../training/Trainings');

export default {
    props: {
        videoconference: {
            default: null
        },
        user: {
            default: null
        },
        editor: {
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
                'anyoneCanStart': true,
                'server': 'server1'
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
            errors: {},
            loading: false,
            loadingMessage: 'Lade Konferenz',
            isRunning: false,
            timerEnabled: false,
            timerCount: 10,
            urlParamModeratorPW: '',
            urlParamAttendeePW: '',
            servers:{}
        }
    },
    mounted() {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        this.urlParamModeratorPW = urlParams.get('moderatorPW')
        this.urlParamAttendeePW = urlParams.get('attendeePW')

        if ( this.videoconference !== null ) {
            this.form = this.videoconference;
            if(this.user != null && this.user.firstname != 'Guest'){
                this.form.userName = this.user.firstname + ' ' + this.user.lastname;
            }
            this.method = 'patch';
        }
        this.$initTinyMCE([
            "autolink link"
        ] );

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

        // Set eventlistener for Media
        this.$eventHub.$on('addMedia', (e) => {
            if (this.component_id == e.id) {
                this.form.medium_id = e.selectedMediumId;
                if ( Array.isArray(this.form.medium_id))  {
                    this.form.medium_id = this.form.medium_id[0]; //Hack to get existing files working.
                }
            }
        });

        axios.get('/videoconferences/servers')
            .then(response => {
                this.servers = response.data;
                $("#server").select2({
                    dropdownParent: $("#server").parent(),
                    allowClear: false
                }).on('select2:select', function (e) {
                    this.form.server = e.params.data.element.id
                }.bind(this))
                    .val(this.servers[this.form.server])
                    .trigger('change');
                })
            .catch(e => {
            console.log(e);
        });

        axios.get('/videoconferences/' + this.videoconference.id + '/getStatus')
            .then(response => {
                if (response.data.videoconference == false) {
                    this.isRunning = false;
                } else {
                    this.isRunning = true;
                }
            })
            .catch(e => {
                console.log(e);
            });

    },
    methods: {
        copyToClipboard(role) {
            if (role === 'moderator') {
                console.log(window.location.origin + '/videoconferences/' + this.videoconference.id + '/startWithPw?moderatorPW=' + this.videoconference.moderatorPW);
                navigator.clipboard.writeText(window.location.origin + '/videoconferences/' + this.videoconference.id + '/startWithPw?moderatorPW=' + this.videoconference.moderatorPW);
            } else if (role === 'attendee') {
                console.log(window.location.origin + '/videoconferences/' + this.videoconference.id + '/startWithPw?attendeePW=' + this.videoconference.attendeePW);
                navigator.clipboard.writeText(window.location.origin + '/videoconferences/' + this.videoconference.id + '/startWithPw?attendeePW=' + this.videoconference.attendeePW);
            }
            this.successNotification(window.trans.global.token_copied);
        },
        successNotification(message) {
            this.$toast.success(message, {
                position: "top-right",
                timeout: 3000,
                closeOnClick: true,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: true,
                draggablePercent: 0.6,
                showCloseButtonOnHover: false,
                hideProgressBar: true,
                closeButton: "button",
                icon: true,
                rtl: false
            });
        },
        showModal() {
            this.$modal.show('subscribe-modal', { 'modelId': this.videoconference.id, 'modelUrl': 'videoconference' , 'shareWithToken': true, 'canEditLabel': 'darf Videoknferenz starten'});
        },
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
        },
        toggleTimer(){
            this.loading = false;
            this.timerEnabled = false;
            this.loadingMessage = 'Laden abgebrochen. Fenster neu laden um Verbindungsaufbau neu zu starten.'
        },
        startVideoconference(){
            this.loading = !this.loading;
            this.timerCount= 10;
            this.timerEnabled = true;

            if (this.videoconference.owner_id == this.$userId ||
                this.videoconference.anyoneCanStart === true ||
                this.videoconference.editable === true ||
                this.urlParamModeratorPW === this.videoconference.moderatorPW ||
                this.urlParamAttendeePW === this.videoconference.attendeePW ){

                window.location = '/videoconferences/' + this.videoconference.id + '/start?userName=' + this.form.userName + '&moderatorPW=' + this.urlParamModeratorPW + '&attendeePW=' + this.urlParamAttendeePW;
            } else {
                axios.get('/videoconferences/' + this.videoconference.id + '/getStatus')
                    .then(response => {
                        if (response.data.videoconference == false) {
                            this.loadingMessage = 'Konferenz ist noch nicht gestartet. Neuer Verbindungsversuch in ';
                        } else {
                            this.timerEnabled = false;
                            window.location = response.data.videoconference + '/start?userName=' + this.form.userName;
                        }
                    })
                    .catch(e => {
                        console.log(e);
                    });
            }
        },

    },
    watch: {
        timerEnabled(value) {
            if (value) {
                setTimeout(() => {
                    this.timerCount--;
                }, 1000);
            }
        },
        timerCount: {
            handler(value) {
                if (value > 0 && this.timerEnabled) {
                    setTimeout(() => {
                        this.timerCount--;
                    }, 1000);
                } else {
                    this.loading = !this.loading;
                    this.startVideoconference();
                }
            },
        }

    },

    components: {
        VideoconferenceMedia,
        MediumForm,
    },
}
</script>
