<template>
    <button
        class="btn btn-icon px-2 py-1"
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
        websocket: {
            type: Boolean,
            default: false,
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
            if (this.likes === null || this.likes === undefined) {
                return;
            }

            return this.likes.findIndex(l => l.user_id == this.$userId) != -1;
        },
        startWebsocket() {
            if (this.websocket === true) {
                this.likes.forEach((like) => {
                    this.$echo
                        .join('App.Reaction.' + this.model.id)
                        .listen('.Liked', (payload) => {
                            this.$eventHub.emit('kanban-item-commend-created', payload.model);
                        })
                        .listen('.Unliked', (payload) => {
                            this.$eventHub.emit('kanban-item-commend-deleted', payload.model);
                        });
                });
            }
        },
    },
    mounted() {
        this.startWebsocket();

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
