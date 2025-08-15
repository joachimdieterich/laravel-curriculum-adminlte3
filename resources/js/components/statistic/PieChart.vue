<template :id="this.id "
          class="p-2">
    <div class="card">
        <div class="w-full flex-1 p-2">
            {{ this.title }}
            <Doughnut
                :chart-options="chartOptions"
                :chart-data="chartData"
                :chart-id="chartId"
                :dataset-id-key="datasetIdKey"
                :plugins="plugins"
                :css-classes="cssClasses"
                :styles="styles"
                :width="width"
                :height="height"
            />
            <input type="search"
                   class="form-control form-control-sm"
                   style="border:0;"
                   :placeholder="trans('global.search')+'...'"
                   v-model="search">
        </div>
        <div class="card-footer bg-light p-0"
             style="max-height:225px; overflow-y: auto">
            <ul class="nav nav-pills flex-column">
                <li v-for="item in chart_data"
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
        <div class="card-footer bg-light p-0">
            <ul class="nav nav-pills flex-column">
                <li class="nav-item text-bold">
                    <span class="nav-link">
                        {{ trans('global.sum') }}
                        <span class="float-right">
                        {{total}}</span>
                    </span>
                </li>
            </ul>
        </div>
    </div>
</template>
<script>
import { Doughnut } from 'vue-chartjs/legacy'

import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    CategoryScale
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale)

export default {
    name: 'DoughnutChart',
    components: {
        Doughnut
    },
    props: {
        'id' : String,
        'title': String,
        'chart': String,
        'date_begin': String,
        'date_end': String,
        chartId: {
            type: String,
            default: 'doughnut-chart'
        },
        datasetIdKey: {
            type: String,
            default: 'label'
        },
        width: {
            type: Number,
            default: 400
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
            default: () => {}
        },
        plugins: {
            type: Array,
            default: () => []
        }
    },

    data() {
        return {
            search: '',
            chartData: {
                labels: [],
                datasets: [
                    {
                        backgroundColor: ["#001219","#005f73","#0a9396","#94d2bd","#e9d8a6","#ee9b00","#ca6702","#bb3e03","#ae2012","#9b2226", "#ff5400","#ff6d00","#ff8500","#ff9100","#ff9e00","#00b4d8","#0096c7","#0077b6","#023e8a","#03045e"],
                        data: []
                    }
                ]
            },
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false,
                borderRadius: 10,
            },
            legend: {
                onHover: this.handleHover,
                onLeave: this.handleLeave
            },
            chart_data: [],
        }
    },
    methods: {
        handleHover(evt, item, legend) {
            legend.chart.data.datasets[0].backgroundColor.forEach((color, index, colors) => {
                colors[index] = index === item.index || color.length === 9 ? color : color + '4D';
            });
            legend.chart.update();
        },
        handleLeave(evt, item, legend) {
            legend.chart.data.datasets[0].backgroundColor.forEach((color, index, colors) => {
                colors[index] = color.length === 9 ? color.slice(0, -2) : color;
            });
            legend.chart.update();
        },
        loaderEvent() {
            axios.get('/statistics?chart=' + this.chart + '&date_begin=' + this.date_begin + '&date_end=' + this.date_end)
                .then(response => {

                    this.chartData = {
                        labels: response.data.message.map(m => m.value), //['VueJs', 'EmberJs', 'ReactJs', 'AngularJs'],
                        datasets: [
                            {
                                data: response.data.message.map(m => m.counter)
                            }
                        ]
                    };
                    this.chart_data = this.sumIfEqual(response.data.message);

                }).catch(e => {
                    console.log(e);
                });
        },
        sumIfEqual(data){
            let obj     = data;
            var holder  = {};

            obj.forEach(function(d) {
                if (holder.hasOwnProperty(d.value)) {
                    holder[d.value] = parseInt(holder[d.value]) + parseInt(d.counter);
                } else {
                    holder[d.value] = parseInt(d.counter);
                }
            });

            var obj2 = [];

            for (var prop in holder) {
                obj2.push({ value: prop, counter: holder[prop] });
            }

            obj2.sort((a,b) => (a.counter < b.counter) ? 1 : ((b.counter < a.counter) ? -1 : 0)) //sort by counter (desc)

            return obj2;
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
        total: function(){

            let total = [];
            Object.entries(this.chart_data).forEach(([key, val]) => {
                total.push(val.counter) // the value of the current key.
            });

            return total.reduce(function(total, num){ return Number(total) + Number(num) }, 0);
        }
    },
    mounted() {
        this.loaderEvent();
    }
}
</script>
