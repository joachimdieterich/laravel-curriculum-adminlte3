<template>
    <div class="row">

        <div class="col-md-12 col-lg-6">
            <div class="card mb-2 bg-gradient-dark">
                <img class="card-img-top" :src="'/media/'+this.meeting.medium_id" alt="Dist Photo 1">

                <div class="card-img-overlay d-flex flex-column justify-content-end"
                     style="padding:0 !important;">
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
            <div class="card mb-2 bg-gradient-dark">
                <img class="card-img-top" :src="'/media/'+this.meeting.medium_id" alt="Dist Photo 1">

                <div class="card-img-overlay d-flex flex-column justify-content-end"
                     style="padding:0 !important;">
                    <span class="p-3" style="background-color: rgba(0,0,0,0.5); ">
                        <h5 class="card-title text-white" >Livestream</h5>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 pt-2">
            <div class="card card-primary card-outline card-outline-tabs">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-tabs-four-home-tab"
                               data-toggle="pill" href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home"
                               aria-selected="true">Veranstaltungsbeschreibung</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-four-profile-tab"
                               data-toggle="pill" href="#custom-tabs-four-profile" role="tab"
                               aria-controls="custom-tabs-four-profile" aria-selected="false">Organisatorisches</a>
                        </li>
                        <li class="nav-item">
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

        <div class="col-9">
            <ul class="nav nav-pills"
                id="terminalObjectivesTopNav">
                <li class="nav-item pl-0 pr-2 pb-2 pt-2">
                    <a class="nav-link text-sm"
                       href="#tab_1"
                       data-toggle="tab">
                        Persönliche Agenda
                    </a>
                </li>
                <li class="nav-item pl-0 pr-2 pb-2 pt-2">
                    <a class="nav-link active text-sm"
                       href="#tab_2"
                       data-toggle="tab">
                        Datum 1
                    </a>
                </li>
                <li class="nav-item pl-0 pr-2 pb-2 pt-2">
                    <a class="nav-link text-sm"
                       href="#tab_3"
                       data-toggle="tab">
                        Datum 2
                    </a>
                </li>
                <li class="nav-item pl-0 pr-2 pb-2 pt-2">
                    <a class="nav-link text-sm"
                       href="#tab_4"
                       data-toggle="tab">
                        Raum 1
                    </a>
                </li>
                <li class="nav-item pl-0 pr-2 pb-2 pt-2">
                    <a class="nav-link text-sm"
                       href="#tab_5"
                       data-toggle="tab">
                        Raum 2
                    </a>
                </li>
                <li class="nav-item pl-0 pr-2 pb-2 pt-2">
                    <a class="nav-link text-sm"
                       href="#tab_6"
                       data-toggle="tab">
                        +
                    </a>
                </li>

            </ul>

            <div class="tab-content">
                <div class="tab-pane" id="tab_1">
                    <div class="timeline">
                        <!-- timeline time label -->
                        <!--                <div class="time-label">
                                            <span class="bg-primary">08. September 2022</span>
                                        </div>-->
                        <!-- /.timeline-label -->
                        <!-- timeline item -->
                        <div>
                            <i class="fas fa-message bg-blue"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> 10:00</span>
                                <h3 class="timeline-header"><a href="#">Begrüßung</a></h3>

                                <div class="timeline-body">
                                    Joachim Dieterich & Stephan Pfurtscheller
                                </div>
                                <!--                        <div class="timeline-footer">
                                                            <a class="btn btn-primary btn-sm">Read more</a>
                                                            <a class="btn btn-danger btn-sm">Delete</a>
                                                        </div>-->
                            </div>
                        </div>
                        <!-- END timeline item -->
                        <!-- timeline item -->
                        <div>
                            <i class="fas fa-comment bg-green"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> 10:05</span>
                                <h3 class="timeline-header"><a href="#">Grußworte</a><br></h3>

                                <div class="timeline-body">
                                    Ministerin brDr. Stefanie Hubig <br>
                                    Direktorin Dr. Birgit Pikows
                                </div>
                                <!--                        <div class="timeline-footer">
                                                            <a class="btn btn-primary btn-sm">Read more</a>
                                                            <a class="btn btn-danger btn-sm">Delete</a>
                                                        </div>-->
                            </div>
                        </div>
                        <!-- END timeline item -->
                        <!-- timeline item -->
                        <div>
                            <i class="fa fa-user-graduate bg-green"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> 5 mins ago</span>
                                <h3 class="timeline-header no-border">
                                    <a href="#">Das Digitale Kompetenzzentrum Rheinland-Pfalz</a>
                                    <br>
                                    Joachim Dieterich @ Stephan Pfurtscheller
                                </h3>
                                <div class="timeline-footer">
                                    <a class="btn btn-primary btn-sm">Read more</a>
                                </div>
                            </div>
                        </div>
                        <!-- END timeline item -->
                        <!-- timeline item -->
                        <div>
                            <i class="fas fa-video bg-maroon"></i>

                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> 5 days ago</span>

                                <h3 class="timeline-header"><a href="#">Markt der Möglichkeiten</a></h3>

                                <div class="timeline-body">
                                    <div class="embed-responsive embed-responsive-16by9">
                                        <iframe src="https://player.vimeo.com/video/749530355?h=35065989da&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479" width="1920" height="1080" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen title="Markt-der-Möglichkeiten_DigiKomp"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END timeline item -->
                        <div>
                            <i class="fas fa-clock bg-gray"></i>
                        </div>
                    </div>
                </div>
                <div class="tab-pane active" id="tab_2">
                    <div class="timeline">
                        <!-- timeline time label -->
                        <!--                <div class="time-label">
                                            <span class="bg-primary">08. September 2022</span>
                                        </div>-->
                        <!-- /.timeline-label -->
                        <!-- timeline item -->
                        <div>
                            <i class="fas fa-message bg-blue"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> 10:00</span>
                                <h3 class="timeline-header"><a href="#">Begrüßung</a></h3>

                                <div class="timeline-body">
                                    Joachim Dieterich & Stephan Pfurtscheller
                                </div>
                                <!--                        <div class="timeline-footer">
                                                            <a class="btn btn-primary btn-sm">Read more</a>
                                                            <a class="btn btn-danger btn-sm">Delete</a>
                                                        </div>-->
                            </div>
                        </div>
                        <!-- END timeline item -->
                        <!-- timeline item -->
                        <div>
                            <i class="fas fa-comment bg-green"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> 10:05</span>
                                <h3 class="timeline-header"><a href="#">Grußworte</a><br></h3>

                                <div class="timeline-body">
                                    Ministerin Dr. Stefanie Hubig <br>
                                    Direktorin Dr. Birgit Pikowsky
                                </div>
                                <!--                        <div class="timeline-footer">
                                                            <a class="btn btn-primary btn-sm">Read more</a>
                                                            <a class="btn btn-danger btn-sm">Delete</a>
                                                        </div>-->
                            </div>
                        </div>
                        <!-- END timeline item -->
                        <!-- timeline item -->
                        <div>
                            <i class="fa fa-user-graduate bg-green"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> 5 mins ago</span>
                                <h3 class="timeline-header no-border">
                                    <a href="#">Support(-strukturen und -kulturen) | Gestaltung des "Maschienenraums" der Schule</a>
                                    <br>
                                    Dr. Mandy Schiefner-Rohs
                                </h3>
                                <div class="timeline-footer">
                                    <a class="btn btn-primary btn-sm">Read more</a>
                                </div>
                            </div>
                        </div>
                        <!-- END timeline item -->
                        <!-- timeline item -->
                        <div>
                            <i class="fa fa-user-graduate bg-green"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> 5 mins ago</span>
                                <h3 class="timeline-header no-border">
                                    <a href="#">Das Digitale Kompetenzzentrum Rheinland-Pfalz</a>
                                    <br>
                                    Joachim Dieterich @ Stephan Pfurtscheller
                                </h3>
                                <div class="timeline-footer">
                                    <a class="btn btn-primary btn-sm">Read more</a>
                                </div>
                            </div>
                        </div>
                        <!-- END timeline item -->
                        <!-- timeline item -->
                        <div>
                            <i class="fas fa-comments bg-yellow"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> 27 mins ago</span>
                                <h3 class="timeline-header"><a href="#">Vernetzung und Austausch</a></h3>
                                <div class="timeline-body">
                                    Gruppenpuzzle
                                </div>
                                <div class="timeline-footer">
                                    <a class="btn btn-warning btn-sm">Gruppe wählen</a>
                                </div>
                            </div>
                        </div>
                        <!-- END timeline item -->
                        <!-- timeline item -->
                        <div>
                            <i class="fas fa-video bg-maroon"></i>

                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> 5 days ago</span>

                                <h3 class="timeline-header"><a href="#">Markt der Möglichkeiten</a></h3>

                                <div class="timeline-body">
                                    <div class="embed-responsive embed-responsive-16by9">
                                        <iframe src="https://player.vimeo.com/video/749530355?h=35065989da&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479" width="1920" height="1080" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen title="Markt-der-Möglichkeiten_DigiKomp"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END timeline item -->
                        <div>
                            <i class="fas fa-clock bg-gray"></i>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab_3">
                    <div class="timeline">
                        <!-- timeline time label -->
                        <!--                <div class="time-label">
                                            <span class="bg-primary">08. September 2022</span>
                                        </div>-->
                        <!-- /.timeline-label -->
                        <!-- timeline item -->
                        <div>
                            <i class="fas fa-message bg-blue"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> 10:00</span>
                                <h3 class="timeline-header"><a href="#">Datum 2</a></h3>

                                <div class="timeline-body">
                                    Test
                                </div>
                                <!--                        <div class="timeline-footer">
                                                            <a class="btn btn-primary btn-sm">Read more</a>
                                                            <a class="btn btn-danger btn-sm">Delete</a>
                                                        </div>-->
                            </div>
                        </div>
                        <!-- END timeline item -->
                    </div>
                </div>
                <div class="tab-pane" id="tab_4">
                    <div class="timeline">
                        <!-- timeline time label -->
                        <!--                <div class="time-label">
                                            <span class="bg-primary">08. September 2022</span>
                                        </div>-->
                        <!-- /.timeline-label -->
                        <!-- timeline item -->
                        <div>
                            <i class="fas fa-message bg-blue"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> 10:00</span>
                                <h3 class="timeline-header"><a href="#">Raum 1</a></h3>

                                <div class="timeline-body">
                                    Test
                                </div>
                                <!--                        <div class="timeline-footer">
                                                            <a class="btn btn-primary btn-sm">Read more</a>
                                                            <a class="btn btn-danger btn-sm">Delete</a>
                                                        </div>-->
                            </div>
                        </div>
                        <!-- END timeline item -->
                    </div>
                </div>
                <div class="tab-pane" id="tab_5">
                    <div class="timeline">
                        <!-- timeline time label -->
                        <!--                <div class="time-label">
                                            <span class="bg-primary">08. September 2022</span>
                                        </div>-->
                        <!-- /.timeline-label -->
                        <!-- timeline item -->
                        <div>
                            <i class="fas fa-message bg-blue"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> 10:00</span>
                                <h3 class="timeline-header"><a href="#">Raum 2</a></h3>

                                <div class="timeline-body">
                                    Test
                                </div>
                                <!--                        <div class="timeline-footer">
                                                            <a class="btn btn-primary btn-sm">Read more</a>
                                                            <a class="btn btn-danger btn-sm">Delete</a>
                                                        </div>-->
                            </div>
                        </div>
                        <!-- END timeline item -->
                    </div>
                </div>
                <div class="tab-pane" id="tab_6">
                    Neue Agenda anlegen
                </div>
            </div>



        </div>
        <div class="col-3 pl-0">
            <h6>Veranstaltungschat</h6>
            <div class="direct-chat-messages px-0" style="height: 500px !important;">
                <div class="direct-chat-msg"><div class="direct-chat-infos clearfix"><span class="direct-chat-name  float-left">
                            Schüler Role
                        </span> <span class="direct-chat-timestamp float-right">
                            2020-12-20 17:18:34
                        </span></div> <span class="direct-chat-img"><img src="/media/2975" alt="User profile picture" class="direct-chat-img"></span> <div class="direct-chat-text"><!---->
                Hallo aus Mainz
            </div></div><div class="direct-chat-msg right"><div class="direct-chat-infos clearfix"><span class="direct-chat-name  float-right">
                            Joachim Dieterich
                        </span> <span class="direct-chat-timestamp float-left">
                            2020-12-20 17:16:45
                        </span></div> <img src="/media/2973" alt="User profile picture" class="direct-chat-img"> <div class="direct-chat-text"><!---->
                Die Veranstaltung startet um 10 Uhr
            </div></div><div class="direct-chat-msg right"><div class="direct-chat-infos clearfix"><span class="direct-chat-name  float-right">
                            Joachim Dieterich
                        </span> <span class="direct-chat-timestamp float-left">
                            2020-12-20 15:53:53
                        </span></div> <img src="/media/2973" alt="User profile picture" class="direct-chat-img"> <div class="direct-chat-text"><!---->
                Herzlich Willkommen
            </div></div></div>
        </div>

    </div>
</template>

<script>
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

    }

}

</script>
