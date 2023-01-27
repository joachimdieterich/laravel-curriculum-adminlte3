<template >

    <ul class="products-list product-list-in-card pl-2 pr-2">
        <li v-if="subscriptions.length != 0">&nbsp;
            <span class="pull-right">
                <small>{{ trans('global.can_edit') }}</small>
            </span>
        </li>
        <li style="clear:right;"
            v-for="(item,index) in subscriptions"
            :id="'subscription_'+item.id"
            v-bind:value="item.id"
            class="item">
            <div class="d-flex flex-column">
                <div>
                    <span>
                        {{ item.subscribable.username }} |
                        <small>
                            {{ trans('global.valid_to') }} {{ diffForHumans(item.due_date) }}
                        </small>
                    </span>
                    <span class="pull-right custom-control custom-switch custom-switch-on-green">
                        <input  v-model="item.editable"
                                type="checkbox"
                                class="custom-control-input pt-1 "
                                :id="'subscription_input'+item.id"
                                @click="setPermission(item.id, item.editable)">
                        <label class="custom-control-label " :for="'subscription_input'+item.id" ></label>
                    </span>
                    <span class="pull-right pr-2" ></span>
                    <button class="btn btn-flat py-0 pull-right"
                            @click="unsubscribe(item.id)">
                        <i class="fa fa-trash text-danger vuehover" ></i>
                    </button>
                </div>
                <div>
                    <span @click="copyToClipboard" class="pointer text-muted text-xs" v-html="generateShareURL(item.subscribable.sharing_token)"></span>
                </div>
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
            errors: {}
        }
    },
    methods: {
        generateShareURL(token){
            return window.location.origin + "/" + this.modelUrl + "/share/" + token;
        },
        copyToClipboard(event){
            console.log(event.target.innerText);
            navigator.clipboard.writeText(event.target.innerText);
        },
        async unsubscribe(id) { //id of external reference and value in db
            try {
                await axios.delete('/' + this.modelUrl + 'Subscriptions/' + id  ).data;
            } catch(error) {
                //this.errors = error.response.data.errors;
            }
            $("#subscription_"+id).hide();
        },
        async setPermission(id, status) { //id of external reference and value in db
            try {
                status = (await axios.patch('/' + this.modelUrl + 'Subscriptions/' + id, {'editable': !status } )).data.editable;
            } catch(error) {
                //this.errors = error.response.data.errors;
            }
        },
        diffForHumans : function (date) {
            return moment(date).locale('de').fromNow();
        },
    }
}
</script>
