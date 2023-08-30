<template>
    <svg :id="id" viewBox="0 0 200 40">
        <path d="M 0 40 H 200"
              :fill="fill"
              :stroke-width="stroke_width"
              :stroke="color"
        />
    </svg>
</template>

<script>
import * as d3 from "d3";

export default {
    name: "SparkLine",
    props: {
        data: {
            type: Array,
            default() {
                return [0, 0];
            }
        },
        stroke_width: {
            type: Number,
            default: 4
        },
        color: {
            type: String,
            default: '#007bff'
        },
        fill: {
            type: String,
            default: 'none'
        }
    },
    data() {
        return {
            id: 'chart-' + this._uid
        };
    },
    watch: {
        data() {
            this.plot();
        }
    },
    methods: {
        plot() {
            this.x.domain([0, this.data.length - 1]);
            this.y.domain(d3.extent(this.data));
            this.chart.select("path").attr("d", this.line(this.data));
        }
    },
    mounted() {
        this.chart = d3.select(`#${this.id}`);

        this.x = d3.scaleLinear().range([0, 200]);

        this.y = d3.scaleLinear().range([40, 0]);

        this.line = d3
            .line()
            .x((d, i) => this.x(i))
            .y(d => this.y(d));

        this.plot();
    }
};
</script>
