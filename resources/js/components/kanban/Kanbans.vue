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

        <div class="col-md-12 py-2">
            <div v-for="(kanban,index) in kanbans"
                 v-if="(kanban.title.toLowerCase().indexOf(search.toLowerCase()) !== -1)
                        || search.length < 3"
                 :id="'kanban-' + kanban.id"
                 class="box box-objective nav-item-box-image pointer my-1"
                 style="min-width: 200px !important;"
                 :style="'border-bottom: 5px solid ' + kanban.color">
                <a :href="'/kanbans/'+kanban.id"
                   class="text-decoration-none text-black"
                   >
                    <div v-if="kanban.medium_id"
                         class="nav-item-box-image-size"
                         :style="{'background': 'url(/media/' + kanban.medium_id + '?model=Kanban&model_id=' + kanban.id +') top center no-repeat', 'background-size': 'cover', }">
                         <div class="nav-item-box-image-size"
                              style="width: 100% !important;"
                              :style="{backgroundColor: kanban.color + ' !important',  'opacity': '0.5'}">
                         </div>
                    </div>
                    <div v-else
                         class="nav-item-box-image-size text-center"
                         :style="{backgroundColor: kanban.color + ' !important'}">
                        <i class="fa fa-2x p-5 fa-columns nav-item-text text-white"></i>
                    </div>

                    <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                       <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                           {{ kanban.title }}
                       </h1>
                       <p class="text-muted small"
                          v-html="decodeHtml(kanban.description)">
                       </p>
                    </span>

                    <div class="symbol"
                         style="position: absolute;
                                padding: 6px;
                                z-index: 1;
                                width: 30px;
                                height: 40px;
                                background-color: #0583C9;
                                top: 0px;
                                font-size: 1.2em;
                                left: 10px;">
                        <i v-if="$userId == kanban.owner_id"
                           class="fa fa-user text-white pt-2"></i>
                        <i v-else
                           class="fa fa-share-nodes text-white pt-2"></i>
                    </div>
                    <span v-if="$userId == kanban.owner_id"
                          class="p-1 pointer_hand"
                          accesskey="" style="position:absolute; top:0px; height: 30px; width:100%;">

                       <button
                           :id="'delete-kanban-'+kanban.id"
                           type="submit" class="btn btn-danger btn-sm pull-right"
                           @click.prevent="destroy(kanban.id)">
                           <small><i class="fa fa-trash"></i></small>
                       </button>

                       <a :href="'/kanbans/' + kanban.id + '/edit'"
                          class="btn btn-primary btn-sm pull-right mr-1">
                           <small><i class="fa fa-pencil-alt"></i></small>
                       </a>
                   </span>
                </a>

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
                kanbans: [],
                subscriptions: {},
                search: '',
                url: 'kanbans/list',
                errors: {}
            }
        },
        methods: {
            loaderEvent(){

                if (typeof (this.subscribable_type) !== 'undefined' && typeof(this.subscribable_id) !== 'undefined'){
                    this.url = '/kanbanSubscriptions?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id
                }
                axios.get(this.url)
                    .then(response => {
                            if (typeof (this.subscribable_type) !== 'undefined' && typeof(this.subscribable_id) !== 'undefined'){
                                this.kanbans = response.data.data;
                            } else {
                                this.kanbans = response.data.data;
                            }

                    })
                    .catch(e => {
                        console.log(e.data.errors);
                    });
            },
            decodeHtml(html) {
                let txt = document.createElement("textarea");
                txt.innerHTML = html;
                return txt.value.replace(/<[^>]+>/g, '');
            },
            async destroy(id) {
                try {
                    this.kanbans = (await axios.delete('/kanbans/' + id)).data.data;
                } catch (error) {
                    console.log(error);
                }
            },
        },

        mounted() {
            this.loaderEvent();
        },
        components: {

        },
    }
</script>
