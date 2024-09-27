<template>
    <span :title="fullname">
        <span v-if="label">
            <div class="user-block pr-2"
                :class="css">
                    <img v-if="typeof avatar_medium_id === 'number'"
                         class="img-circle img-bordered-sm"
                         :style="'width:'+size+' !important;height:'+size+' !important;'"
                         :src="'/media/'+avatar_medium_id"/>
                    <canvas
                        v-else
                        class="img-circle"
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
        <span v-else :style="'width:'+size+'px !important; height:'+size+'px !important;'">
            <img v-if="typeof avatar_medium_id === 'number'"
                 class="direct-chat-img"
                 :class="css"
                 :style="'width:'+size+'px !important; height:'+size+'px !important; float:none !important'"
                 :src="'/media/'+avatar_medium_id"/>
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
            medium_id: null
        },
        data() {
            return {
                component_id: this.$.uid,
                id: this.$.uid,
                avatar_medium_id: null,
                colours: ["#1abc9c", "#2ecc71", "#3498db", "#9b59b6", "#34495e", "#16a085", "#27ae60", "#2980b9", "#8e44ad", "#2c3e50", "#f1c40f", "#e67e22", "#e74c3c", "#95a5a6", "#f39c12", "#d35400", "#c0392b", "#bdc3c7", "#7f8c8d"],
                user: null,
                fullname: '',
                errors: null
            };
        },

        methods: {
            setData(){
                let initials = "";
                if(this.firstname){
                    initials = this.firstname[0].charAt(0).toUpperCase() + this.lastname[0].charAt(0).toUpperCase();
                    this.fullname = this.firstname[0] +' ' + this.lastname[0];
                }
                else if(this.username){
                    initials = this.username[0].charAt(0).toUpperCase();
                }
                else if (this.user){
                    initials = this.user.firstname.charAt(0).toUpperCase() + this.user.lastname.charAt(0).toUpperCase();
                    this.fullname = this.user.firstname +' ' + this.user.lastname;
                    /*if (this.user.medium_id){ //todo: top position of images are wrong
                        this.avatar_medium_id = this.user.medium_id;
                    }*/
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
                    context.fillRect (0, 0, this.size, this.size);
                    context.font = (this.size/2.5) + "px Arial";
                    context.textAlign = "center";
                    context.fillStyle = "#FFF";
                    context.fillText(initials, canvasCssWidth / 2, canvasCssHeight / 1.495);
                });
            }
        },
        mounted() {
            this.id = 'user-avatar' + this.$.uid;
            this.avatar_medium_id =  this.medium_id;

            if (this.user_id == null  && this.medium_id  == null || this.user_id === undefined) {
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

    }
</script>
