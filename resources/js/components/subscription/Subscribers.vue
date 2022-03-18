<template >

    <ul class="products-list product-list-in-card pl-2 pr-2">
        <li v-if="filterSubscriptions().length != 0">&nbsp;
            <span class="btn btn-flat pull-right py-0"><small>{{ trans('global.can_edit') }}</small></span>
        </li>
        <li style="clear:right;"
            v-for="(item,index) in filterSubscriptions()"
            :id="'subscription_'+item.id"
            v-bind:value="item.id"
            v-if="item.subscribable_type === subscribing_model"
            class="item">
            <span v-if="item.subscribable_type === 'App\\User'">
            {{ item.subscribable.firstname }}  {{ item.subscribable.lastname }}
            </span>
            <span v-else>
            {{ item.subscribable.title }}
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
        </li>
    </ul>

</template>


<script>

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
            filterSubscriptions() {
                return  this.subscriptions.filter(
                    s => s.subscribable_type === this.subscribing_model
                );
            }

        },



    }
</script>
