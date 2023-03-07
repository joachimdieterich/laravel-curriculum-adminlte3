<template>
    <div>
        <span v-if="mime(medium.mime_type) === 'embed'"
             style="height:500px">
            <iframe
                :src="scr"
                :height="height"
                :width="width"
                frameborder="0"></iframe>
        </span>
        <span v-else-if="mime(medium.mime_type) === 'external'">
            <render-usage
                :medium="medium"
                ></render-usage>
<!--            <img
                :src="scr"
                :width="width"
                @click="show()"/>-->
        </span>
        <span v-else-if="mime(medium.mime_type) === 'img'">
            <img
                :src="scr"
                :width="width"
                @click="show()"/>
        </span>
        <span v-else>
            - Please download file -
        </span>

    </div>
</template>

<script>
import renderUsage from "../../../../app/Plugins/Repositories/edusharing/resources/js/components/RenderUsage";
    export default {
        props: {
            medium: {
                type: Object,
                default: ''
            },
            height: {
                type: Number,
                default: 500
            },
            width: {
                type: Number,
                default: 600
            },
            edit: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                errors: {}
            }
        },
        methods: {
            mime(type) {
                switch (type) {
                    //Images use <img>
                    case 'image/jpg' :
                    case 'image/jpeg':
                    case 'image/png':
                    case 'image/gif':
                    case 'image/bmp':
                    case 'image/tiff':
                    case 'image/ico':
                    case 'image/svg':
                        return 'img';
                        break;
                    case 'edusharing':
                        return 'external';
                        break;
                    // default use <embed>
                    default:
                        return 'embed';
                        break;
                }
            },
            show() {
                this.$modal.show('medium-modal', {
                    'content': this.medium,
                });
            },
        },
        computed: {
            scr: function () {
                return '/media/'+ this.medium.id;
            },
        },
        components: {
            renderUsage
        }

    }
</script>
