<template>
    <div class="row">

        <div class="col-md-12 col-lg-6">
            <div class="card mb-2 bg-gradient-dark">
                <img v-if ="this.meeting.medium_id"
                     class="card-img-top"
                     :src="'/media/'+this.meeting.medium_id"
                     alt="image"
                >
                <div class="card-img-overlay d-flex flex-column justify-content-end"
                     style="padding:0 !important;min-height:140px">
                    <span class="p-3" style="background-color: rgba(0,0,0,0.5); ">
                        <h5 class="card-title text-white" v-html="this.meeting.title"></h5>
                        <p class="card-text text-white pb-2 pt-1" v-html="this.meeting.subtitle">
                        </p>
                        <a class="text-white" > {{postDate()}}</a>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-lg-6">
            <Livestream
            :meeting="meeting"/>
        </div>
<!--        Details-->
        <div class="col-12 pt-2">
            <div class="card card-primary card-outline card-outline-tabs">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-tabs-four-home-tab"
                               data-toggle="pill" href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home"
                               aria-selected="true">Veranstaltungsbeschreibung</a>
                        </li>
                        <li v-if="meeting.info"
                            class="nav-item" >
                            <a class="nav-link" id="custom-tabs-four-profile-tab"
                               data-toggle="pill" href="#custom-tabs-four-profile" role="tab"
                               aria-controls="custom-tabs-four-profile" aria-selected="false">Organisatorisches</a>
                        </li>
                        <li
                            v-if="meeting.speakers"
                            class="nav-item">
                            <a class="nav-link" id="custom-tabs-four-messages-tab"
                               data-toggle="pill" href="#custom-tabs-four-messages"
                               role="tab" aria-controls="custom-tabs-four-messages" aria-selected="false">Referent:innen</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-four-tabContent">
                        <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab"
                             v-html="this.meeting.description">
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-four-profile" role="tabpanel" aria-labelledby="custom-tabs-four-profile-tab"
                             v-html="this.meeting.info">
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-four-messages"
                             role="tabpanel" aria-labelledby="custom-tabs-four-messages-tab"
                             v-html="this.meeting.speakers">
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

    </div>
</template>

<script>
import MeetingDates from "./MeetingDates";
import Livestream from "./Livestream";

export default {
    props: {
        'meeting': Object,
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

    },
    mounted() {

    },
    components: {
        Livestream,
        MeetingDates
    }

}

</script>
