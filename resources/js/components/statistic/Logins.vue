<template>
    <div class="card">
        <div class="w-full flex-1 p-2">
            Registered Users (local)
            <D3LineChart :config="chart_config" :datum="chart_data_login"></D3LineChart>
            Registered Users (sso)
            <D3LineChart :config="chart_config" :datum="chart_data_ssoLogin"></D3LineChart>
            Guests
            <D3LineChart :config="chart_config" :datum="chart_data_guestLogin"></D3LineChart>
        </div>
    </div>
</template>
<script>
import { D3LineChart } from 'vue-d3-charts'; // documentation https://saigesp.github.io/vue-d3-charts/#/linechart

export default {
    components: {
        D3LineChart,
    },
    props: {
        'date': String,
    },
    data() {
        return {
            chart_data_login: [],
            chart_data_ssoLogin: [],
            chart_data_guestLogin: [],
           /* chart_data: [
                {hours: 238, production: 134, date: 2000},
                {hours: 938, production: 478, date: 2001},

            ],*/
            chart_config: {
                date: {
                    key: 'created_at',
                    inputFormat: "%Y-%m-%d",
                    outputFormat: "%d-%m-%Y",
                },
                values: ["counter"],
                axis: {
                    yTitle: "Logins",
                    xTitle: "Datum",
                    yFormat: ".0f",
                    xFormat: "%Y-%m-%d",
                    yTicks: 5,
                    xTicks: 3
                },
                color: {
                    key: false,
                    keys: false,
                    scheme: [ '#7D5BA6','#ACFCD9', '#DDDDDD', '#FC6471','#55D6BE'],
                    current: "#1f77b4",
                    default: "#AAA",
                    axis: "#000",
                },
                margin: {
                    top: 20,
                    right: 20,
                    bottom: 20,
                    left: 40
                },
                points: {
                    visibleSize: 3,
                    hoverSize: 6,
                },
                tooltip: {
                    labels: ['counter']
                },
                transition: {
                    duration: 350,
                    ease: "easeLinear",
                },
                curve: "curveMonotoneX" //options: http://bl.ocks.org/d3indepth/b6d4845973089bc1012dec1674d3aff8
            },
            count: 2010,
        }
    },
    methods: {
        loaderEvent(chart) {
            axios.get('/statistics?chart='+chart)
                .then(response => {
                    if (chart === 'login'){
                        this.chart_data_login = response.data.message;
                    }
                    if (chart === 'ssoLogin'){
                        this.chart_data_ssoLogin = response.data.message;
                    }
                    if (chart === 'guestLogin'){
                        this.chart_data_guestLogin = response.data.message;
                    }

                }).catch(e => {

            });
        }
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
    mounted() {
        this.loaderEvent('login');
        this.loaderEvent('guestLogin');
        this.loaderEvent('ssoLogin');
    }
}
</script>

