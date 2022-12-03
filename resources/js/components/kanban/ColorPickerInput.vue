<template>
    <div>
        <v-swatches v-model="content" @input="handleInput" ></v-swatches>
        <input type="hidden" name="color" :value="content"/>
    </div>
</template>



<script>
import VSwatches from 'vue-swatches'
import "vue-swatches/dist/vue-swatches.css"


export default {
    components: {VSwatches},
    name: "ColorPickerInput",
    props: ["current","value"],
    methods: {
        handleInput (e) {
            this.$emit('input', this.content)
        }
    },
    data() {
        return {
            content: this.value
        }
    },
    watch: {
        content: function(){
            console.log(this.content);
        }
    }
}
</script>

<style scoped>
.vue-swatches__trigger {
    width: 19px !important;
    height: 19px !important;
    ;
}

</style>
