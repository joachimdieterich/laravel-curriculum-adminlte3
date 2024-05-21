<template>
    <date-picker v-model="time" v-bind="$attrs"></date-picker>
</template>
<script>
import Datepicker from 'vue3-datepicker';

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
