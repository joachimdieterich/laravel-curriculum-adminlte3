<template>
    <template v-for="(part, index) in formattedParts">
        <LinkItem v-if="typeof part === 'object'"
            :ref="'link-' + index"
            class="d-none"
            :href="part.href"
            :text="part.text"
            :target="part.target"
        />
    </template>
    <span v-html="renderedHtml"></span>
</template>
<script>
import LinkItem from "./LinkItem.vue";

export default {
    components: { LinkItem },
    props: {
        htmlContent: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            componentId: this.$.uid,
            renderedHtml: null,
        }
    },
    mounted() {
        // let the DOM update first
        this.$nextTick(() => this.renderHTML());
    },
    computed: {
        formattedParts() {            
            let parts = []; // parts consist of either strings (plain HTML) or objects (LinkItem props)
            let remainingHtml = this.htmlContent;
            // each match contains three parts: the full match, the href, and the text
            const regex = /<a.+?href="(.*?)".*?>(.*?)<\/a>/g;
            const matches = this.htmlContent.matchAll(regex);

            for (let match of matches) {
                let index = remainingHtml.indexOf(match[0]);
                if (index > 0) {
                    parts.push(remainingHtml.substring(0, index));
                    remainingHtml = remainingHtml.substring(index + match[0].length);
                }

                if (typeof match[1] === 'undefined' || !this.isValidHttpUrl(match[1])) {
                    parts.push(match[0]);
                    continue;
                }

                parts.push({
                    href: match[1],
                    text: match[2],
                    target: match[0].includes('target="_blank"') ? '_blank' : '_self'
                });
            }

            if (remainingHtml.length > 0) {
                parts.push(remainingHtml);
            }

            return parts;
        }
    },
    methods: {
        isValidHttpUrl(string) {
            let url;

            try {
                url = new URL(string);
            } catch (_) {
                return false;
            }

            return url.protocol === "http:" || url.protocol === "https:";
        },
        renderHTML() {
            let tempHtml = '';

            this.formattedParts.forEach((part, index) => {
                if (typeof part === 'object') {
                    const linkItem = this.$refs['link-' + index][0];
                    if (linkItem) {
                        tempHtml += linkItem.$el.innerHTML;
                    } else {
                        tempHtml += part;
                    }
                } else {
                    tempHtml += part;
                }
            });
            // only assign the completed HTML so it doesn't try to re-render for each part
            this.renderedHtml = tempHtml;

            // clean up
            this.$el.parentElement.querySelectorAll('.link-item-wrapper').forEach(elem => elem.remove());
        },
    },
    watch: {
        formattedParts() {
            this.$nextTick(() => this.renderHTML());
        },
    },
}
</script>