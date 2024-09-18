<template >
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-body">
                    <h5>
                        {{ videoconference.meetingName }}
                        <span v-if="videoconference.owner_id == this.$userId"
                              class="pointer">
                            <i class="fa fa-share-alt text-secondary pl-2"
                               @click="share()"
                            ></i>
                            <a class="pointer"
                                @click="editVideoconference(videoconference)">
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
        <div v-permission="'is_admin'"
             class="col-12">
            <div v-if="videoconference.owner_id == this.$userId"
                 class="col-12">
                <h5 class="pt-4">Präsentationen</h5>
                <hr class="bg-gray mt-0">
                <media
                    ref="videoconferenceMedia"
                    :subscribable_type="'App\\\Videoconference'"
                    :subscribable_id="videoconference.id"
                    format="list">
                </media>
            </div>
        </div>
        <Teleport to="body">
            <VideoconferenceModal></VideoconferenceModal>
            <MediumModal
                :show="this.mediumStore.getShowMediumModal"
                @close="() =>{
                     this.mediumStore.setShowMediumModal(false);
                }"
            ></MediumModal>
            <SubscribeModal></SubscribeModal>
        </Teleport>

    </div>
</template>

<script>
import Form from "form-backend-validation";
import MediumForm from "../media/MediumForm.vue";
import VideoconferenceModal from "../videoconference/VideoconferenceModal.vue";
import {useToast} from "vue-toastification";
import Media from "../media/Media.vue";
import MediumModal from "../media/MediumModal.vue";
import {useMediumStore} from "../../store/media";
import SubscribeModal from "../subscription/SubscribeModal.vue";
import {useGlobalStore} from "../../store/global";


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
    setup () { //https://pinia.vuejs.org/core-concepts/getters.html#passing-arguments-to-getters
        const toast = useToast();
        const mediumStore = useMediumStore();
        const globalStore = useGlobalStore();
        return {
            toast,
            mediumStore,
            globalStore
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            url: '/videoconferences',
            form: new Form({
                'userName': '',
            }),
            loading: false,
            loadingMessage: 'Lade Konferenz',
            isRunning: false,
            timerEnabled: false,
            timerCount: 10,
            urlParamModeratorPW: '',
            urlParamAttendeePW: '',
            currentVideoconference: {},
            servers:{},
        }
    },
    mounted() {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        this.urlParamModeratorPW = urlParams.get('moderatorPW')
        this.urlParamAttendeePW = urlParams.get('attendeePW')

        if (this.user != null && this.user.firstname != 'Guest'){
            this.form.userName = this.user.firstname + ' ' + this.user.lastname;
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
        editVideoconference(videoconference){
            this.globalStore?.showModal('videoconference-modal', videoconference);
        },
        share(){
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

                window.location = this.url + '/' + this.videoconference.id + '/start?userName=' + this.form.userName + '&moderatorPW=' + this.urlParamModeratorPW + '&attendeePW=' + this.urlParamAttendeePW;
            } else {
                axios.get(this.url + '/'  + this.videoconference.id + '/getStatus')
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
        }
    },

    components: {
        Media,
        MediumForm,
        MediumModal,
        SubscribeModal,
        VideoconferenceModal,
    },
}
</script>
