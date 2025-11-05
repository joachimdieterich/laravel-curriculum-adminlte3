<template>
    <button
        class="btn btn-icon px-2 py-1"
        :title="active ? trans('global.tag.remove_favourite') : trans('global.tag.add_favourite')"
        @click="active = !active"
    >
        <i v-if="active"
           class="fa fa-heart"
        ></i>
        <i v-else
           class="far fa-heart"
        ></i>
    </button>
</template>
<script>
export default {
    name: "Favourite",
    props: {
        url: {
            type: String,
            required: true,
            title: "Url to attach/detach tag to a model"
        },
        model: {
            type: Object,
            required: true,
            title: "The model to favourite"
        },
        isFavourited: {
            type: Boolean,
            title: "If the model is already favourited"
        },
    },
    data: function () {
        return {
            active: undefined
        };
    },
    mounted() {
        this.active = this.isFavourited;
    },
    watch: {
        active: function (newValue, oldValue) {
            if (oldValue === undefined) {
                return;
            }

            axios.post(this.url + "/" + this.model.id + "/favour", {
                favourite: newValue,
            })
                .catch(err => {
                    console.log(err);
                });
        }
    }
}
</script>