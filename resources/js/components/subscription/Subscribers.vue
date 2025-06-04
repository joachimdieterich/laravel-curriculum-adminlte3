<template>
    <ul v-if="subscriptions.length != 0"
        class="products-list product-list-in-card pl-2 pr-2"
    >
        <li v-if="canEditCheckbox && subscriptions.length > 0"
            class="d-flex border-bottom"
        >
            <small class="flex-fill">Name</small>
            <small>{{ canEditLabel }}</small>
        </li>
        <template v-for="subscription in subscriptions">
            <li
                :id="'subscription_' + subscription.id"
                :value="subscription.id"
                style="clear: right;"
                class="item d-flex"
            >
                <div class="flex-fill">
                    <span v-if="subscribing_model == 'App\\User'">
                        {{ subscription?.subscribable.firstname }}  {{ subscription.subscribable.lastname }}
                    </span>
                    <span v-else>
                        {{ subscription.subscribable.title }}
                    </span>
                </div>

                <button
                    class="btn btn-flat px-2 py-0 ml-auto mr-2"
                    @click="unsubscribe(subscription)"
                >
                    <i class="fa fa-trash text-danger vuehover"></i>
                </button>

                <span v-if="canEditCheckbox"
                    class="custom-control custom-switch custom-switch-on-green"
                >
                    <input
                        :id="'subscription_input' + subscription.id"
                        v-model="subscription.editable"
                        type="checkbox"
                        class="custom-control-input pt-1"
                        @click="setPermission(subscription.id, subscription.editable)"
                    />
                    <label
                        class="custom-control-label"
                        :for="'subscription_input' + subscription.id"
                    ></label>
                </span>
            </li>
        </template>
    </ul>
</template>
<script>
export default {
    props: {
        modelUrl: {
            type: String,
            default: null,
        },
        subscriptions: {
            type: Object,
            deafult: null,
        },
        subscribing_model: {
            type: String,
            default: null,
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
    methods: {
        async unsubscribe(subscription) { // id of external reference and value in db
            try {
                await axios.delete('/' + this.modelUrl + 'Subscriptions/' + subscription.id  ).data;
            } catch(error) {
                console.log(error);
            }
            this.$eventHub.emit('unsubscribe', subscription);
            //$("#subscription_"+id).hide();
        },
        async setPermission(id, status) { // id of external reference and value in db
            try {
                status = (await axios.patch('/' + this.modelUrl + 'Subscriptions/' + id, {'editable': !status } )).data.editable;
            } catch(error) {
                console.log(error);
            }
        },
    },
}
</script>