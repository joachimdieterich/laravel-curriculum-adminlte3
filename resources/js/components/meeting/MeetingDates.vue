<template>
    <div >
        <ul class="nav nav-pills"
            id="meetingDatesNav">
            <li v-for="date in this.dates"
                class="nav-item pl-0 pr-2 pb-2 pt-2"
                @click="setLocalStorage('#meeting_' + meeting.id, '#meetingDates_' + date.id)">
                <a class="nav-link "
                   :href="'#meetingDates_' + date.id"
                   :class="checkLocalStorage('#meeting_' + meeting.id, '#meetingDates_' + date.id, 'active')"
                   @click="setActiveDate(date)"
                   data-toggle="tab">
                    {{ date.title | truncate(10, '&nbsp;') }}
                </a>

            </li>


            <li class="nav-item pl-0 pr-2 pb-2 pt-2"
                @click="setLocalStorage('#meeting_' + meeting.id, 'new_meeting_date_tab')">
                <a class="nav-link text-sm"
                   :class="checkLocalStorage('#meeting_' + meeting.id, 'new_meeting_date_tab', 'active')"
                   href="#new_meeting_date_tab"
                   data-toggle="tab"
                   @click="editMeetingDate();">
                    <i class="fa fa-pencil-alt"></i>
                </a>
            </li>

            <li class="nav-item ml-auto pull-right pl-0 pr-2 pb-2 pt-2"
                @click="setLocalStorage('#meeting_' + meeting.id, 'new_meeting_date_tab')">
                <a class="nav-link text-sm"
                   :class="checkLocalStorage('#meeting_' + meeting.id, 'new_meeting_date_tab', 'active')"
                   href="#new_meeting_date_tab"
                   data-toggle="tab">
                    <i class="fas fa-plus"></i>
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div v-for="date in this.dates"
                 :id="'meetingDates_' + date.id"
                 class="tab-pane"
                 :class="checkLocalStorage('#meeting_' + meeting.id, '#meetingDates_' + date.id, 'active')"
                 >
                <ul class="nav nav-pills"
                    id="AgendaNav">
                    <li class="nav-item pl-0 pr-2 pb-2 pt-2"
                        @click="setLocalStorage('#meeting_date_' + date.id, '#subscribed_agenda_'+ date.id); $eventHub.$emit('reload_subscribed_agenda', date);"
                    >
                        <a class="nav-link text-sm"
                           :href="'#subscribed_agenda_' + date.id"
                           :class="checkLocalStorage('#meeting_date_' + date.id, '#subscribed_agenda_'+ date.id, 'active', true)"
                           data-toggle="tab">
                            Persönliche Agenda
                        </a>
                    </li>
                    <li  v-for="agenda in date.agendas"
                         class="nav-item pl-0 pr-2 pb-2 pt-2"
                         @click="setLocalStorage('#meeting_date_' + date.id, '#agenda_' + agenda.id)">
                        <a class="nav-link text-sm"
                           :href="'#agenda_' + agenda.id"
                           data-toggle="tab"
                           :class="checkLocalStorage('#meeting_date_' + date.id, '#agenda_' + agenda.id, 'active')"
                           >
                            {{ agenda.title }}
                        </a>
                    </li>
                    <li class="nav-item pl-0 pr-2 pb-2 pt-2"
                        @click="setLocalStorage('#meeting_date_' + date.id, '#new_meeting_agenda_tab_' + date.id)"
                    >
                        <a class="nav-link text-sm"
                           :class="checkLocalStorage('#meeting_date_' + date.id, '#new_meeting_agenda_tab_' + date.id, 'active')"
                           :href="'#new_meeting_agenda_tab_' + date.id"
                           data-toggle="tab">
                            <i class="fas fa-plus"></i>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <Agenda
                        :id="'subscribed_agenda_'+ date.id"
                        :meeting_date_id="date.id"
                        :personal_agenda=true
                        class="tab-pane"
                        :class="checkLocalStorage('#meeting_date_' + date.id, '#subscribed_agenda_'+ date.id, 'active', true)"/>
                    <div v-for="agenda in date.agendas"
                         class="tab-pane"
                         :id="'agenda_' + agenda.id"
                         :class="checkLocalStorage('#meeting_date_' + date.id, '#agenda_' + agenda.id, 'active')">
                        <Agenda :agenda="agenda"
                                :ref="'agenda_' + agenda.id"
                                ref="agenda"/>
                    </div>
                    <div
                        class="tab-pane"
                        :class="checkLocalStorage('#meeting_date_' + date.id, '#new_meeting_agenda_tab_' + date.id, 'active')"
                        :id="'new_meeting_agenda_tab_' + date.id">
                        <MeetingAgendaForm
                            :meeting_date_id="date.id"
                            :ref="'new_meeting_agenda_tab_' + date.id"/>
                    </div>
                </div>
            </div>

            <div
                class="tab-pane"
                :class="checkLocalStorage('#meeting_' + meeting.id, 'new_meeting_date_tab', 'active')"
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
            activeDate: null,
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
        setActiveDate(date){
            this.activeDate = date;
        },
        editMeetingDate(){
            this.setLocalStorage('#meeting_' + this.activeDate.meeting_id, 'new_meeting_date_tab');
            this.$refs.new_meeting_date_tab.setData(this.activeDate);
        },

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
