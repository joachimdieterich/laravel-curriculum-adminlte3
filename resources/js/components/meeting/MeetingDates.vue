<template>
    <div >
        <ul class="nav nav-pills"
            id="meetingDatesNav">
            <li v-for="date in this.dates"
                class="nav-item pl-0 pr-2 pb-2 pt-2">
                <a class="nav-link " :href="'#meetingDates_' + date.id"
                   :class="(activetab == date.id) ? 'active' : ''"
                   @click="setActiveTab(date.id)"
                   data-toggle="tab">
                    {{ date.title | truncate(10, '&nbsp;') }}
                </a>
            </li>

            <li class="nav-item pl-0 pr-2 pb-2 pt-2">
                <a class="nav-link text-sm"
                   href="#tab_6"
                   data-toggle="tab">
                    + Datum
                </a>
            </li>

        </ul>

        <div class="tab-content">
            <div v-for="date in this.dates"
                 class="tab-pane" :id="'meetingDates_' + date.id"
                 :class="(activetab == date.id) ? 'active' : ''">
                <ul class="nav nav-pills"
                    id="AgendaNav">
                    <li class="nav-item pl-0 pr-2 pb-2 pt-2">
                        <a class="nav-link text-sm"
                           href="#subscribed_agenda"
                           @click="setActiveSubTab(0)"
                           data-toggle="tab">
                            Persönliche Agenda
                        </a>
                    </li>
                    <li  v-for="agenda in date.agendas"
                         class="nav-item pl-0 pr-2 pb-2 pt-2">
                        <a class="nav-link text-sm"
                           :href="'#agenda_' + agenda.id"
                           data-toggle="tab"
                           @click="setActiveSubTab(agenda.id)">
                            {{ agenda.title }}
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <Agenda id="agenda_0"
                        :meeting_date_id="date.id"
                        :subscribed=true
                        class="tab-pane"
                        :class="(activesubtab == '0') ? 'active' : ''"/>
                    <div v-for="agenda in date.agendas"
                         class="tab-pane"
                         :id="'agenda_' + agenda.id"
                         :class="(activesubtab == agenda.id) ? 'active' : ''">
                        <Agenda :agenda="agenda"/>
                    </div>
                </div>

            </div>


<!--            <div class="tab-pane active" id="tab_2">
                <div class="timeline">
                    &lt;!&ndash; timeline time label &ndash;&gt;
                    &lt;!&ndash;                <div class="time-label">
                                        <span class="bg-primary">08. September 2022</span>
                                    </div>&ndash;&gt;
                    &lt;!&ndash; /.timeline-label &ndash;&gt;
                    &lt;!&ndash; timeline item &ndash;&gt;
                    <div>
                        <i class="fas fa-message bg-blue"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> 10:00</span>
                            <h3 class="timeline-header"><a href="#">Begrüßung</a></h3>

                            <div class="timeline-body">
                                Joachim Dieterich & Stephan Pfurtscheller
                            </div>
                            &lt;!&ndash;                        <div class="timeline-footer">
                                                        <a class="btn btn-primary btn-sm">Read more</a>
                                                        <a class="btn btn-danger btn-sm">Delete</a>
                                                    </div>&ndash;&gt;
                        </div>
                    </div>
                    &lt;!&ndash; END timeline item &ndash;&gt;
                    &lt;!&ndash; timeline item &ndash;&gt;
                    <div>
                        <i class="fas fa-comment bg-green"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> 10:05</span>
                            <h3 class="timeline-header"><a href="#">Grußworte</a><br></h3>

                            <div class="timeline-body">
                                Ministerin Dr. Stefanie Hubig <br>
                                Direktorin Dr. Birgit Pikowsky
                            </div>
                            &lt;!&ndash;                        <div class="timeline-footer">
                                                        <a class="btn btn-primary btn-sm">Read more</a>
                                                        <a class="btn btn-danger btn-sm">Delete</a>
                                                    </div>&ndash;&gt;
                        </div>
                    </div>
                    &lt;!&ndash; END timeline item &ndash;&gt;
                    &lt;!&ndash; timeline item &ndash;&gt;
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
                    &lt;!&ndash; END timeline item &ndash;&gt;
                    &lt;!&ndash; timeline item &ndash;&gt;
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
                    &lt;!&ndash; END timeline item &ndash;&gt;
                    &lt;!&ndash; timeline item &ndash;&gt;
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
                    &lt;!&ndash; END timeline item &ndash;&gt;
                    &lt;!&ndash; timeline item &ndash;&gt;
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
                    &lt;!&ndash; END timeline item &ndash;&gt;
                    <div>
                        <i class="fas fa-clock bg-gray"></i>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="tab_3">
                <div class="timeline">
                    &lt;!&ndash; timeline time label &ndash;&gt;
                    &lt;!&ndash;                <div class="time-label">
                                        <span class="bg-primary">08. September 2022</span>
                                    </div>&ndash;&gt;
                    &lt;!&ndash; /.timeline-label &ndash;&gt;
                    &lt;!&ndash; timeline item &ndash;&gt;
                    <div>
                        <i class="fa fa-user-graduate bg-orange"></i>
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
                    &lt;!&ndash; END timeline item &ndash;&gt;
                </div>
            </div>
            <div class="tab-pane" id="tab_4">
                <div class="timeline">
                    &lt;!&ndash; timeline time label &ndash;&gt;
                    &lt;!&ndash;                <div class="time-label">
                                        <span class="bg-primary">08. September 2022</span>
                                    </div>&ndash;&gt;
                    &lt;!&ndash; /.timeline-label &ndash;&gt;
                    &lt;!&ndash; timeline item &ndash;&gt;
                    <div>
                        <i class="fas fa-comment bg-green"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> 10:00</span>
                            <h3 class="timeline-header"><a href="#">Raum 1</a></h3>

                            <div class="timeline-body">
                                Test
                            </div>
                            &lt;!&ndash;                        <div class="timeline-footer">
                                                        <a class="btn btn-primary btn-sm">Read more</a>
                                                        <a class="btn btn-danger btn-sm">Delete</a>
                                                    </div>&ndash;&gt;
                        </div>
                    </div>
                    &lt;!&ndash; END timeline item &ndash;&gt;
                </div>
            </div>
            <div class="tab-pane" id="tab_5">
                <div class="timeline">
                    &lt;!&ndash; timeline time label &ndash;&gt;
                    &lt;!&ndash;                <div class="time-label">
                                        <span class="bg-primary">08. September 2022</span>
                                    </div>&ndash;&gt;
                    &lt;!&ndash; /.timeline-label &ndash;&gt;
                    &lt;!&ndash; timeline item &ndash;&gt;
                    <div>
                        <i class="fas fa-user bg-orange"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> 10:00</span>
                            <h3 class="timeline-header"><a href="#">Raum 2</a></h3>

                            <div class="timeline-body">
                                Test
                            </div>
                            &lt;!&ndash;                        <div class="timeline-footer">
                                                        <a class="btn btn-primary btn-sm">Read more</a>
                                                        <a class="btn btn-danger btn-sm">Delete</a>
                                                    </div>&ndash;&gt;
                        </div>
                    </div>
                    &lt;!&ndash; END timeline item &ndash;&gt;
                </div>
            </div>-->
            <div class="tab-pane" id="tab_6">
                Neue Agenda anlegen
            </div>
        </div>



    </div>
</template>

<script>
import Agenda from "./Agenda";

export default {
    props: {
        'meeting': Object,
    },
    data () {
        return {
            dates: {},
            search: '',
            showPrintOptions: false,
            activetab: null,
            activesubtab: null,
            agenda_ids: [],

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
        setActiveTab(tab){
            this.activetab = tab;
        },
        setActiveSubTab(tab){
            this.activesubtab = tab;
        },
        generateSubscribedItems(){
            this.dates.forEach(d => d.agendas.forEach(a => this.agenda_ids.push(a.id)));
            //console.log(this.agenda_ids);
        }

    },
    mounted() {

        axios.get('/meetingDates?meeting_id='+this.meeting.id)
            .then(response => {
                this.dates = response.data.dates;
                this.generateSubscribedItems(); //generate subscribed agenda
            })
            .catch(e => {
                this.errors = e.data.errors;
            });

    },
    components: {
        Agenda

    }

}

</script>
