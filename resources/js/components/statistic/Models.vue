<template>
    <div class="card">
        <div class="w-full flex-1 p-2">
            {{ title }}
            <input
                type="search"
                class="form-control form-control-sm"
                style="border:0;"
                :placeholder="trans('global.search') + '...'"
                v-model="search"
            >
        </div>
        <div
            class="card-footer bg-light p-0"
            style="max-height: 225px; overflow-y: auto"
        >
            <ul class="nav nav-pills flex-column">
                <li v-for="item in model_data"
                    class="nav-item"
                    :style="isVisible(item)"
                >
                    <span class="nav-link">
                        {{ item.value }}
                        <span class="float-right">{{item.counter}}</span>
                    </span>
                </li>
            </ul>
        </div>
    </div>
</template>
<script>
export default {
    name: 'Models',
    props: {
        id: {
            type: String,
            required: true,
        },
        title: {
            type: String,
            required: true,
        },
        chart: {
            type: String,
            required: true,
        },
        date_begin: {
            type: String,
            required: true,
        },
        date_end: {
            type: String,
            required: true,
        },
        width: {
            type: Number,
            default: 200,
        },
        height: {
            type: Number,
            default: 400,
        },
        cssClasses: {
            type: String,
            default: '',
        },
        styles: {
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
            search: '',
            model_data: [],
        }
    },
    methods: {
        loaderEvent() {
            this.model_data = [];
            const models = [
                'Certificate',
                'Content',
                'Curriculum',
                'CurriculumSubscription',
                'EnablingObjective',
                'TerminalObjective',
                'Exercise',
                'Group',
                'Kanban',
                'KanbanStatus',
                'KanbanItem',
                'KanbanSubscription',
                'LmsReference',
                'Logbook',
                'LogbookEntry',
                'LogbookSubscription',
                'Map',
                'MapMarker',
                'MapMarkerSubscription',
                'Medium',
                'MediumSubscription',
                'Meeting',
                'MeetingSubscription',
                'Organization',
                'Plan',
                'PlanEntry',
                'PlanSubscription',
                'Task',
                'TaskSubscription',
                'Training',
                'TrainingSubscription',
                'User',
                'Videoconference',
                'VideoconferenceSubscription',
            ];

            models.forEach((model_name) => {
                axios.get('/statistics?chart=' + this.chart + '&model=' + model_name + '&date_begin=' + this.date_begin + '&date_end=' + this.date_end)
                    .then(response => {
                        this.model_data.push(response.data);
                    }).catch(e => console.log(e));
            });
        },
        isVisible(item) {
            if (item.value === null) {
                return this.search.toLowerCase() !== ''
                    ? "display: none"
                    : "";
            } else {
                return item.value.toLowerCase().indexOf(this.search.toLowerCase()) === -1
                    ? "display: none"
                    : "";
            }
        },
    },
    watch: {
        date_begin: {
            handler: function() {
                this.loaderEvent();
            },
        },
        date_end: {
            handler: function() {
                this.loaderEvent();
            },
        },
    },
    mounted() {
        this.loaderEvent();
    },
}
</script>