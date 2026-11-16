<template>
    <div class="card">
        <div class="p-2">
            Models
            <input
                type="search"
                class="form-control border-0"
                :placeholder="trans('global.search') + '...'"
                v-model="search"
            >
        </div>
        <div
            class="card-footer bg-light p-0 overflow-y-auto"
            style="max-height: 225px;"
        >
            <ul class="nav nav-pills flex-column">
                <li v-for="item in model_data"
                    class="nav-item"
                    :style="isVisible(item)"
                >
                    <span class="nav-link">
                        {{ item.value }}
                        <span class="float-end">{{ item.counter }}</span>
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
        date_begin: {
            type: String,
            required: true,
        },
        date_end: {
            type: String,
            required: true,
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
                'Tag',
            ];

            models.forEach((model_name) => {
                axios.get('/statistics?chart=model&model=' + model_name + '&date_begin=' + this.date_begin + '&date_end=' + this.date_end)
                    .then(response => this.model_data.push(response.data))
                    .catch(e => console.log(e));
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