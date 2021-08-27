<template :id="{{ this.id }}"
          class="p-2">
    <div class="card">
        <div class="w-full flex-1 p-2">
            {{ this.title }}
            <D3PieChart
                class="p-6"
                :config="chart_config"
                :datum="chart_data"
            ></D3PieChart>
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
import { D3PieChart } from 'vue-d3-charts'; //documentation https://saigesp.github.io/vue-d3-charts/#/piechart

export default {
    components: {
        D3PieChart
    },
    props: {
        'id' : String,
        'title': String,
        'chart': String,
        'date_begin': String,
        'date_end': String,
    },
    data() {
        return {
            search: '',
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
            axios.get('/statistics?chart=' + this.chart + '&date_begin=' + this.date_begin + '&date_end=' + this.date_end)
                .then(response => {
                    this.chart_data = this.sumIfEqual(response.data.message);
                }).catch(e => {
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
