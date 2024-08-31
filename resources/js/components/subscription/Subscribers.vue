<template >

    <ul v-if="subscriptions.length != 0"
        class="products-list product-list-in-card pl-2 pr-2">
        <li v-if="canEditCheckbox">&nbsp;
            <span class="btn btn-flat pull-right py-0">
                <small>{{ canEditLabel }}</small>
            </span>
        </li>
        <template
            v-for="subscription in subscriptions">
            <li v-if="subscription?.subscribable_type === this.subscribing_model"
                :id="'subscription_' + subscription.id"
                v-bind:value="subscription.id"
                style="clear:right;"
                class="item">
                <span v-if="subscription.subscribable_type == 'App\\User'">
                    {{ subscription?.subscribable.firstname }}  {{ subscription.subscribable.lastname }}
                </span>
                <span v-else>
                    {{ subscription.subscribable.title }}
                </span>

                <span v-if="canEditCheckbox"
                      class="pull-right custom-control custom-switch custom-switch-on-green">
                    <input
                        v-model="subscription.editable"
                        type="checkbox"
                        class="custom-control-input pt-1 "
                        :id="'subscription_input' + subscription.id"
                        @click="setPermission(subscription.id, subscription.editable)">
                    <label class="custom-control-label "
                           :for="'subscription_input'+subscription.id" ></label>
                </span>

                <span class="pull-right pr-2" ></span>

                <button class="btn btn-flat py-0 pull-right"
                        @click="unsubscribe(subscription.id)">
                    <i class="fa fa-trash text-danger vuehover" ></i>
                </button>
            </li>
        </template>
    </ul>

</template>

<script>
    export default {
        props: {
            modelUrl: String,
            subscriptions: {},
            subscribing_model: String,
            canEditLabel: {
                type: String,
                default: window.trans.global.can_edit,
            },
            canEditCheckbox: {
                type: Boolean,
                default: true
            }
        },
        data() {
            return {
                errors: {},
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
        },
    }
</script>
