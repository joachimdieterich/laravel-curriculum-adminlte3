<template>
    <span>
        <span v-if="label">
            <div
                class="user-block pr-2"
                :class="css"
            >
                <img v-if="typeof avatar_medium_id === 'number'"
                     class="img-circle img-bordered-sm"
                     :style="'width:' + size + ' !important;height:' + size + ' !important;'"
                     :src="'/media/' + avatar_medium_id"
                />
                <canvas v-else
                        class="img-circle"
                        :id="id"
                        style="border-radius: 50%;"
                        :width="size"
                        :height="size"
                ></canvas>
                <span class="username">
                    <a href="#">{{ title }}</a>
                </span>
                <span class="description">{{ subtitle }}</span>
            </div>
        </span>
        <div v-else
             @mouseenter="contributorDetailsEntered(user_id, $event)"
             @mouseleave="contributorDetailsLeft"
             @mousemove="contributorDetailsMovement"
             @touchstart="contributorDetailsEntered(user_id, $event);"
             @touchend="contributorDetailsLeft"
             @touchmove="contributorDetailsMovement"
             :style="'width:' + size + 'px; height:' + size + 'px;'"
        >
            <img v-if="typeof avatar_medium_id === 'number'"
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
            <div v-show="contributorDetails.show && contributorDetails.key === user_id"
                 class="rounded-sm contributor-details"
                 :style="{top: contributorDetailsTopStyle + 'px', left: contributorDetailsLeftStyle + 'px'}"
            >
                {{ firstname }} {{ lastname }}
            </div>
        </div>
    </span>
</template>
<style scoped>
.contributor-details {
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
        label: {
            type: Boolean,
            default: false,
        },
        css: {
            type: String,
            default: null,
        },
        title: {
            type: String,
            default: null,
        },
        subtitle: {
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
    },
    data() {
        return {
            component_id: this.$.uid,
            id: this.$.uid,
            avatar_medium_id: null,
            colours: ["#1abc9c", "#2ecc71", "#3498db", "#9b59b6", "#34495e", "#16a085", "#27ae60", "#2980b9", "#8e44ad", "#2c3e50", "#f1c40f", "#e67e22", "#e74c3c", "#95a5a6", "#f39c12", "#d35400", "#c0392b", "#bdc3c7", "#7f8c8d"],
            user: null,
            contributorDetails: {
                key: 0,
                show: false,
                posX: 0,
                posY: 0
            },
            contributorStyles: {}
        };
    },
    methods: {
        contributorDetailsEntered: function (contributorKey, e) {
            this.contributorDetails.key = contributorKey;
            this.contributorDetails.show = true;

            this.contributorDetailsMovement(e);
        },
        contributorDetailsLeft: function () {
            this.contributorDetails.key = 0;
            this.contributorDetails.show = false;
        },
        contributorDetailsMovement: function (e) {
            let x = e.x;
            let y = e.y;

            if (e.targetTouches) {
                x = e.targetTouches[0].clientX;
                y = e.targetTouches[0].clientY;
            }
            this.contributorDetails.posX = x - 80;
            this.contributorDetails.posY = y - 80;
        },
        setData() {
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
        }
    },
    mounted() {
        this.id = 'user-avatar' + this.$.uid;
        this.avatar_medium_id = this.medium_id;

        if (this.user_id == null && this.medium_id == null) {
            this.setData();
        } else {
            axios.get('/users/' + this.user_id)
                .then(response => {
                    this.user = response.data.user;
                    this.setData();
                })
                .catch(e => {
                    console.log(e);
                });
        }
    },
    computed: {
        contributorDetailsLeftStyle() {
            return this.contributorDetails.posX
        },
        contributorDetailsTopStyle() {
            return this.contributorDetails.posY
        },
    }
}
</script>
