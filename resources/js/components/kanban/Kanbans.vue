<template >
    <div class="row">
        <div class="col-md-12 py-2">
            <div id="kanbans_filter" class="dataTables_filter">
                <label >
                    <input type="search"
                           class="form-control form-control-sm"
                           placeholder="Suchbegriff"
                           v-model="search">
                </label>
            </div>
        </div>

        <div style="clear:right;"
             v-for="(kanban,index) in kanbans"
             v-if="(kanban.title.toLowerCase().indexOf(search.toLowerCase()) !== -1)
                || search.length < 3"
             :id="kanban.id"
             v-bind:value="kanban.id"
             class="col-md-4">
            <a :href="'kanbans/'+kanban.id"
               class="text-decoration-none text-black">
                <div class="info-box elevation-1" :style="'border-bottom: 5px solid ' + kanban.color">
                        <span  class="info-box-icon bg-info elevation-1" :style="{backgroundColor: kanban.color + ' !important'}">
                            <i class="fa fa-columns"></i>
                        </span>
                    <div class="info-box-content">
                        <span class="pull-right" v-html="kanban.action"></span>
                        <span class="info-box-text"><strong v-html="kanban.title"></strong></span>
                        <span class="pt-2 " v-html="decodeHtml(kanban.description)"></span>
                    </div>
                </div>
            </a>
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
                kanbans: [],
                subscriptions: {},
                search: '',
                errors: {}
            }
        },
        methods: {
            loaderEvent(){
                axios.get('kanbans/list')//'/kanbanSubscriptions?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id)
                    .then(response => {
                        this.kanbans = response.data.data;
                    })
                    .catch(e => {
                        this.errors = e.data.errors;
                    });
            },
            decodeHtml(html) {
                let txt = document.createElement("textarea");
                txt.innerHTML = html;
                return txt.value.replace(/<[^>]+>/g, '');
            },
        },

        mounted() {
            this.loaderEvent();
        },
        components: {

        },
    }
</script>
