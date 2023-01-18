<template>
    <span>
        <span v-if="label">
            <div class="user-block pr-2"
                :class="css">
                    <img v-if="typeof medium_id === 'number'"
                         class="img-circle img-bordered-sm pull-left"
                         :style="'width:'+size+' !important;height:'+size+' !important;'"
                         :src="'/media/'+medium_id"/>
                    <canvas
                        v-else
                        class="img-circle img-bordered-sm  pull-left"
                        :id="id"
                        style="border-radius: 50%;"
                        :width="size"
                        :height="size">
                    </canvas>

                <span class="username">
                    <a href="#">{{ this.title }}</a>
                </span>
                <span class="description">{{ this.subtitle }}</span>
            </div>

        </span>
        <span v-else>
            <img v-if="typeof medium_id === 'number'"
                 class="direct-chat-img"
                 :class="css"
                 :style="'width:'+size+' !important;height:'+size+' !important;'"
                 :src="'/media/'+medium_id"/>
            <canvas
                v-else
                :id="id"
                :class="css"
                style="border-radius: 50%;"
                :width="size"
                :height="size">
            </canvas>
        </span>
    </span>
</template>

<script>

    export default {
        props: {
            user_id: null,
            label: false,
            css: {
                type: String,
                default: ''
            },
            title: '',
            subtitle: '',
            firstname: '',
            lastname: '',
            username: '',
            size: {
                default: 60
            },
            medium_id: ''
        },
        data() {
            return {
                id: null,
                colours: ["#1abc9c", "#2ecc71", "#3498db", "#9b59b6", "#34495e", "#16a085", "#27ae60", "#2980b9", "#8e44ad", "#2c3e50", "#f1c40f", "#e67e22", "#e74c3c", "#95a5a6", "#f39c12", "#d35400", "#c0392b", "#bdc3c7", "#7f8c8d"],
                user: null,
                errors: null
            };
        },

        methods: {

        },
        mounted() {
            this.id = 'user-avatar' + this._uid;
            if (this.user_id != null) {
                axios.get('/users/'+this.user_id)
                    .then(response => {
                        this.user = response.data.user;
                    })
                    .catch(e => {
                        this.errors = e.data.errors;
                    });
            }

            let initials = "";
            if(this.firstname){
                initials = this.firstname[0].charAt(0).toUpperCase() + this.lastname[0].charAt(0).toUpperCase();
            }
            else if(this.username){
                initials = this.username[0].charAt(0).toUpperCase();
            }


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
                context.font = (this.size/2.5) + "px Arial";
                context.textAlign = "center";
                context.fillStyle = "#FFF";
                context.fillText(initials, canvasCssWidth / 2, canvasCssHeight / 1.495);
            });
        },

    }
</script>
