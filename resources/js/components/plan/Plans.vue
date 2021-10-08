<template >
    <div class="row">
        <div class="col-md-12 py-2">
            <div id="plans_filter" class="dataTables_filter">
                <label >
                    <input type="search"
                           class="form-control form-control-sm"
                           placeholder="Suchbegriff"
                           v-model="search">
                </label>
            </div>
        </div>

        <div style="clear:right;"
             v-for="(item,index) in subscriptions"
             v-if="(item.plan.title.toLowerCase().indexOf(search.toLowerCase()) !== -1)
                || search.length < 3"
             :id="item.plan.id"
             v-bind:value="item.plan.id"
             class="col-md-6">
            <div class="card mb-2">
                <div class="card-header p-1">
                    <a class="link-muted" :href="'/plans/' + item.plan.id">
                        <h3 class="card-title p-2">
                            <i class="fa fa-clipboard-list pr-2 text-muted"></i>
                            {{ item.plan.title }}
                        </h3>
                    </a>
                </div>
            </div>
        </div>

    </div>
</template>

<script>

    export default {
        props: {
            subscribable_type: '',
            subscribable_id: '',
              },
        data() {
            return {
                plans: [],
                subscriptions: {},
                search: '',
                errors: {}
            }
        },
        methods: {
            loaderEvent(){
                axios.get('/planSubscriptions?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id)
                    .then(response => {
                        this.subscriptions = response.data.subscriptions;
                    })
                    .catch(e => {
                        this.errors = e.data.errors;
                    });
            },
        },

        mounted() {
            this.loaderEvent();
        },
        components: {

        },
    }
</script>
