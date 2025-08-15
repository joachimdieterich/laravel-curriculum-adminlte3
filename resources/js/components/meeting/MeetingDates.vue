<template>
    <div>
        <ul
            id="meetingDatesNav"
            class="nav nav-pills"
        >
            <li v-for="date in this.dates"
                class="nav-item pl-0 pr-2 pb-2 pt-2"
                @click="setGlobalStorage('#meeting_' + meeting.id, '#meetingDates_' + date.id)"
            >
                <a
                    :href="'#meetingDates_' + date.id"
                    class="nav-link "
                    :class="getGlobalStorage('#meeting_' + meeting.id, '#meetingDates_' + date.id, 'active')"
                    data-toggle="tab"
                    @click="setActiveDate(date)"
                >
                    {{ date.title }}
                </a>
            </li>

            <li
                class="nav-item pl-0 pr-2 pb-2 pt-2"
                @click="setGlobalStorage('#meeting_' + meeting.id, 'new_meeting_date_tab')"
            >
                <a
                    href="#new_meeting_date_tab"
                    class="nav-link text-sm"
                    :class="getGlobalStorage('#meeting_' + meeting.id, 'new_meeting_date_tab', 'active')"
                    data-toggle="tab"
                    @click="editMeetingDate();"
                >
                    <i class="fa fa-pencil-alt"></i>
                </a>
            </li>

            <li
                class="nav-item ml-auto pull-right pl-0 pr-2 pb-2 pt-2"
                @click="setGlobalStorage('#meeting_' + meeting.id, 'new_meeting_date_tab')"
            >
                <a
                    href="#new_meeting_date_tab"
                    class="nav-link text-sm"
                    :class="getGlobalStorage('#meeting_' + meeting.id, 'new_meeting_date_tab', 'active')"
                    data-toggle="tab"
                >
                    <i class="fas fa-plus"></i>
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div v-for="date in this.dates"
                :id="'meetingDates_' + date.id"
                class="tab-pane"
                :class="getGlobalStorage('#meeting_' + meeting.id, '#meetingDates_' + date.id, 'active')"
            >
                <ul
                    id="AgendaNav"
                    class="nav nav-pills"
                >
                    <li
                        class="nav-item pl-0 pr-2 pb-2 pt-2"
                        @click="setGlobalStorage('#meeting_date_' + date.id, '#subscribed_agenda_' + date.id); $eventHub.emit('reload_subscribed_agenda', date);"
                    >
                        <a
                            :href="'#subscribed_agenda_' + date.id"
                            class="nav-link text-sm"
                            :class="getGlobalStorage('#meeting_date_' + date.id, '#subscribed_agenda_'+ date.id, 'active', true)"
                            data-toggle="tab"
                        >
                            Persönliche Agenda
                        </a>
                    </li>
                    <li v-for="agenda in date.agendas"
                        class="nav-item pl-0 pr-2 pb-2 pt-2"
                        @click="setGlobalStorage('#meeting_date_' + date.id, '#agenda_' + agenda.id)"
                    >
                        <a
                            :href="'#agenda_' + agenda.id"
                            class="nav-link text-sm"
                            :class="getGlobalStorage('#meeting_date_' + date.id, '#agenda_' + agenda.id, 'active')"
                            data-toggle="tab"
                        >
                            {{ agenda.title }}
                        </a>
                    </li>
                    <li
                        class="nav-item pl-0 pr-2 pb-2 pt-2"
                        @click="setGlobalStorage('#meeting_date_' + date.id, '#new_meeting_agenda_tab_' + date.id)"
                    >
                        <a
                            :href="'#new_meeting_agenda_tab_' + date.id"
                            class="nav-link text-sm"
                            :class="getGlobalStorage('#meeting_date_' + date.id, '#new_meeting_agenda_tab_' + date.id, 'active')"
                            data-toggle="tab"
                        >
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
                        :class="getGlobalStorage('#meeting_date_' + date.id, '#subscribed_agenda_'+ date.id, 'active', true)"
                    />
                    <div v-for="agenda in date.agendas"
                        :id="'agenda_' + agenda.id"
                        class="tab-pane"
                        :class="getGlobalStorage('#meeting_date_' + date.id, '#agenda_' + agenda.id, 'active')"
                    >
                        <Agenda
                            :agenda="agenda"
                            :ref="'agenda_' + agenda.id"
                            ref="agenda"
                        />
                    </div>
                    <div
                        :id="'new_meeting_agenda_tab_' + date.id"
                        class="tab-pane"
                        :class="getGlobalStorage('#meeting_date_' + date.id, '#new_meeting_agenda_tab_' + date.id, 'active')"
                    >
                        <MeetingAgendaForm
                            :meeting_date_id="date.id"
                            :ref="'new_meeting_agenda_tab_' + date.id"
                        />
                    </div>
                </div>
            </div>

            <div
                id="new_meeting_date_tab"
                class="tab-pane"
                :class="getGlobalStorage('#meeting_' + meeting.id, 'new_meeting_date_tab', 'active')"
            >
                <MeetingDateForm
                    :meeting="meeting"
                    ref="new_meeting_date_tab"
                />
            </div>
        </div>
    </div>
</template>
<script>
import Agenda from "./Agenda.vue";
import MeetingDateForm from "./MeetingDateForm.vue";
import MeetingAgendaForm from "./MeetingAgendaForm.vue";

export default {
    props: {
        'meeting': Object,
    },
    data() {
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
        setActiveDate(date) {
            this.activeDate = date;
        },
        editMeetingDate() {
            this.setGlobalStorage('#meeting_' + this.activeDate.meeting_id, 'new_meeting_date_tab');
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
        Agenda,
    },
}
</script>