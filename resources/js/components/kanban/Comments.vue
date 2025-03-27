<template>
    <div class="comments p-3 border-top">
        <div
            :id="'comments_of_model_id_' + model.id"
            ref="scroll_container"
            style="max-height: 300px;"
            class="hide-scrollbars overflow-auto"
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
                <img v-if="comment.user.medium_id != null"
                    class="direct-chat-img"
                    :class="(comment.user_id != $userId) ? 'pull-left' : 'pull-right'"
                    :src="'/media/' + comment.user.medium_id"
                    alt="User profile picture"
                />
                <avatar v-else
                    data-toggle="tooltip"
                    :class="(comment.user_id != $userId) ? 'pull-left' : 'pull-right'"
                    :title="comment.user.username"
                    :username="comment.user.username"
                    :firstname="comment.user.firstname"
                    :lastname="comment.user.lastname"
                    :size="40"
                />
                <div
                    class="direct-chat-text"
                    @mouseover="hover = comment.id"
                    @mouseleave="hover = false"
                >
                    <Reaction
                        :model="comment"
                        class="pull-right"
                        reaction="like"
                        url="/kanbanItemComments"
                    />
                    <i v-if="($userId == comment.user.id && hover == comment.id)
                            || ($userId == kanban_owner_id && hover == comment.id)
                        "
                        v-permission="'message_delete'"
                        class="text-danger pull-right p-1 mr-1 fa fa-trash pointer"
                        @click="deleteComment(comment)"
                    ></i>
                    <small>{{ comment.comment }}</small>
                </div>
            </div>
        </div>

        <div class="input-group">
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
        kanban_owner_id: {
            type: Number,
            default: null,
        },
    },
    data() {
        return {
            hover: false,
            form: new Form({
                model_id: this.model.id,
                comment: '',
            }),
        }
    },
    methods: {
        sendComment() {
            if (this.form.comment.trim().length === 0) return;

            axios.post('/kanbanItemComment', this.form)
                .then(res => {
                    this.$emit('addComment', res.data);
                    this.form.comment = '';
                    this.$nextTick(function() {
                        let container = this.$refs.scroll_container;
                        container.scrollTop = container.scrollHeight + 120;
                    });
                })
                .catch(err => function () {
                    console.log(err);
                });
        },
        deleteComment(comment) {
            axios.delete('/kanbanItemComment/' + comment.id)
                .then(res => {
                    this.$emit('removeComment', comment);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
    },
    mounted() {
        this.conversation = this.comments;
    },
}
</script>