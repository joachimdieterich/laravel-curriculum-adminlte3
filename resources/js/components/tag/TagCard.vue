<template>
    <div class="col-lg-12" :id="componentId" v-permission="'tag_access'">
        <div class="card">
            <div class="card-header">
                <div class="card-title px-1">
                    {{ trans(trans_header) }}
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active show">
                        <div class="row">
                            <div v-if="tags?.length != 0" v-for="tag in tags"
                                 class="col-1 mb-1"
                                 style="min-width: min-content;"
                            >
                                <a :href="'/tags/' + tag.id" role="button">
                                    <div class="badge badge-info badge-pill tag btn">
                                        {{ tag.translation }}
                                    </div>
                                </a>
                            </div>
                            <div v-else>
                                <div style="padding-left: 1.25rem;">
                                    {{ trans(trans_no_tags) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "tag-card",
    props: {
        tags: {
            type: Array
        },
        trans_header: {
            type: String,
            default: 'global.tag.title'
        },
        trans_no_tags: {
            type: String,
            default: 'global.tag.no_tags'
        },
    },
    data() {
        return {
            componentId: this.$.uid,
        }
    },
}
</script>
<style scoped>
.tag {
    width: 100%;
    height: 100%;
    vertical-align: middle;
    text-align: center;
    line-height: 100%;
    align-content: center;
    font-size: 0.9em;
    padding: 0.7em;
}
</style>