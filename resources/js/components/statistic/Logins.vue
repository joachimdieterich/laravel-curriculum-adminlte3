<template :id="this.id">
    <div v-if="show"
        class="card"
    >
        <div class="p-2">
            <Line
                :options="chartOptions"
                :data="chartDataLogin"
                id="loginChart"
                :dataset-id-key="datasetIdKey"
                :plugins="plugins"
                :css-classes="cssClasses"
                :styles="styles"
                :width="width"
                :height="height"
            />
        </div>
        <div class="p-2">
            <Line
                :options="chartOptions"
                :data="chartDataSso"
                id="ssoChart"
                :dataset-id-key="datasetIdKey"
                :plugins="plugins"
                :css-classes="cssClasses"
                :styles="styles"
                :width="width"
                :height="height"
            />
        </div>
        <div class="p-2">
            <Line
                :options="chartOptions"
                :data="chartDataGuest"
                id="guestChart"
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
import { Line } from 'vue-chartjs';

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    LineElement,
    LinearScale,
    CategoryScale,
    PointElement
);

export default {
    name: 'LoginCharts',
    props: {
        date_begin: {
            type: String,
            required: true,
        },
        date_end: {
            type: String,
            required: true,
        },
        chartId: {
            type: String,
            default: 'line-chart',
        },
        datasetIdKey: {
            type: String,
            default: 'label',
        },
        width: {
            type: Number,
            default: 400,
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
        plugins: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            chartDataSso: {
                labels: [],
                datasets: [
                    {
                        label: '',
                        backgroundColor: '#001219',
                        data: [],
                    },
                ],
            },
            chartDataLogin: {
                labels: [],
                datasets: [
                    {
                        label: '',
                        backgroundColor: '#005f73',
                        data: [],
                    },
                ],
            },
            chartDataGuest: {
                labels: [],
                datasets: [
                    {
                        label: '',
                        backgroundColor: '#0a9396',
                        data: [],
                    },
                ],
            },
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false,
            },
            show: false,
        };
    },
    methods: {
        loadAll() {
            this.show = false;
            this.loaderEvent('login');
            this.loaderEvent('guestLogin');
            this.loaderEvent('ssoLogin');
            this.show = true;
        },
        loaderEvent(chart) {
            axios.get('/statistics?chart=' + chart + '&date_begin=' + this.date_begin + '&date_end=' + this.date_end)
                .then(response => {
                    let result = {
                        labels: response.data.labels,
                        datasets: [
                            {
                                label: response.data.datasets.label,
                                backgroundColor: response.data.datasets.backgroundColor,
                                data: response.data.datasets.data,
                            },
                        ],
                    };
                    if (chart === 'login') {
                        this.chartDataLogin = result;
                    }
                    if (chart === 'ssoLogin') {
                        this.chartDataSso = result;
                    }
                    if (chart === 'guestLogin') {
                        this.chartDataGuest = result;
                    }

                }).catch(e => {
                    console.log(e);
                });
        },
    },
    watch: {
        date_begin: {
            handler: function() {
                this.loadAll();
            },
        },
        date_end: {
            handler: function() {
                this.loadAll();
            },
        },
    },
    mounted() {
        this.loadAll();
    },
    components: {
        Line,
    },
};
</script>