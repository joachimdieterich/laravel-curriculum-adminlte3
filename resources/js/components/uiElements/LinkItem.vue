<template>
    <div class="link-preview">
        <div class="info-box bg-info">
            <a
                :href="htmlToText(href)"
                :target="target"
                class="d-flex w-100"
            >
                <div v-if="qrCode"
                    v-html="qrCode"
                ></div>
                <div class="info-box-content pr-0">
                    <span
                        class="line-clamp line-clamp-3"
                        style="word-break: break-word;"
                    >
                        {{ text }}
                    </span>
                </div>
            </a>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        href: {
            type: String,
            default: null,
        },
        text: {
            type: String,
            default: null,
        },
        target: {
            type: String,
            default: '_blank',
        },
        size: {
            type: Number,
            default: 16,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            qrCode: null,
        }
    },
    mounted() {
        axios.get('/qrCodes/?url=' + this.href + '&size=' + this.size)
            .then(r => {
                this.qrCode = r.data.image;
            })
            .catch(e => {
                console.log(e);
            });
    },
}
</script>
<style>
.link-preview svg {
    height: 75px;
    width: 75px;
}
</style>