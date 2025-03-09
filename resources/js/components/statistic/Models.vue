<template :id="this.id "
          class="p-2">
    <div class="card">
        <div class="w-full flex-1 p-2">
            {{ this.title }}
            <input type="search"
                   class="form-control form-control-sm"
                   style="border:0;"
                   :placeholder="trans('global.search')+'...'"
                   v-model="search">
        </div>
        <div class="card-footer bg-light p-0"
             style="max-height:225px; overflow-y: auto">
            <ul class="nav nav-pills flex-column">
                <li v-for="item in model_data"
                    class="nav-item"
                    :style="isVisible(item)">
                    <span  class="nav-link">
                        {{item.value}}
                        <span class="float-right">
                        {{item.counter}}</span>
                    </span>
                </li>
            </ul>
        </div>
    </div>
</template>
<script>

export default {
    name: 'Models',
    components: {
    },
    props: {
        'id' : String,
        'title': String,
        'chart': String,
        'date_begin': String,
        'date_end': String,
        width: {
            type: Number,
            default: 200
        },
        height: {
            type: Number,
            default: 400
        },
        cssClasses: {
            default: '',
            type: String
        },
        styles: {
            type: Object,
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
            this.model = [
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


            this.model.forEach(
                (model_name) => {
                    axios.get('/statistics?chart=' + this.chart + '&model=' + model_name + '&date_begin=' + this.date_begin + '&date_end=' + this.date_end)
                        .then(response => {
                            this.model_data.push(response.data.message);
                        }).catch(e => {
                        console.log(e);
                    });
                }
            );
        },

        isVisible(item){
            if (item.value === null){
                if (this.search.toLowerCase() != ''){
                    return "display:none";
                } else {
                    return "";
                }
            }
            if (item.value.toLowerCase().indexOf(this.search.toLowerCase()) === -1){
                return "display:none";
            } else {
                return "";
            }
        },
    },
    watch: {
        date_begin: {
            handler: function(){
                this.loaderEvent();
            }
        },
        date_end: {
            handler: function(){
                this.loaderEvent();
            }
        }
    },
    computed: {
    },
    mounted() {
        this.loaderEvent();
    }
}
</script>
