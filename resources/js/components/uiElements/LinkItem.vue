<template>
    <div class="link-item-wrapper">
        <div class="info-box bg-info mb-0">
            <a
                ref="link"
                :href="href"
                :target="target"
                class="d-flex w-100"
            >
                <img
                    :src="'/qrCodes/?url=' + encodeURIComponent(this.href) + '&size=' + this.size"
                    width="75"
                    height="75"
                />
                <div class="info-box-content pe-0">
                    <span
                        ref="text"
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
            component_id: this.$.uid
        }
    },
}
</script>