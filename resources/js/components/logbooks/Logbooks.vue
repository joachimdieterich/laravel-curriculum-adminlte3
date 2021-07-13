<template >
    <div class="row">
        <div class="col-md-12 py-2">
            <div id="logbooks_filter" class="dataTables_filter">
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
             v-if="(item.logbook.title.toLowerCase().indexOf(search.toLowerCase()) !== -1)
                || search.length < 3"
             :id="item.logbook.id"
             v-bind:value="item.logbook.id"
             class="col-md-6">
            <div class="card mb-2">
                <div class="card-header p-1">
                    <a class="link-muted" :href="'/logbooks/' + item.logbook.id">
                        <h3 class="card-title p-2">
                            <i class="fas fa-book pr-2 text-muted"></i>
                            {{ item.logbook.title }}
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
                logbooks: [],
                subscriptions: {},
                search: '',
                errors: {}
            }
        },
        methods: {
            loaderEvent(){
                axios.get('/logbookSubscriptions?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id)
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
