<template>
    <div class="row">
        <div class="col-sm-12 col-md-7">
            <div
                class="card mb-2 h-100"
                :style="{ 'background': 'url(/media/' + meeting.medium_id + '?model=meeting&model_id=' + meeting.id +') top center no-repeat', 'background-size': 'cover', }"
            >
                <div
                    class="card-img-overlay d-flex flex-column justify-content-end p-0"
                    style="min-height: 230px !important"
                >
                    <span
                        class="p-3"
                        style="background-color: rgba(0,0,0,0.5); "
                        :style="{ backgroundColor: meeting.color + ' !important' }"
                    >
                        <h5 class="card-title text-white">{{ meeting.title }}</h5>
                        <p class="card-text text-white pb-2 pt-1">{{ meeting.subtitle }}</p>
                        <a class="text-white">{{ postDate() }}</a>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-5">
            <Livestream :meeting="meeting"/>
        </div>
    </div>
    <div class="row">
        <!--Details-->
        <div class="col-12 pt-2">
            <div class="card card-primary card-outline card-outline-tabs">
                <div class="card-header p-0 border-bottom-0">
                    <ul
                        id="custom-tabs-four-tab"
                        class="nav nav-tabs"
                        role="tablist"
                    >
                        <li class="nav-item">
                            <a
                                id="custom-tabs-four-home-tab"
                                href="#custom-tabs-four-home"
                                class="nav-link active"
                                data-toggle="pill"
                                role="tab"
                                aria-controls="custom-tabs-four-home"
                                aria-selected="true"
                            >
                                Veranstaltungsbeschreibung
                            </a>
                        </li>
                        <li v-if="meeting.info"
                            class="nav-item"
                        >
                            <a
                                id="custom-tabs-four-profile-tab"
                                href="#custom-tabs-four-profile"
                                class="nav-link"
                                data-toggle="pill"
                                role="tab"
                                aria-controls="custom-tabs-four-profile"
                                aria-selected="false"
                            >
                                Organisatorisches
                            </a>
                        </li>
                        <li
                            v-if="meeting.speakers"
                            class="nav-item"
                        >
                            <a
                                id="custom-tabs-four-messages-tab"
                                href="#custom-tabs-four-messages"
                                class="nav-link"
                                data-toggle="pill"
                                role="tab"
                                aria-controls="custom-tabs-four-messages"
                                aria-selected="false"
                            >
                                Referent:innen
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div
                        id="custom-tabs-four-tabContent"
                        class="tab-content"
                    >
                        <div
                            id="custom-tabs-four-home"
                            class="tab-pane fade show active"
                            role="tabpanel"
                            aria-labelledby="custom-tabs-four-home-tab"
                            v-html="meeting.description"
                        >
                        </div>
                        <div
                            id="custom-tabs-four-profile"
                            class="tab-pane fade"
                            role="tabpanel"
                            aria-labelledby="custom-tabs-four-profile-tab"
                            v-html="meeting.info"
                        >
                        </div>
                        <div
                            id="custom-tabs-four-messages"
                            class="tab-pane fade"
                            role="tabpanel"
                            aria-labelledby="custom-tabs-four-messages-tab"
                            v-html="meeting.speakers"
                        >
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>

        <!--Agenda-->
        <div class="col-12">
            <meeting-dates
                :meeting="this.meeting"
                ref="meetingDates"
            ></meeting-dates>
        </div>

        <!-- Chat-->
<!--        <div class="col-3 pl-0">
                <h6>Veranstaltungschat</h6>
                <div class="direct-chat-messages px-0" style="height: 500px !important;">
                    <div class="direct-chat-msg"><div class="direct-chat-infos clearfix">
                    <span class="direct-chat-name  float-left">
                         User1
                    </span>
                    <span class="direct-chat-timestamp float-right">
                                2020-12-20 17:18:34
                    </span>
                </div>
                <span class="direct-chat-img">
                    <img src="/media/2975" alt="User profile picture" class="direct-chat-img">
                </span>
                <div class="direct-chat-text">&lt;!&ndash;&ndash;&gt;
                    Message 1
                </div>
            </div>
            <div class="direct-chat-msg right">
                <div class="direct-chat-infos clearfix">
                    <span class="direct-chat-name  float-right">
                      User2
                    </span>
                    <span class="direct-chat-timestamp float-left">
                            2020-12-20 17:16:45
                    </span>
                </div>
                <img src="/media/2973" alt="User profile picture" class="direct-chat-img">
                <div class="direct-chat-text">&lt;!&ndash;&ndash;&gt;
                    Message 2
                </div>
            </div>
            <div class="direct-chat-msg right">
                <div class="direct-chat-infos clearfix">
                <span class="direct-chat-name  float-right">
                      User 2
                </span>
                <span class="direct-chat-timestamp float-left">
                      2020-12-20 15:53:53
                </span>
            </div>
            <img src="/media/2973" alt="User profile picture" class="direct-chat-img">
            <div class="direct-chat-text">&lt;!&ndash;&ndash;&gt;
                Message 3
            </div>
            </div>
        </div>
    </div>-->
        <Teleport to="body">
            <MeetingModal></MeetingModal>
            <SubscribeModal></SubscribeModal>
        </Teleport>
        <teleport
            v-if="$userId == meeting.owner_id"
            to="#customTitle">
            <small>{{ trans('global.meeting.title_singular') }} </small>

            <a class="btn btn-flat"
               @click="editMeeting(meeting)"
            >
                <i class="fa fa-pencil-alt text-secondary"></i>
            </a>

            <button
                v-permission="'meeting_create'"
                v-if="$userId == meeting.owner_id"
                class="btn btn-flat"
                @click="share()">
                <i class="fa fa-share-alt text-secondary"></i>
            </button>
        </teleport>
    </div>
</template>

<script>
import MeetingDates from "./MeetingDates.vue";
import Livestream from "./Livestream.vue";
import MeetingModal from "./MeetingModal.vue";
import {useGlobalStore} from "../../store/global";
import SubscribeModal from "../subscription/SubscribeModal.vue";

export default {
    props: {
        'meeting': Object,
    },
    setup () {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data () {
        return {
            search: '',
            showPrintOptions: false
        };
    },

    methods: {
        postDate() {
            var start = new Date(this.meeting.begin.replace(/-/g, "/"));
            var end = new Date(this.meeting.end.replace(/-/g, "/"));
            var dateFormat = {
                weekday: 'short',
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };

            if (start.toDateString() === end.toDateString()) {
                return start.toLocaleString([], dateFormat) + " - " + end.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } else {
                return start.toLocaleString([], dateFormat) + " - " + end.toLocaleString([], dateFormat);
            }
        },
        editMeeting(meeting){
            this.globalStore?.showModal('meeting-modal', meeting);
        },
        share(){
            this.globalStore?.showModal('subscribe-modal', {
                'modelId': this.meeting.id,
                'modelUrl': 'meeting',
                'shareWithUsers': true,
                'shareWithGroups': true,
                'shareWithOrganizations': true,
                'shareWithToken': true,
                'canEditCheckbox': true
            });
        },

    },
    mounted() {

    },
    components: {
        SubscribeModal,
        MeetingModal,
        Livestream,
        MeetingDates
    }

}

</script>
