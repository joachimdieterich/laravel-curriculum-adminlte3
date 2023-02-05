<template>
    <div
       @click="toggle()"
       class="position-relative pull-right">

        <i  v-if="userHasReaction()"
           class="fa fa-heart pointer with-comment-count"></i>
        <i v-else
           class="far fa-heart pointer with-comment-count"></i>
        <span v-if=" model.likes  !== null">
            <span v-if="likes_count > 0"
                class="comment-count mt-1 small bg-success"
            >
            {{ this.likes_count }}
            </span>
        </span>
    </div>
</template>
<script>
export default {
  name: 'Reaction',
  props: {
        model: {},
        url: {
            type: String,
        },
        reaction: {
          type: String,
        },
  },
    methods: {
        toggle(){
            axios.post(this.url + "/" + this.model.id + "/react", {
                'reaction': this.reaction,
            })
                .then(res => {
                    console.log(res);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        userHasReaction(){
            if (this.model.likes.findIndex(l => l.user_id == this.$userId) != -1){
               return true;
            }  else {
               return false;
            }
        },
    },
    computed:{
      likes_count() {
          return this.model.likes.length;
      }
    }

}
</script>
