<template>
    <span :style="'width: ' + size + 'px; height: ' + size + 'px; display: inline-block;'">
        <div
            @mouseenter="showPopupDetails ? { mouseenter: entered($event) } : {}"
            @mouseleave="showPopupDetails ? { mouseleave: left() } : {}"
            @mousemove="showPopupDetails ? { mousemove: movement($event) } : {}"
            @touchstart="showPopupDetails ? { touchstart: entered($event) } : {}"
            @touchend="showPopupDetails ? { touchend: left() } : {}"
            @touchmove="showPopupDetails ? { touchmove: movement($event) } : {}"
            :style="'width:' + size + 'px; height:' + size + 'px;'"
        >
            <img v-if="medium !== null"
                 class="img-circle img-bordered-sm"
                 :width="size"
                 :height="size"
                 :src="medium"
            />
            <img v-else-if="typeof avatar_medium_id === 'number'"
                 class="direct-chat-img"
                 :class="css"
                 :style="'width:' + size + 'px; height:' + size + 'px; float:none !important'"
                 :src="'/media/' + avatar_medium_id"
            />
            <canvas v-else
                    :id="id"
                    :class="css"
                    style="border-radius: 50%;"
                    :width="size"
                    :height="size"
            ></canvas>
            <div v-if="details.show"
                 class="rounded-sm details"
                 :style="position"
            >
                {{ firstname }} {{ lastname }}
            </div>
        </div>
    </span>
</template>
<style scoped>
.details {
    cursor: default;
    position: fixed;
    font-weight: normal;
    padding: 0.5rem;
    z-index: 9999;
    color: black !important;
    border-radius: 0.3rem;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    background: white;
}
</style>
<script>
export default {
    props: {
        user_id: {
            type: Number,
            default: null,
        },
        css: {
            type: String,
            default: null,
        },
        firstname: {
            type: String,
            default: null,
        },
        lastname: {
            type: String,
            default: null,
        },
        username: {
            type: String,
            default: null,
        },
        size: {
            type: Number,
            default: 60,
        },
        medium_id: {
            type: Number,
            default: null,
        },
        medium: {
            type: String,
            default: null,
            title: "The image itself. Best base64-format."
        },
        showPopupDetails: {
            type: Boolean,
            default: true,
            title: "Controls if the Popup with user information should be displayed"
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            id: this.$.uid,
            avatar_medium_id: null,
            colours: ["#1abc9c", "#2ecc71", "#3498db", "#9b59b6", "#34495e", "#16a085", "#27ae60", "#2980b9", "#8e44ad", "#2c3e50", "#f1c40f", "#e67e22", "#e74c3c", "#95a5a6", "#f39c12", "#d35400", "#c0392b", "#bdc3c7", "#7f8c8d"],
            user: null,
            details: {
                key: 0,
                show: false,
                posX: 0,
                posY: 0
            },
        };
    },
    methods: {
        entered(e) {
            this.details.key = this.user_id;
            this.details.show = true;

            this.movement(e);
        },
        left() {
            this.details.key = 0;
            this.details.show = false;
        },
        movement(e) {
            let x = e.x;
            let y = e.y;

            if (e.targetTouches) {
                x = e.targetTouches[0].clientX;
                y = e.targetTouches[0].clientY;
            }
            this.details.posX = x - 80;
            this.details.posY = y - 80;
        },
        drawCanvas() {
            let initials = "";

            if (this.firstname && this.lastname) {
                initials = (this.firstname[0].charAt(0) + this.lastname[0].charAt(0)).toUpperCase();
            } else if (this.username) {
                initials = this.username[0].charAt(0).toUpperCase();
            } else if (this.user) {
                initials = (this.user.firstname.charAt(0) + this.user.lastname.charAt(0)).toUpperCase();
            }

            let charIndex = initials.charCodeAt(0) - 65;
            let colourIndex = charIndex % 19;

            this.$nextTick(() => {
                let canvas = document.getElementById(this.id);
                let context = canvas?.getContext("2d");
                context.clearRect(0, 0, canvas.width, canvas.height);

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
                context.fillRect(0, 0, this.size, this.size);
                context.font = (this.size / 2.5) + "px Arial";
                context.textAlign = "center";
                context.fillStyle = "#FFF";
                context.fillText(initials, canvasCssWidth / 2, canvasCssHeight / 1.495);
            });
        },
    },
    watch: {
        user_id: function() {
            this.drawCanvas();
        },
        firstname: function() {
            this.drawCanvas();
        },
        lastname: function() {
            this.drawCanvas();
        },
    },
    mounted() {
        this.id = 'user-avatar' + this.$.uid;
        this.avatar_medium_id = this.medium_id;

        if (this.user_id == null && this.medium_id == null && this.medium == null) {
            this.drawCanvas();
        } else {
            axios.get('/users/' + this.user_id)
                .then(response => {
                    this.user = response.data.user;
                    this.drawCanvas();
                })
                .catch(e => {
                    console.log(e);
                });
        }
    },
    computed: {
        position() {
            return {
                top: this.details.posY + 'px',
                left: this.details.posX + 'px',
            };
        },
    },
}
</script>