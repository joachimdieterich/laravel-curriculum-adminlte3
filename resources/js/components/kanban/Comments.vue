<template>
    <div
        :id="'comments_' + model.id"
        class="comments px-3 border-top collapse"
    >
        <div
            ref="scroll_container"
            style="max-height: 300px;"
            class="hide-scrollbars overflow-auto pt-3"
        >
            <div v-for="comment in comments"
                class="direct-chat-msg"
                :class="{ 'right': comment.user_id == $userId }"
            >
                <div class="direct-chat-infos clearfix">
                    <span
                        class="direct-chat-name"
                        :class="{ 'float-left': comment.user_id != $userId, 'float-right': comment.user_id ==  $userId }"
                    >
                        {{ comment.user.firstname }} {{ comment.user.lastname }}
                    </span>
                    <span
                        class="direct-chat-timestamp"
                        :class="{ 'float-left': comment.user_id == $userId,'float-right': comment.user_id !=  $userId }"
                    >
                        {{ comment.created_at }}
                    </span>
                </div>
                <div
                    class="d-flex"
                    :class="comment.user_id == $userId ? 'flex-row-reverse' : 'flex-row'"
                >
                    <img v-if="comment.user.medium_id != null"
                        class="direct-chat-img"
                        :src="'/media/' + comment.user.medium_id"
                        alt="User profile picture"
                    />
                    <avatar v-else
                        data-toggle="tooltip"
                        :title="comment.user.username"
                        :username="comment.user.username"
                        :firstname="comment.user.firstname"
                        :lastname="comment.user.lastname"
                        :size="40"
                    />
                    <div
                        class="direct-chat-text flex-fill"
                        @mouseover="hover = comment.id"
                        @mouseleave="hover = false"
                    >
                        <div class="d-flex align-items-center pull-right">
                            <a v-if="$userId == comment.user.id
                                    || $userId == model.owner_id
                                    || checkPermission('is_admin')
                                "
                                v-permission="'message_delete'"
                                class="btn btn-flat text-danger px-2 py-1 mr-1 invisible"
                                @click="deleteComment(comment)"
                            >
                                <i class="fa fa-trash"></i>
                            </a>
                            <Reaction
                                :model="comment"
                                :websocket="websocket"
                                class="pull-right"
                                reaction="like"
                                url="/kanbanItemComments"
                            />
                        </div>
                        <small>{{ comment.comment }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="input-group pb-3" v-if="commentable">
            <input
                type="text"
                name="message"
                class="form-control"
                v-model.trim="form.comment"
                :placeholder="trans('global.comment') + '...'"
                @keyup.enter="sendComment()"
            />
            <span class="input-group-append">
                <button
                    class="btn btn-primary"
                    :disabled="!form.comment"
                    @click.prevent="sendComment()"
                >
                    <i class="far fa-paper-plane"></i>
                </button>
            </span>
        </div>
    </div>
</template>
<script>
import Form from 'form-backend-validation';
import Avatar from "../uiElements/Avatar.vue"
import Reaction from "../reaction/Reaction.vue";

export default {
    name: 'Comments',
    components: {
        Avatar,
        Reaction,
    },
    props: {
        comments: {
            type: Array,
            default: null,
        },
        model: {
            type: Object,
            default: null,
        },
        websocket: {
            type: Boolean,
            default: false,
        },
        commentable: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            hover: false,
            form: new Form({
                model_id: this.model.id,
                comment: '',
            }),
            scrollHeight: this.$refs.scroll_container
        }
    },
    methods: {
        sendComment() {
            if (this.form.comment.trim().length === 0) return;

            axios.post('/kanbanItemComment', this.form)
                .then(res => {
                    this.startWebsocket(res.data.id);
                    this.scrollDown();
                    this.form.comment = '';
                })
                .catch(err => function () {
                    console.log(err);
                });
        },
        deleteComment(comment) {
            axios.delete('/kanbanItemComment/' + comment.id)
                .then(res => {
                    this.stopWebsocket(comment.id);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        scrollDown() {
            this.$nextTick(() => {
                this.$refs.scroll_container.scrollTop = this.$refs.scroll_container.scrollHeight;
            });
        },
        startWebsockets() {
            if (this.websocket === true) {
                this.comments.forEach((comment) => {
                    this.startWebsocket(comment.id);
                });
            }
        },
        startWebsocket(id) {
            this.$echo
                .join('App.KanbanItemComment.' + id)
                .listen('.KanbanItemCommentUpdated', (payload) => {
                    const index = this.comments.findIndex(s => s.id === payload.model.id);
                    this.comments[index] = payload.model;
                })
                .listen('.KanbanItemCommentDeleted', (payload) => {
                    let index = this.comments.indexOf(payload.model);
                    this.item.comments.splice(index, 1);
                    this.stopWebsocket(payload.model.id);
                });
        },
        stopWebsockets() {
            if (this.websocket === true) {
                this.comments.forEach((comment) => {
                    this.stopWebsocket(comment.id);
                });
            }
        },
        stopWebsocket(id) {
            this.$echo.leave('App.KanbanItemComment.' + id);
        },

    },
    mounted() {
        this.comments.forEach(() => {
            this.$refs.scroll_container.scrollTop = this.$refs.scroll_container.scrollTop + 120
        });
    },
    unmounted() {
        this.stopWebsockets();
    },
    created() {
        this.startWebsockets();
    },
    watch: {
        comments:{
            handler() {
                this.scrollDown();
            },
        }
    }
}
</script>
<style scoped>
.direct-chat-text:hover .text-danger { visibility: visible !important; }
</style>
