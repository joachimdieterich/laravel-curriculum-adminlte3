<template :id="this.id">
    <div class="card">

        <div class="w-full flex-1 p-2">
            <LineChartGenerator
                :chart-options="chartOptions"
                :chart-data="chartDataLogin"
                :chart-id="chartId"
                :dataset-id-key="datasetIdKey"
                :plugins="plugins"
                :css-classes="cssClasses"
                :styles="styles"
                :width="width"
                :height="height"
            />
            <LineChartGenerator
                :chart-options="chartOptions"
                :chart-data="chartDataSso"
                :chart-id="chartId"
                :dataset-id-key="datasetIdKey"
                :plugins="plugins"
                :css-classes="cssClasses"
                :styles="styles"
                :width="width"
                :height="height"
            />
            <LineChartGenerator
                :chart-options="chartOptions"
                :chart-data="chartDataGuest"
                :chart-id="chartId"
                :dataset-id-key="datasetIdKey"
                :plugins="plugins"
                :css-classes="cssClasses"
                :styles="styles"
                :width="width"
                :height="height"
            />
        </div>
    </div>
</template>
<script>
import { Line as LineChartGenerator } from 'vue-chartjs/legacy'
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    LinearScale,
    CategoryScale,
    PointElement
} from 'chart.js'

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    LineElement,
    LinearScale,
    CategoryScale,
    PointElement
)

export default {
    name: 'LineChart',

    props: {
        'date_begin': String,
        'date_end': String,
        chartId: {
            type: String,
            default: 'line-chart'
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
            chartDataSso: {},
            chartDataLogin: {},
            chartDataGuest: {},
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false
            }
        };
    },
    methods: {
        loadAll(){
            this.loaderEvent('login');
            this.loaderEvent('guestLogin');
            this.loaderEvent('ssoLogin');
        },
        loaderEvent(chart) {
            axios.get('/statistics?chart=' + chart + '&date_begin=' + this.date_begin + '&date_end=' + this.date_end)
                .then(response => {
                    let result = [];
                    result = {
                        labels: response.data.message.labels,
                        datasets: [
                            {
                                label: response.data.message.datasets.label,
                                backgroundColor: response.data.message.datasets.backgroundColor,
                                data: response.data.message.datasets.data
                            }
                        ]
                    }
                    if (chart === 'login'){
                        this.chartDataLogin = result;
                    }
                    if (chart === 'ssoLogin'){
                        this.chartDataSso = result;
                    }
                    if (chart === 'guestLogin'){
                        this.chartDataGuest = result;
                    }

                }).catch(e => {
                    console.log(e);
            });
        }
    },
    watch: {
        date_begin: {
            handler: function(){
                this.loadAll();
            }
        },
        date_end: {
            handler: function(){
                this.loadAll();
            }
        }
    },
    mounted() {
        this.loadAll();
    },
    components: {
        LineChartGenerator
    },

};
</script>
