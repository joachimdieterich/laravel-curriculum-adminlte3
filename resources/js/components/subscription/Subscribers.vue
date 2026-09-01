<template>
    <ul v-if="subscriptions.length != 0"
        class="products-list product-list-in-card px-1 mt-3"
    >
        <li v-if="canEditCheckbox && subscriptions.length > 0"
            class="d-flex border-bottom"
        >
            <small class="flex-fill">Name</small>
            <small :id="modelUrl + subscribing_model.split('\\')[1]">{{ canEditLabel }}</small>
        </li>
        <template v-for="subscription in subscriptions">
            <li
                :id="'subscription_' + subscription.id"
                :value="subscription.id"
                class="item d-flex align-items-center"
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
                    class="btn btn-icon text-danger ms-auto me-3"
                    @click="unsubscribe(subscription)"
                >
                    <i class="fa fa-trash"></i>
                </button>

                <span v-if="canEditCheckbox"
                    class="form-check form-switch mb-0"
                >
                    <input
                        :id="'subscription_input' + subscription.id"
                        class="form-check-input"
                        type="checkbox"
                        role="switch"
                        :aria-labelledby="modelUrl + subscribing_model.split('\\')[1]"
                        v-model="subscription.editable"
                        switch
                        @click="setPermission(subscription.id, subscription.editable)"
                    />
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
<style>
li.item .fa-trash {
    opacity: 0%;
    transition: opacity 0.15s ease-in-out;
}
li.item:hover .fa-trash { opacity: 100%; }
@media (max-width: 991px) {
    li.item .fa-trash { opacity: 100%; }
}
</style>