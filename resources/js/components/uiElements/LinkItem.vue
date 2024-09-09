<template>

    <div
        class="link-preview my-2 pointer"
        @click="open()">
        <div class="info-box mb-3 bg-info pointer">
            <span class="info-box-icon"
                  @click.prevent="big()">
                <span v-if="this.qrCode">
                    <div v-html="this.qrCode"
                        ></div>
                </span>
            </span>
            <div class="info-box-content ">
                <span class="info-box-text"
                style="width:180px">
                    {{ this.text }}
                </span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            href: '',
            text: '',
            defaultFavIcon: '/favicon.png',
            size: {
                type: Number,
                default: 16
            }
        },
        data() {
            return {
                component_id: this.$.uid,
                src: '',
                link: '',
                qrCode: false
            }
        },
        methods: {
            faviconizeElements(){
                this.src = this.getDomainFaviconURL(this.href);
            },
            open(){
                window.open(this.href, '_blank');
            },
        },
        mounted() {
            console.log(this.href);
            axios.get('/qrCodes/?url=' + this.href + '&size=' + this.size)
                .then(r => {
                    console.log(r);
                    this.qrCode = r.data.image;
                })
                .catch(e => {
                    console.log(e);
                });
        }
    }
</script>
<style>
.link-preview svg {
    height: 75px !important;
}
</style>
