<template >
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-body">
                    <h5>
                        {{ videoconference.meetingName }}
                        <span v-if="videoconference.owner_id == $userId || checkPermission('is_admin')"
                            class="pointer"
                        >
                            <a class="text-secondary p-1">
                                <i
                                    class="fa fa-share-alt"
                                    @click="share()"
                                ></i>
                            </a>
                            <a
                                class="text-secondary pull-right"
                                @click="editVideoconference(videoconference)"
                            >
                                <i class="fa fa-pencil-alt"></i>
                            </a>
                        </span>
                    </h5>
                    <hr class="bg-gray mt-0">
                    <div v-if="loading"
                        class="text-center"
                    >
                        <i class="fas fa-2x fa-spinner fa-spin"></i>
                        <p> {{ loadingMessage }} {{ timerCount }}</p>
                        <button
                            class="btn btn-primary pt-2"
                            @click="toggleTimer()"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                    </div>
                    <div v-else
                        class="row"
                    >
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
                                :disabled="lockUserName"
                                :placeholder="trans('global.videoconference.enter_name')"
                            />
                            <span v-if="form.userName"
                                class="input-group-append"
                                @click="startVideoconference()"
                            >
                                <button v-if="isRunning"
                                    type="button"
                                    class="btn btn-info"
                                >
                                    {{ trans('global.videoconference.join') }}
                                </button>
                                <button v-else
                                    type="button"
                                    class="btn btn-primary"
                                >
                                    {{ trans('global.videoconference.start') }}
                                </button>
                            </span>
                        </div> <!-- guestName -->

                        <div v-if="videoconference.owner_id == $userId
                            || videoconference.moderatorPW == urlParamModeratorPW
                            || checkPermission('is_admin')
                            "
                            class="d-flex pt-4 w-100"
                        >
                            <a
                                class="btn btn-light pt-2 ml-auto mr-3"
                                @click="copyToClipboard('attendee')"
                            >
                                <i class="fa fa-copy"></i>
                                {{ trans('global.videoconference.participant_link') }}
                            </a>
                            <a
                                class="btn btn-light pt-2"
                                @click="copyToClipboard('moderator')"
                            >
                                <i class="fa fa-copy"></i>
                                {{ trans('global.videoconference.moderator_link') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-permission="'is_admin'"
            class="col-12"
        >
            <div v-if="videoconference.owner_id == $userId || checkPermission('is_admin')"
                class="col-12"
            >
                <h5 class="pt-4">{{ trans('global.videoconference.presentations') }}</h5>
                <hr class="bg-gray mt-0">
                <Media
                    ref="videoconferenceMedia"
                    :subscribable_id="videoconference.id"
                    subscribable_type="App\Videoconference"
                    format="list"
                />
            </div>
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
import {useToast} from "vue-toastification";
import Media from "../media/Media.vue";
import MediumModal from "../media/MediumModal.vue";
import SubscribeModal from "../subscription/SubscribeModal.vue";
import {useGlobalStore} from "../../store/global";

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
    setup() {
        const toast = useToast();
        const globalStore = useGlobalStore();
        return {
            toast,
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            url: '/videoconferences',
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
            currentVideoconference: {},
            servers: {},
        }
    },
    mounted() {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        this.urlParamModeratorPW = urlParams.get('moderatorPW')
        this.urlParamAttendeePW = urlParams.get('attendeePW')

        if (this.user != null && this.user.firstname != 'Guest') {
            this.form.userName = this.user.firstname + ' ' + this.user.lastname;
            // lock name field for non-owners/non-admins
            if (this.videoconference.owner_id !== this.$userId || this.checkPermission('is_admin') === false) {
                this.form.setInitialValues({
                    userName: this.user.firstname + ' ' + this.user.lastname,
                });
                this.lockUserName = true;
            }
        }

        axios.get(this.url + '/' + this.videoconference.id + '/getStatus')
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

        this.$eventHub.on('videoconference-updated', (videoconference) => {
            this.globalStore?.closeModal('videoconference-modal');
            window.location.reload();
        });
    },
    methods: {
        copyToClipboard(role) {
            if (role === 'moderator') {
                navigator.clipboard.writeText(window.location.origin + this.url + '/' + this.videoconference.id + '/startWithPw?moderatorPW=' + this.videoconference.moderatorPW);
            } else if (role === 'attendee') {
                navigator.clipboard.writeText(window.location.origin + this.url + '/' + this.videoconference.id + '/startWithPw?attendeePW=' + this.videoconference.attendeePW);
            }
            this.successNotification(window.trans.global.token_copied);
        },
        successNotification(message) {
            this.toast.success(message, {
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
        editVideoconference(videoconference) {
            this.globalStore?.showModal('videoconference-modal', videoconference);
        },
        share() {
            this.globalStore?.showModal('subscribe-modal', {
                'modelId': this.videoconference.id,
                'modelUrl': 'videoconference',
                'shareWithUsers': true,
                'shareWithGroups': true,
                'shareWithOrganizations': true,
                'shareWithToken': true,
                'canEditCheckbox': true
            });
        },
        toggleTimer() {
            this.loading = false;
            this.timerEnabled = false;
            this.loadingMessage = 'Laden abgebrochen. Fenster neu laden um Verbindungsaufbau neu zu starten.'
        },
        startVideoconference() {
            this.loading = !this.loading;
            this.timerCount= 10;
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