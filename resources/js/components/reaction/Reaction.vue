<template>
    <div
       @click="toggle()"
       class="position-relative px-1 pointer"
    >
        <i  v-if="userHasReaction()"
           class="fa fa-heart"
        ></i>
        <i v-else
           class="far fa-heart"
        ></i>
        <span v-if="this.likes !== null && likes_count > 0"
            class="comment-count bg-success"
        >
            {{ this.likes_count }}
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
    data() {
        return {
            likes: []
        };
    },
    methods: {
        toggle() {
            axios.post(this.url + "/" + this.model.id + "/react", {
                'reaction': this.likes,
            })
                .then(res => {
                    this.likes = res.data.message.likes;
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        userHasReaction() {
            if (this.likes.findIndex(l => l.user_id == this.$userId) != -1){
               return true;
            }  else {
               return false;
            }
        },
    },
    mounted() {
      this.likes = this.model.likes;
    },
    computed: {
        likes_count() {
            return this.likes.length;
        }
    },
}
</script>