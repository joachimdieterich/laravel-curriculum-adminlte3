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
                    <span
                        class="pl-2"
                        @click.stop="editMeetingDate(date)">
                        <i class="fa fa-pencil-alt"></i>
                    </span>
                </a>

            </li>

            <li class="nav-item pl-0 pr-2 pb-2 pt-2">
                <a class="nav-link text-sm"
                   :class="(activetab == 0) ? 'active' : ''"
                   @click="setActiveTab(0, 'reset');"
                   href="#new_meeting_date_tab"
                   data-toggle="tab">
                    <i class="fas fa-plus"></i>
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
                    <li class="nav-item pl-0 pr-2 pb-2 pt-2">
                        <a class="nav-link text-sm"
                           @click="setActiveSubTab('new_meeting_agenda_tab_' + date.id, 'reset');"
                           :href="'#new_meeting_agenda_tab_' + date.id"
                           data-toggle="tab">
                            <i class="fas fa-plus"></i>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <Agenda
                        id="agenda_0"
                        ref="agenda"
                        :meeting_date_id="date.id"
                        :personal_agenda=true
                        class="tab-pane"
                        :class="(activesubtab == '0') ? 'active' : ''"/>
                    <div v-for="agenda in date.agendas"
                         class="tab-pane"
                         :id="'agenda_' + agenda.id"
                         :class="(activesubtab == agenda.id) ? 'active' : ''">
                        <Agenda :agenda="agenda"
                                :ref="'agenda_' + agenda.id"
                                ref="agenda"/>
                    </div>
                    <div
                        class="tab-pane"
                        :class="(activesubtab == 'new_meeting_agenda_tab_' + date.id) ? 'active' : ''"
                        :id="'new_meeting_agenda_tab_' + date.id">
                        <MeetingAgendaForm
                            :meeting_date_id="date.id"
                            :ref="'new_meeting_agenda_tab_' + date.id"/>
                    </div>
                </div>
            </div>

            <div
                class="tab-pane"
                :class="(activetab == 0) ? 'active' : ''"
                id="new_meeting_date_tab">
                <MeetingDateForm
                    :meeting="meeting"
                    ref="new_meeting_date_tab"/>
            </div>
        </div>



    </div>
</template>

<script>
import Agenda from "./Agenda";
import MeetingDateForm from "./MeetingDateForm";
import MeetingAgendaForm from "./MeetingAgendaForm";

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
        setActiveTab(tab, reset = ''){
            if (reset == 'reset'){
                this.$refs.new_meeting_date_tab.setData(reset);
            }
            this.activetab = tab;
        },
        setActiveSubTab(tab, reset = ''){
            this.activesubtab = tab;
        },
        editMeetingDate(date){
            this.setActiveTab(0);
            this.$refs.new_meeting_date_tab.setData(date);
        },

       /* generateSubscribedItems(){
            this.dates.forEach(d => d.agendas.forEach(a => this.agenda_ids.push(a.id)));
            //console.log(this.agenda_ids);
        }*/

    },
    mounted() {
        axios.get('/meetingDates?meeting_id='+this.meeting.id)
            .then(response => {
                this.dates = response.data.dates;
                //this.generateSubscribedItems(); //generate subscribed agenda
            })
            .catch(e => {
                this.errors = e.data.errors;
            });

    },
    components: {
        MeetingAgendaForm,
        MeetingDateForm,
        Agenda
    }

}

</script>
