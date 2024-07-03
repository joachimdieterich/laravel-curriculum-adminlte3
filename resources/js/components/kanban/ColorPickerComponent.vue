<template>
    <v-swatches v-model="hex_color" @input="updateColor"></v-swatches>
</template>

<script>
const VSwatches =
    () => import('vue-swatches');
//import VSwatches from 'vue-swatches'

// Import the styles too, globally
import "vue-swatches/dist/vue-swatches.css"

export default {
    props: ['id'],
    components: {VSwatches},
    name: "ColorPickerComponent.vue",
    data() {
        return {
            hex_color: '#F4F4F4',
            rgba_color: null
        }
    },
    methods: {
        getColor() {
            if(this.id){
                axios.get('/kanbans/getColor/' + this.id).then(res => {
                    this.hex_color = res.data.hex
                    this.rgba_color = res.data.rgba
                    $(function () {
                        $("#kanban_board_wrapper").css("background-color", res.data.rgba);
                    });
                })
            }
        },
        updateColor(value) {
            let that = this;
            axios.post('/kanbans/setColor', {
                'id': this.id,
                'color': value
            }).then(
                function(){
                    that.getColor();
                    that.$emit('change');
                }
            );
        }
    },
    mounted() {
        this.getColor()
    },
}
</script>

<style>
.vue-swatches__trigger {
    width: 19px !important;
    height: 19px !important;
}

</style>
