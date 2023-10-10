<template>
    <date-picker v-model="time" v-bind="$attrs"></date-picker>
</template>
<script>
import Datepicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

export default {
    data() {
        return {
            time: this.value,
        }
    },
    props: {
        value: '',
    },
    components: {
        Datepicker
    }
}
</script>