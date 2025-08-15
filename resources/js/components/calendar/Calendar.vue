<template>
    <div>
        <FullCalendar
            ref="fullCalendar"
            :options='calendarOptions'
        >
            <template v-slot:eventContent='arg'>
                <b>{{ arg.timeText }}</b>
                <i>{{ arg.event.title }}</i>
            </template>
        </FullCalendar>
    </div>
</template>


<script>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import deLocale from "@fullcalendar/core/locales/de";

export default {

    components: {
        FullCalendar // make the <FullCalendar> tag available
    },

    data: function() {
        return {

            calendarOptions: {
                locale: deLocale,
                plugins: [
                    dayGridPlugin,
                    timeGridPlugin,
                    interactionPlugin // needed for dateClick
                ],
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                initialView: 'dayGridMonth',
                initialEvents: [], // alternatively, use the `events` setting to fetch from a feed
                editable: true,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                weekends: true,
                select: this.handleDateSelect,
                eventClick: this.handleEventClick,
                eventsSet: this.handleEvents,
                eventSources: [
                    {
                        url: '/calendarEvents',
                        method: 'GET',
                        color: 'yellow',
                        textColor: 'black',
                    }
                ],

                /* you can update a remote database when these fire:
                eventAdd:
                eventChange:
                eventRemove:
                */

            },
            currentEvents: []
        }
    },

    methods: {
        loaderEvent(){
            let calendarApi = this.$refs.fullCalendar.getApi();

            axios.get('/calendarEvents?start='+ '2020-01-01' + '&end='+ '2024-01-01' )
                .then(res => {
                    this.calendarOptions.events = res.data;
                    //this.currentEvents = res.data;
                })
                .catch(e => {
                    this.errors = e.data.errors;
                });
        },
        handleWeekendsToggle() {
            this.calendarOptions.weekends = !this.calendarOptions.weekends // update a property
        },

        handleDateSelect(selectInfo) {
            let title = prompt('Please enter a new title for your event')
            let calendarApi = selectInfo.view.calendar

            calendarApi.unselect() // clear date selection

            if (title) {
            axios.post('/calendarEvents',
                {
                    id: 3,//createEventId(),
                    title,
                    start: selectInfo.startStr,
                    end: selectInfo.endStr,
                    allDay: selectInfo.allDay
                })
                .then(res => { // Tell the parent component we've added a new task and include it
                    calendarApi.addEvent({
                        id: res.data.id,
                        title: res.data.title,
                        start: res.data.start,
                        end: res.data.end,
                        allDay: res.data.allDay
                    })
                })
                .catch(error => { // Handle the error returned from our request
                    alert(error.data.errors);
                });
            }
        },

        handleEventClick(clickInfo) {
            if (confirm(`Are you sure you want to delete the event '${clickInfo.event.title}'`)) {
                clickInfo.event.remove()
            }
        },

        handleEvents(events) {
            this.currentEvents = events
        },
    },
    mounted() {
        //this.loaderEvent();
    },
}
</script>



<!--
<style lang='css'>

h2 {
    margin: 0;
    font-size: 16px;
}

ul {
    margin: 0;
    padding: 0 0 0 1.5em;
}

li {
    margin: 1.5em 0;
    padding: 0;
}

b { /* used for event dates/times */
    margin-right: 3px;
}

.fc { /* the calendar root */
    max-width: 1100px;
    margin: 0 auto;
}

</style>
-->
