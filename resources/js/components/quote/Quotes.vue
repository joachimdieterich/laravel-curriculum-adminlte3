<template>
    <div>
        <div class="card collapsed-card mb-2" v-for="curriculum in quote_curricula_list">
            <div class="card-header">
                <h3 class="card-title"
                     data-card-widget="collapse"
                    :data-target="'#'+tagName(curriculum.id)">
                   {{curriculum.title}}
                </h3>
                <div class="card-tools pull-right">
                    <button class="btn btn-tool"
                         data-card-widget="collapse"
                         :data-target="'#'+tagName(curriculum.id)">
                     <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class=" card-body collapse"
                 :id="tagName(curriculum.id)">
                <div v-for="filtered_quote in filterQuotes(curriculum.id)">
                    <div class="row">
                        <div class="col-xs-12 p2" >
                            <div class="p-2">
                                <div v-dompurify-html="filtered_quote.quote.quote"></div>
                                <a style="cursor: pointer;" @click="showFullContent = !showFullContent">
                                    <cite class="text-primary" v-dompurify-html="filtered_quote.quote.content.title"></cite>
                                </a>
                                <div
                                    v-if="showFullContent"
                                    v-dompurify-html="filtered_quote.quote.content.content">
                                </div>
                            </div>

                        </div>
                    </div>

                    <hr style="clear:both;">
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import content from "../content/Content.vue";

    export default {
        props: {
            objective: {
                type: Object
            },
            type: {
                type: String
            }
        },
        data: function() {
            return {
                showFullContent: false,
                quote_subscriptions: [],
                quote_curricula_list: [],
            }
        },

        methods: {
            loaderEvent() {
                axios.get('/' + this.type + 'Objectives/' + this.objective.id + '/quoteSubscriptions')
                    .then(response => {
                        if (typeof (response.data.quotes_subscriptions) !== 'undefined') {
                            this.quote_subscriptions = response.data.quotes_subscriptions;
                            this.quote_curricula_list = response.data.curricula_list;
                        }
                    }).catch(e => {
                    console.log(e)
                });
            },
           filterQuotes(curriculum_id) {
                let filterQuotes = this.quote_subscriptions;
                filterQuotes = filterQuotes.filter(
                    (c) => {
                        if (c.quote.content) {
                          return c.quote.content?.subscriptions[0].subscribable_id === curriculum_id;
                        }
                    }
                  );

                return filterQuotes;
            },
           tagName: function(i){
                return 'quote_curriculum_'+i;
            },
            show(content, quote) {
                //this.$modal.show('content-modal', {'content': content, 'quote': quote});
            },
        },

        components: {
            content

        },

        }
</script>
