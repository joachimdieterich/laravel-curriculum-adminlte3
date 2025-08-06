<template>
    <button
        class="btn btn-icon px-2 py-1"
        role="button"
        :title="userHasReaction() ? trans('global.remove_like') : trans('global.add_like')"
        @click="toggle()"
    >
        <i v-if="userHasReaction()"
           class="fa fa-heart"
        ></i>
        <i v-else
           class="far fa-heart"
        ></i>
        <span v-if="likes !== null && likes_count > 0"
            class="comment-count bg-success"
        >
            {{ likes_count }}
        </span>
    </button>
</template>
<script>
export default {
    name: 'Reaction',
    props: {
        model: {
            type: Object,
            default: null,
        },
        url: {
            type: String,
            default: null,
        },
        reaction: {
            type: String,
            default: 'like',
        },
    },
    data() {
        return {
            likes: [],
        };
    },
    methods: {
        toggle() {
            axios.post(this.url + "/" + this.model.id + "/react", {
                reaction: this.likes,
            })
                .then(res => {
                    this.likes = res.data.message.likes;
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        userHasReaction() {
            return this.likes.findIndex(l => l.user_id == this.$userId) != -1;
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
    watch: {
        'model.likes': function(newLikes) {
            this.likes = newLikes;
        },
    },
}
</script>