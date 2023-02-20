<template>
  <div class="comments p-3 border-top" >

    <div :id="'comments_of_model_id_'+model.id"
         ref="scroll_container"
         style="max-height: 300px;"
        class="hide-scrollbars overflow-auto">
      <div
          v-for="comment in comments"
          class="direct-chat-msg"
          :class="{ 'right': comment.user_id == $userId }"
          s>
        <div class="direct-chat-infos clearfix">
            <span
                class="direct-chat-name "
                :class="{ 'float-left': comment.user_id != $userId, 'float-right': comment.user_id ==  $userId }">
                {{ comment.user.firstname }} {{ comment.user.lastname }}
            </span>
            <span
                class="direct-chat-timestamp"
                :class="{ 'float-left': comment.user_id == $userId,'float-right': comment.user_id !=  $userId }">
                {{ comment.created_at }}
            </span>
        </div>
        <img v-if="comment.user.medium_id != null"
             class="direct-chat-img"
             :class="(comment.user_id != $userId) ? 'pull-left' : 'pull-right'"
             :src="'/media/'+comment.user.medium_id"
             alt="User profile picture">
        <avatar v-else
                data-toggle="tooltip"
                :class="(comment.user_id != $userId) ? 'pull-left' : 'pull-right'"
                :title="comment.user.username"
                :username="comment.user.username"
                :firstname="comment.user.firstname"
                :lastname="comment.user.lastname"
                :size="40"
        ></avatar>
        <div class="direct-chat-text"
             @mouseover="hover = comment.id"
             @mouseleave="hover = false">
            <Reaction
                :model="comment"
                class="pull-right"
                reaction="like"
                url="/kanbanItemComments"
            />
          <i v-if="$userId == comment.user.id && hover == comment.id"
             class="text-danger pull-right p-1 mr-1 fa fa-trash pointer"
             @click="deleteComment(comment.id)"></i>
          <i v-else-if="hover == comment.id"
             v-can="'message_delete'"
             class="text-danger pull-right p-1 mr-1 fa fa-trash pointer"
             @click="deleteComment(comment.id)"></i>
            <small>{{ comment.comment }}</small>

        </div>
      </div>
    </div>


    <form action="#" method="post">
        <div class="input-group">
            <input type="text"
                   name="message"
                   v-model="form.comment"
                   :placeholder="trans('global.comment')+'...'"
                   class="form-control">
            <span class="input-group-append">
                  <button class="btn btn-primary "
                          @keyup.enter="sendComment()"
                          @click.prevent="sendComment()">
                      <i class="far fa-paper-plane"></i>
                  </button>
                </span>
        </div>
    </form>

  </div>
</template>
<script>
import Avatar from "../uiElements/Avatar"
import Form from 'form-backend-validation';
import Reaction from "../reaction/Reaction";

export default {
    name: 'Comments',
    components: {
        Avatar,
        Reaction
    },
    props: {
        comments: {},
        model: {},
        url: String
    },
    data() {
        return {
            new_comment: '',
            hover: false,
            form: new Form({
                'model_id': this.model.id,
                'comment': '',
            }),
        }
    },
    methods: {
        sendComment() {
            this.form.submit('post', this.url)
                .then(res => {
                    this.comments = res.data.data;
                })
                .catch(response => function () {
                    console.log(err.response);
                });
        },
        deleteComment(id){
            axios.delete(this.url + "/" + id)
                .then(res => { // Tell the parent component we've added a new task and include it
                    this.comments = res.data.data;
                })
                .catch(err => {
                    console.log(err.response);
                });

        },
    },
    watch: {
        comments: function() {
            this.$nextTick(function() {
                let container = this.$refs.scroll_container;
                container.scrollTop = container.scrollHeight + 120;
            });
        }
    }
}
</script>
