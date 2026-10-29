<template>
    <div class="d-flex flex-column px-3">
        <div class="px-3 py-2 bg-white rounded-3 shadow-layout">
            <div class="d-flex align-items-center gap-2">
                <span class="fs-4">{{ videoconference.meetingName }}</span>
                <span v-if="ownerOrAdmin"
                    class="d-contents"
                >
                    <button
                        type="button"
                        class="btn btn-icon text-secondary"
                        data-bs-toggle="tooltip"
                        :data-bs-title="trans('global.videoconference.edit')"
                        @click="editVideoconference()"
                    >
                        <i class="fa fa-pencil-alt"></i>
                    </button>
                    <button
                        type="button"
                        class="btn btn-icon text-secondary"
                        data-bs-toggle="tooltip"
                        :data-bs-title="trans('global.videoconference.share')"
                        @click="share()"
                    >
                        <i class="fa fa-share-alt"></i>
                    </button>
                </span>
            </div>

            <hr class="mt-1">

            <div v-if="loading"
                class="text-center"
            >
                <i class="fas fa-2x fa-spinner fa-spin"></i>
                <p> {{ loadingMessage }} {{ timerCount }}</p>
                <button
                    type="button"
                    class="btn btn-primary pt-2"
                    @click="toggleTimer()"
                >
                    {{ trans('global.cancel') }}
                </button>
            </div>
            <div v-else
                class="d-flex flex-column gap-3"
            >
                <div class="d-flex flex-column flex-md-row">
                    <div class="col mb-2 mb-md-0">{{ videoconference.owner.firstname }} {{ videoconference.owner.lastname }} (Initiator)</div>
                    <div class="col input-group">
                        <input
                            type="text"
                            id="videoconference-username"
                            class="form-control"
                            maxlength="191"
                            v-model.trim="form.userName"
                            :disabled="lockUserName"
                            :placeholder="trans('global.videoconference.enter_name')"
                            @keyup.enter="form.userName && startVideoconference()"
                        />
                        <button v-if="form.userName"
                            type="button"
                            class="btn"
                            :class="isRunning ? 'btn-info' : 'btn-primary'"
                            @click="startVideoconference()"
                        >
                            {{ (isRunning ? trans('global.videoconference.join') : trans('global.videoconference.start')) }}
                        </button>
                    </div>
                </div>

                <div v-if="ownerOrAdmin
                        || videoconference.moderatorPW == urlParamModeratorPW
                    "
                    class="d-flex flex-column flex-sm-row justify-content-center justify-content-md-end gap-3"
                >
                    <button
                        type="button"
                        class="btn btn-light"
                        @click="copyToClipboard('attendee')"
                    >
                        <i class="fa fa-copy"></i>
                        {{ trans('global.videoconference.participant_link') }}
                    </button>
                    <button
                        type="button"
                        class="btn btn-light"
                        @click="copyToClipboard('moderator')"
                    >
                        <i class="fa fa-copy"></i>
                        {{ trans('global.videoconference.moderator_link') }}
                    </button>
                </div>
            </div>
        </div>

        <div v-if="ownerOrAdmin">
            <h5 class="pt-4">{{ trans('global.videoconference.presentations') }}</h5>
            <hr>
            <Media
                ref="videoconferenceMedia"
                :subscribable_id="videoconference.id"
                subscribable_type="App\Videoconference"
                format="list"
            />
        </div>

        <Teleport to="body">
            <MediumModal/>
            <SubscribeModal/>
            <VideoconferenceModal/>
        </Teleport>
    </div>
</template>
<script>
import Form from "form-backend-validation";
import VideoconferenceModal from "../videoconference/VideoconferenceModal.vue";
import Media from "../media/Media.vue";
import MediumModal from "../media/MediumModal.vue";
import SubscribeModal from "../subscription/SubscribeModal.vue";

export default {
    props: {
        videoconference: {
            type: Object,
            default: null,
        },
        user: {
            type: Object,
            default: null,
        },
        editor: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                userName: '',
            }),
            lockUserName: false,
            loading: false,
            loadingMessage: 'Lade Konferenz',
            isRunning: false,
            timerEnabled: false,
            timerCount: 10,
            urlParamModeratorPW: '',
            urlParamAttendeePW: '',
        }
    },
    mounted() {
        this.enableTooltips();

        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        this.urlParamModeratorPW = urlParams.get('moderatorPW')
        this.urlParamAttendeePW = urlParams.get('attendeePW')

        // logged in users shouldn't be able to change their username (owner and admins excluded)
        if (this.user != null && this.user.firstname != 'Guest') {
            this.form.userName = this.user.firstname + ' ' + this.user.lastname;

            if (!this.ownerOrAdmin) {
                this.form.setInitialValues({
                    userName: this.user.firstname + ' ' + this.user.lastname,
                });
                this.lockUserName = true;
            }
        }

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

        this.$eventHub.on('videoconference-updated', videoconference => {
            window.location.reload();
        });
    },
    methods: {
        copyToClipboard(role) {
            navigator.clipboard.writeText(window.location.origin + '/videoconferences/' + this.videoconference.id + '/startWithPw?' + role + 'PW=' + this.videoconference[role + 'PW']);
            this.toast.success(window.trans.global.token_copied, { timeout: 3000 })
        },
        editVideoconference() {
            this.globalStore.showModal('videoconference-modal', this.videoconference);
        },
        share() {
            this.globalStore.showModal('subscribe-modal', {
                modelId: this.videoconference.id,
                modelUrl: 'videoconference',
                shareWithUsers: true,
                shareWithGroups: true,
                shareWithOrganizations: true,
                shareWithToken: true,
                canEditCheckbox: true
            });
        },
        toggleTimer() {
            this.loading = false;
            this.timerEnabled = false;
            this.loadingMessage = 'Laden abgebrochen. Fenster neu laden um Verbindungsaufbau neu zu starten.'
        },
        startVideoconference() {
            this.loading = !this.loading;
            this.timerCount = 10;
            this.timerEnabled = true;

            const userName = this.lockUserName
                ? this.form.initial.userName
                : this.form.userName;

            if ( // owner or moderator
                this.videoconference.owner_id == this.$userId ||
                this.urlParamModeratorPW === this.videoconference.moderatorPW ||
                this.videoconference.anyoneCanStart === true ||
                this.videoconference.editable === true
            ) {
                // has permission to start
                window.location = '/videoconferences/' + this.videoconference.id + 
                    '/start?userName=' + userName + 
                    '&moderatorPW=' + this.urlParamModeratorPW + 
                    '&attendeePW=' + this.urlParamAttendeePW;
            } else { // join as attendee
                axios.get('/videoconferences/' + this.videoconference.id + '/getStatus')
                    .then(response => {
                        if (response.data.videoconference == false) {
                            this.loadingMessage = 'Konferenz ist noch nicht gestartet. Neuer Verbindungsversuch in ';
                        } else {
                            this.timerEnabled = false;
                            // send token to verify access
                            const token = new URLSearchParams(window.location.search).get('sharing_token');
                            window.location = '/videoconferences/' + this.videoconference.id +
                                '/start?userName=' + userName +
                                '&moderatorPW=&attendeePW=' + this.urlParamAttendeePW +
                                '&sharing_token=' + token;
                        }
                    })
                    .catch(e => {
                        console.log(e);
                    });
            }
        },
    },
    computed: {
        ownerOrAdmin() {
            return this.videoconference.owner_id == this.$userId || this.checkPermission('is_admin');
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
        s: {
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
        },
    },
    components: {
        Media,
        MediumModal,
        SubscribeModal,
        VideoconferenceModal,
    },
}
</script>