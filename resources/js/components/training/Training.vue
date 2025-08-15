<template>
    <div>
        <div class="card">
            <div class="card-header">
<!--                <div class="card-tools pr-2 s">
                    <i class="fa fa-pencil-alt"></i>
                </div>-->
                <div class="user-block">
                    <span class="username ml-0">
                        {{training.title}}
                        <span class="description ml-0 ">
                            {{this.timePeriod}}
                        </span>
                    </span>
                </div>
            </div>
            <div class="card-body"
                v-dompurify-html="training.description">
            </div>
<!--            <div class="card-footer">
                <CalendarCreateEvent/>
            </div>-->
        </div>
        <exercises
            :training="training">
        </exercises>
    </div>
</template>

<script>
import Exercises from "../exercise/Exercises";
import CalendarCreateEvent from "../calendar/CalendarCreateEvent";

export default {
    name: "Training.vue",
    props: {
        training: {
            default: null
        },
    },
    data() {
        return {
            timePeriod: '',
        }
    },
    mounted() {
        // TODO: improve this
        if (this.training.begin != null && this.training.end != null) this.postDate();
    },
    methods: {
        postDate() {
            const start = new Date(this.training.begin.replace(/-/g, "/"));
            const end = new Date(this.training.end.replace(/-/g, "/"));
            const dateFormat = {
                weekday: 'short',
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
              /*  hour: '2-digit',
                minute: '2-digit'*/
            };

            if (start.toDateString() === end.toDateString()) {
                this.timePeriod = start.toLocaleString([], dateFormat) + " - " + end.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } else {
                this.timePeriod = start.toLocaleString([], dateFormat) + " - " + end.toLocaleString([], dateFormat);
            }
        },
    },
    components: {
        CalendarCreateEvent,
        Exercises
    }
}
</script>

