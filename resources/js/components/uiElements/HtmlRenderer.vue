<template>
    <template v-for="(part, index) in formattedParts"
        :key="index"
    >
        <component v-if="part.component"
            :is="part.component"
            v-bind="part.props"
        ></component>
        <span v-else
            v-html="part"
        ></span>
    </template>
</template>
<script>
import LinkItem from "./LinkItem.vue";

export default {
    props: {
        htmlContent: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            componentId: this.$.uid,
        }
    },
    computed: {
        formattedParts() {
            let parts = [];
            const regex = /(<a[^>]+href=["|'](.*?)["|'][^>]*>(.*)?<\/a>)?((?:.(?!<a))*.)?/gu;

            let m;
            while ((m = regex.exec(this.htmlContent)) !== null) {
                // This is necessary to avoid infinite loops with zero-width matches
                if (m.index === regex.lastIndex) {
                    regex.lastIndex++;
                }
                let ma = [];
                m.forEach((match, groupIndex) => {
                    ma.push(match);
                });
                let matches = [];
                matches.push(ma);

                //console.log(matches);
                matches.forEach((match) => {
                    if (typeof (match[2]) !== 'undefined') {
                        if (this.isValidHttpUrl(match[2])) {
                            const target = match[1].includes('target="_blank"') ? '_blank' : '_self';
                            parts.push({
                                component: 'LinkItem',
                                props: { href: match[2], text: match[3], target: target }
                            });
                        }
                    } else {
                        parts.push( match[0]);
                    }
                });
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
    },
    components: {
        LinkItem,
    },
}
</script>