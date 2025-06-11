<template>
    <ul class="products-list product-list-in-card pl-2 pr-2">
        <li v-if="subscriptions.length > 0"
            class="d-flex flex-row-reverse border-bottom"    
        >
            <small>{{ canEditLabel }}</small>
        </li>
        <li v-for="item in subscriptions"
            :id="'subscription_' + item.token.id"
            class="item"
            style="clear: right;"
            :value="item.token.id"
        >
            <div class="d-flex flex-column">
                <div>{{ item.token.title }}</div>
                <div class="d-flex align-items-center">
                    <i
                        class="fa fa-qrcode mr-2 pointer"
                        @click="setQRCodeId(item.token.id)"
                    ></i>
                    <span class="flex-fill">
                        {{ item.token.title }} |
                        <small>{{ diffForHumans(item.token.due_date) }}</small>
                    </span>
                    <a
                        class="text-danger px-2 py-0 mr-2 vuehover"
                        @click="unsubscribe(item)"
                    >
                        <i class="fa fa-trash"></i>
                    </a>
                    <span v-if="canEditCheckbox"
                        class="pull-right custom-control custom-switch custom-switch-on-green"
                    >
                        <input
                            :id="'subscription_input' + item.token.id"
                            type="checkbox"
                            class="custom-control-input pt-1"
                            v-model="item.token.editable"
                            @click="setPermission(item.token.id, item.token.editable)"
                        />
                        <label class="custom-control-label" :for="'subscription_input' + item.token.id"></label>
                    </span>
                </div>
                <div>
                    <span
                        :id="'subscription_link_'+item.token.id"
                        class="pointer text-muted text-xs line-clamp"
                        v-dompurify-html="generateShareURL(item.token)"
                        @click="copyToClipboard"
                    ></span>
                </div>
                <div v-if="item.token.id == showQrCodeId"
                    :id="'svgQrCode_'+item.token.id"
                    v-dompurify-html="item.qr.image"
                    style="width: 212px;"
                    class="pointer"
                    @click="downloadSVG(item)"
                ></div>
            </div>
        </li>
    </ul>
</template>
<script>
import {useToast} from "vue-toastification";

export default {
    props: {
        modelUrl: {
            type: String,
        },
        subscriptions: {
            type: Object,
        },
        subscribing_model: {
            type: String,
        },
        canEditLabel: {
            type: String,
            default: window.trans.global.can_edit,
        },
        canEditCheckbox: {
            type: Boolean,
            default: true,
        },
    },
    setup() {
        const toast = useToast();
        return {
            toast,
        }
    },
    data() {
        return {
            showQrCodeId: '',
        }
    },
    methods: {
        generateShareURL(item) {
            if(this.modelUrl == 'curriculum'){
                return window.location.origin + "/curricula/" + item[this.modelUrl+'_id']  + "/token?sharing_token=" + item.sharing_token;
            } else {
                return window.location.origin + "/" + this.modelUrl + "s/" + item[this.modelUrl+'_id']  + "/token?sharing_token=" + item.sharing_token;
            }
        },
        copyToClipboard(event) {
            console.log(event.target.innerText);
            navigator.clipboard.writeText(event.target.innerText);
            this.successNotification(window.trans.global.token_copied);
        },
        unsubscribe(item) { //id of external reference and value in db
            axios.delete('/' + this.modelUrl + 'Subscriptions/' + item.token.id)
                .then(() => this.$emit('tokenDeleted', item))
                .catch(error => console.log(error));
        },
        async setPermission(id, status) { //id of external reference and value in db
            try {
                status = (await axios.patch('/' + this.modelUrl + 'Subscriptions/' + id, {'editable': !status})).data.editable;
            } catch (error) {
                console.log(error);
            }
        },
        diffForHumans: function (date) {
            if (date == null) {
                return window.trans.global.valid;
            }
            return window.trans.global.valid_to + ' ' + moment(date).locale('de').fromNow();
        },
        successNotification(message) {
            this.toast.success(message, {
                position: "top-right",
                timeout: 3000,
                closeOnClick: true,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: true,
                draggablePercent: 0.6,
                showCloseButtonOnHover: false,
                hideProgressBar: true,
                closeButton: "button",
                icon: true,
                rtl: false,
            });
        },
        setQRCodeId(id) {
            if (this.showQrCodeId == id) {
                this.showQrCodeId = '';
            } else {
                this.showQrCodeId = id;
            }
        },
        downloadSVG(item) {
            var pWindow = window.open('', 'PRINT', 'height=400,width=600');

            pWindow.document.write('<html><head><title>' + item.token.title  + '</title>');
            pWindow.document.write('</head><body>');
            pWindow.document.write('<h5>' + item.token.title + '</h5>');
            pWindow.document.write(document.getElementById('svgQrCode_'+this.showQrCodeId).innerHTML);
            pWindow.document.write('<p>' + document.getElementById('subscription_link_'+this.showQrCodeId).innerHTML + '</p>');
            pWindow.document.write('</body></html>');

            pWindow.document.close(); // necessary for IE >= 10
            pWindow.focus(); // necessary for IE >= 10

            pWindow.print();
            pWindow.close();

            return true;
        },
    },
}
</script>