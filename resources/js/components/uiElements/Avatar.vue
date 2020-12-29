<template>
    <canvas
        :id="id"
        style="border-radius: 50%;"
        :width="size"
        :height="size"
    >
    </canvas>
</template>

<script>

    export default {
        props: {
            firstname: '',
            lastname: '',
            size: {
                default: 60
            },
        },
        data() {
            return {
                id: null,
                colours: ["#1abc9c", "#2ecc71", "#3498db", "#9b59b6", "#34495e", "#16a085", "#27ae60", "#2980b9", "#8e44ad", "#2c3e50", "#f1c40f", "#e67e22", "#e74c3c", "#95a5a6", "#f39c12", "#d35400", "#c0392b", "#bdc3c7", "#7f8c8d"],
                errors: null
            };
        },

        methods: {

        },
        mounted() {
            this.id = 'user-avatar'+this._uid;

            let initials = this.firstname[0].charAt(0).toUpperCase() + this.lastname[1].charAt(0).toUpperCase();

            let charIndex = initials.charCodeAt(0) - 65;
            let colourIndex = charIndex % 19;
            this.$nextTick(() => {
                let canvas = document.getElementById(this.id);
                let context = canvas.getContext("2d");

                let canvasWidth = this.size,
                    canvasHeight = this.size,
                    canvasCssWidth = canvasWidth,
                    canvasCssHeight = canvasHeight;

                if (window.devicePixelRatio) {
                    $(canvas).attr("width", canvasWidth * window.devicePixelRatio);
                    $(canvas).attr("height", canvasHeight * window.devicePixelRatio);
                    $(canvas).css("width", canvasCssWidth);
                    $(canvas).css("height", canvasCssHeight);
                    context.scale(window.devicePixelRatio, window.devicePixelRatio);
                }

                context.fillStyle = this.colours[colourIndex];
                context.fillRect (0, 0, this.size, this.size);
                context.font = (this.size/2.5)+"px Arial";
                context.textAlign = "center";
                context.fillStyle = "#FFF";
                context.fillText(initials, canvasCssWidth / 2, canvasCssHeight / 1.495);
            });
        },

    }
</script>
