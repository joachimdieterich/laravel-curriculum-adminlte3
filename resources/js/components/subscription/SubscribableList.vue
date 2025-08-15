<template >
    <div>
        <h5 class="pt-3">
            Weiterführende Links
        </h5>
        <ul class="products-list product-list-in-card pl-2 pr-2">
            <template
                v-for="subscription in subscribers.subscriptions">
                <li :id="'subscription_' + subscription.id"
                    v-bind:value="subscription.id"
                    style="clear:right;"
                    class="item">
                <span v-if="subscription.subscribable_type == 'App\\Organization'">
                    <a :href="'/organizations/' + subscription?.subscribable.id"
                       target="_blank">
                        {{ subscription?.subscribable.title }}
                    </a>
                </span>
                    <span v-else-if="subscription.subscribable_type == 'App\\User'">
                    {{ subscription?.subscribable.firstname }}  {{ subscription.subscribable.lastname }}
                </span>
                    <span v-else>
                    {{ subscription.subscribable.title }}
                </span>
                </li>
            </template>
        </ul>
    </div>
</template>

<script>
    export default {
        props: {
            url: String,
            model_id: Number
        },
        data() {
            return {
                subscribers: {},
                errors: {},
            }
        },
        methods: {
            loadSubscribers() {
                axios.get(this.url + '=' + this.model_id)
                    .then(res => {
                        this.subscribers = res.data.subscribers;
                    })
                    .catch(err => {
                        console.log(err.response);
                    });
            },
        },
        mounted() {
            this.loadSubscribers();
        }
    }
</script>
