<template> 
    <div class="row" >
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12">
                            <h5 class="m-0 pull-left">
                                <i class="fa fa-bullseye mr-1"></i> 
                            </h5>
                        
                            <div class="pull-right" v-can="'objective_edit'">
                                <a  @click="editObjective()">
                                    <i class="far fa-edit"></i>
                                </a> 
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body"> 
                    <p v-html="objective.title"></p>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <div class="float-left">
                        <small>{{ trans('global.enablingObjective.fields.time_approach') }}:{{objective.time_approach}}</small>
                    </div>
                    <small class="float-right" v-html="objective.updated_at"></small> 
                </div>
            </div>
        </div>
          
        <div class="col-12">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills pull-left">
                        <li class="nav-item">
                            <a class="nav-link active show" 
                               href="#description" 
                               data-toggle="tab">{{ trans('global.description') }}</a>
                        </li>
                        
                        <li class="nav-item" v-for="(item,index) in contentCategories" v-bind:value="'category_'+item.id">
                            <a class="nav-link" 
                               v-bind:href="'#category_'+item.id" 
                               data-toggle="tab">{{ item.title }}</a>
                        </li>
                        
                        <li v-for="typetab in typetabs" class="nav-item">
                        <a class="nav-link " 
                           v-bind:href="'#tab_' + typetab.id" 
                           data-toggle="tab">
                           {{ typetab.title }}
                        </a>
                    </li>
                        
                    </ul>
                     <div class="pull-right p-2" v-can="'objective_edit'">
                        <a href="">
                            <i class="fas fa-plus" @click.prevent="open('content-create-modal');"></i>
                        </a> 
                    </div>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active show" 
                             id="description"  
                             v-html="objective.description"></div>
                        <!-- /.tab-pane -->
                        <!--                    1 Contents -->
                        <div class="tab-pane" 
                             v-for="(item,index) in contentCategories" 
                             v-bind:id="'category_'+item.id"
                             >
                            <content-group 
                                :contents="filterContent(item.id)"
                                :category="item">
                            </content-group>
                        </div>
                   
                        <!--                    1 Files -->
                        <div class="tab-pane" 
                             id="tab_1"  
                             name="tab_1">
                             <div class="px-2" 
                                  v-for="subscription in objective.media_subscriptions" >
                                <div class="row">
                                    <div class="col-12">
                                        <medium :subscription="subscription" :medium="match(subscription.medium_id)" ></medium>
                                    </div>
                                </div>
                            </div>     
                        </div><!-- /.tab-pane -->
                        <!--                  2  References -->
                        <div class="tab-pane" 
                             id="tab_2"  
                             name="tab_2">
                             <references 
                                :reference_subscriptions="reference_subscriptions" 
                                :curricula_list="curricula_list" 
                                ></references>
                        </div><!-- /.tab-pane -->
                        <!--                  3  Quotes -->
                        <div class="tab-pane" 
                             id="tab_3"
                             name="tab_3">
                             <quotes 
                                :quote_subscriptions="quote_subscriptions" 
                                :quote_curricula_list="quote_curricula_list"
                             ></quotes>
                        </div><!-- /.tab-pane -->
  
                    </div>
                    <!-- /.tab-pane -->

                </div><!-- /.card-body -->
            </div>
        </div>
    </div>
</template>

<script>
    import Medium from '../media/Medium';
    import References from '../reference/References';
    import Quotes from '../quote/Quotes';
    import ContentGroup from '../content/ContentGroup';


    export default {
        props: {
            'objective': Object
        },    
        data() {
            return {
                type: null,
                media_subscriptions: [],
                reference_subscriptions: [],
                curricula_list: [],
                quote_subscriptions: [],
                quote_curricula_list: [],
                typetabs: [],
                categories: [],
                sharingLevels: {},
                activetab: null,
                setting: {
                    'last': null

                },
                errors: {}
            }
        },
        methods: {
            editObjective() {     
                this.$modal.show(this.type+'-objective-modal', {'objective': this.objective, 'method': 'PATCH' });
            },
            filterMedia(typetab) {
                let filteredMedia = this.objective.media_subscriptions; //to array

                filteredMedia = filteredMedia.filter(
                        t => t.sharing_level_id === typetab
                );
                return filteredMedia;
            },
            filterContent(category){
                if (category === 1){
                    return [].concat(...this.objective.content_subscriptions.filter(c => c.content.categories.find(cat => cat.id === category)))
                             .concat(...this.objective.content_subscriptions.filter(c => c.content.categories.length === 0));
                } else {
                    return [].concat(...this.objective.content_subscriptions.filter(c => c.content.categories.find(cat => cat.id === category)));
                }    
            },
            match(medium_id) {
                let medium = this.objective.media; //to array
                medium = medium.filter(
                        t => t.id === medium_id
                );
                return medium[0];
            },
            getTypeTitle(id) {
                let typeObject = this.sharingLevels.filter(
                        t => t.id === id
                );
                return typeObject;
            },
            setActiveTab(typetab) {
                this.activetab = typetab.id;
            },
            open(modal) {
                var reference_class = 'App\\EnablingObjective';
                if (this.type === 'terminal'){
                    reference_class = 'App\\TerminalObjective';
                }
                this.$modal.show(modal, {'referenceable_type': reference_class, 'referenceable_id': this.objective.id});
            },
            getUnique(arr, comp) {
                const unique = arr
                   .map(e => e[comp])
                   // store the keys of the unique objects
                   .map((e, i, final) => final.indexOf(e) === i && i)
                   // eliminate the dead keys & store unique objects
                   .filter(e => arr[e]).map(e => arr[e]);

                 return unique;
             }
            
        },
        mounted() {    
            this.typetabs = []; //reset
            //console.log(event.params.content);
            if (typeof this.objective.terminal_objective === 'object'){
                this.type = 'enabling';
            } else {
                this.type = 'terminal';
            }
            //this.media_subscriptions = event.params.content.media_subscriptions;

            if (this.objective.media_subscriptions.length != 0) {
                this.typetabs = [... new Set([{'id': 1, 'title': 'Media'}])];
                axios.get('/sharingLevels').then(response => {
                    this.sharingLevels = response.data.sharingLevel;
                }).catch(e => {
                    this.errors = response.data.errors;
                });

                this.activetab = this.typetabs[0];
            }

            axios.get('/'+this.type+'Objectives/' + this.objective.id + '/referenceSubscriptionSiblings').then(response => {
                if (response.data.siblings.length !== 0) {
                    this.reference_subscriptions = response.data.siblings;
                    this.curricula_list = response.data.curricula_list;
                    this.typetabs = this.typetabs.concat([{'id': 2, 'title': 'Reference'}]);
                    this.activetab = this.typetabs[0].id;
                }
            }).catch(e => {
                this.errors = response.data.errors;
            });

            axios.get('/'+this.type+'Objectives/' + this.objective.id + '/quoteSubscriptions').then(response => {
                if (response.data.quotes_subscriptions.length !== 0) {
                    this.quote_subscriptions = response.data.quotes_subscriptions;
                    this.quote_curricula_list = response.data.curricula_list;
                     this.typetabs = this.typetabs.concat([{'id': 3, 'title': 'Quotes'}]);
                     this.activetab = this.typetabs[0].id;
                 }
            }).catch(e => {
                this.errors = response.data.errors;
            });
            
            this.filterContent();
        },
        computed: {
            scr: function () {
                return '/media/' + this.objective.media_subscriptions.medium_id;
            },
            contentCategories: function() {
                let categories = [].concat(...this.objective.content_subscriptions
                                   .map(c => c.content.categories.map(cat =>({'id' : cat.id, 'title' : cat.title}))))
                                   .concat({'id' : 1, 'title' : 'Ohne Kategorie'}); //hack: default has to be set
                
              return this.getUnique(categories, 'id');
            },
        },
        components: {
            Medium,
            References,
            Quotes,
            ContentGroup
        }

    }
    </script>