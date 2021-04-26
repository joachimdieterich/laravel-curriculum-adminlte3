<template class="p-2">
    <div class="card">
        <div class="w-full flex-1 p-2">
            Active Organizations
            <D3PieChart
                class="p-6"
                :config="chart_config"
                :datum="chart_data"
            ></D3PieChart>
        </div>
        <div class="card-footer bg-light p-0">
            <ul class="nav nav-pills flex-column">
                <li v-for="item in chart_data" class="nav-item">
                    <span  class="nav-link">
                        {{item.value}}
                        <span class="float-right">
                        {{item.counter}}</span>
                    </span>
                </li>
                <li class="nav-item text-bold">
                    <span class="nav-link">
                        Total
                        <span class="float-right">
                        {{total}}</span>
                    </span>
                </li>
            </ul>
        </div>
    </div>
</template>
<script>
import { D3PieChart } from 'vue-d3-charts'; //documentation https://saigesp.github.io/vue-d3-charts/#/piechart

export default {
    components: {
        D3PieChart
    },
    props: {
        'date_begin': String,
        'date_end': String,
    },
    data() {
        return {
            chart_data: [],
            chart_config: {
                key: 'value',
                value: 'counter',
                color: {scheme: 'schemeSet2'},
                radius: {
                    inner: 60,
                    padding: 0.05,
                    round: 4
                }
            },
            count: 1
        }
    },
    methods: {
        loaderEvent() {
            axios.get('/statistics?chart=organizations&date_begin=' + this.date_begin + '&date_end=' + this.date_end)
                .then(response => {
                    this.chart_data = response.data.message;
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

