<template>
    <div>
        <v-swatches v-model="color"></v-swatches>
        <input type="hidden" name="color" :value="color"/>
    </div>
</template>



<script>
import VSwatches from 'vue-swatches'
import "vue-swatches/dist/vue-swatches.css"


export default {
    components: {VSwatches},
    name: "ColorPickerInput",
    data() {
        return {
            color: '#F4F4F4'
        }
    },
}
</script>

<style scoped>
.vue-swatches__trigger {
    width: 19px !important;
    height: 19px !important;
}

</style>
