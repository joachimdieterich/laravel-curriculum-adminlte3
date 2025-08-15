<template>
    <div :class="content.css_class +' px-0 my-1'">
        <div class="card collapsed-card pointer">
            <div class="card-header">
                <h1 class="h3 card-title"
                    :data-target="'#card_'+ content.id"
                    data-card-widget="collapse"
                    role="tab" aria-selected="true" tabindex="0">
                    {{ this.content.referenceable.title ?? this.content.title}}
                </h1>
                <div class="card-tools pull-right">
                    <button
                        v-permission="'navigator_create'"
                        :id="'delete-navigator-content-' + content.id"
                        type="submit"
                        class="btn btn-tool"
                        :aria-label="trans('global.delte')">
                        <i class="fa fa-trash"></i>
                    </button>
                    <button
                        :id="'navigator-item-content-' + content.id"
                        class="btn btn-tool"
                        :aria-label="trans('global.toggle')"
                        data-card-widget="collapse"
                        data-expand-icon="fa-expand-alt"
                        data-collapse-icon="fa-compress-alt">
                        <i class="fas fa-expand-alt"></i>
                    </button>

                    <a v-permission="'navigator_create'"
                       class="btn btn-tool"
                       :title="trans('global.edit')">
                        <i class="fa fa-pencil-alt"></i>
                    </a>
                </div>
            </div>
            <div :id="'card_' + content.id"
                class="card-body collapse"
                v-html="this.$decodeHtml(content.referenceable.content)">
            </div>

        </div>
    </div>

</template>

<script>

export default {
    name: "content",
    components:{
    },
    props: {
        content: {
            default: null
        },
    },
    data() {
        return {
            componentId: this.$.uid,
        }
    },
    mounted() {

    },
    methods: {

    }
}
</script>
