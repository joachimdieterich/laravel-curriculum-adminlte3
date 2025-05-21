<template>
    <div class="link-preview">
        <div class="info-box bg-info">
            <a
                :href="href"
                :target="target"
                class="d-flex w-100"
            >
                <span>
                    <span v-if="qrCode">
                        <div v-html="qrCode"></div>
                    </span>
                </span>
                <div class="info-box-content">{{ text }}</div>
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