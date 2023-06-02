<template >

    <ul id="test12341"
        class="products-list product-list-in-card pl-2 pr-2">
        <li v-if="subscriptions.length != 0">&nbsp;
            <span class="pull-right">
                <small>{{ trans('global.can_edit') }}</small>
            </span>
        </li>
        <li style="clear:right;"
            v-for="(item,index) in subscriptions"
            :id="'subscription_'+item.token.id"
            v-bind:value="item.token.id"
            class="item">
            <div class="d-flex flex-column">
                <div>
                    <i class="fa fa-qrcode"
                        @click="setQRCodeId(item.token.id)"></i>
                    <span>
                        {{ item.title }} |
                        <small>
                            {{ diffForHumans(item.token.due_date) }}
                        </small>
                    </span>
                    <span class="pull-right custom-control custom-switch custom-switch-on-green">
                        <input  v-model="item.token.editable"
                                type="checkbox"
                                class="custom-control-input pt-1 "
                                :id="'subscription_input'+item.token.id"
                                @click="setPermission(item.token.id, item.token.editable)">
                        <label class="custom-control-label " :for="'subscription_input'+item.token.id" ></label>
                    </span>
                    <span class="pull-right pr-2" ></span>
                    <button class="btn btn-flat py-0 pull-right"
                            @click="unsubscribe(item.token.id)">
                        <i class="fa fa-trash text-danger vuehover" ></i>
                    </button>
                </div>
                <div>
                    <span :id="'subscription_link_'+item.token.id"
                          @click="copyToClipboard"
                          class="pointer text-muted text-xs"
                          v-html="generateShareURL(item.token)"></span>
                </div>
                <div :id="'svgQrCode_'+item.token.id"
                    v-if="item.token.id == showQrCodeId"
                     v-html="item.qr.image"
                     style="width: 49px;"
                     class="float-left"

                     @click="downloadSVG(item)"></div>
            </div>
        </li>
    </ul>

</template>


<script>
import moment from 'moment';

export default {
    props: {
        modelUrl: String,
        subscriptions: {},
        subscribing_model: String,
    },
    data() {
        return {
            showQrCodeId: '',
            errors: {}
        }
    },
    methods: {
        generateShareURL(item) {
            return window.location.origin + "/" + this.modelUrl + "s/" + item.kanban_id + "/token?sharing_token=" + item.sharing_token;
        },
        copyToClipboard(event) {
            console.log(event.target.innerText);
            navigator.clipboard.writeText(event.target.innerText);
            this.successNotification(window.trans.global.token_copied);
        },
        async unsubscribe(id) { //id of external reference and value in db
            try {
                await axios.delete('/' + this.modelUrl + 'Subscriptions/' + id).data;
            } catch (error) {
                //this.errors = error.response.data.errors;
            }
            $("#subscription_" + id).hide();
        },
        async setPermission(id, status) { //id of external reference and value in db
            try {
                status = (await axios.patch('/' + this.modelUrl + 'Subscriptions/' + id, {'editable': !status})).data.editable;
            } catch (error) {
                //this.errors = error.response.data.errors;
            }
        },
        diffForHumans: function (date) {
            if (date == null) {
                return window.trans.global.valid;
            }
            return window.trans.global.valid_to + ' ' + moment(date).locale('de').fromNow();
        },
        successNotification(message) {
            this.$toast.success(message, {
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
                rtl: false
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
        }
    }
}
</script>
