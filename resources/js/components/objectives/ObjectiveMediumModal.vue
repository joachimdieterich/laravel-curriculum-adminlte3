<template>
    <modal 
        id="objective-medium-modal" 
        name="objective-medium-modal" 
        height="auto" 
        width="70%"
        :adaptive=true
        :scrollable=true
        :draggable=true
        :resizable=true
        @before-open="beforeOpen"
        style="z-index: 25000">
        <div class="card" style="margin-bottom: 0px !important">
            <div class="card-header">
                <h3 class="card-title">
                    {{ trans('global.media.title') }}
                </h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-widget="remove" @click="close()">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

            </div>

            <div class="card-body" >
                <ul class="nav nav-pills">
                    <li v-for="typetab in typetabs" class="nav-item p-2">
                        <a class="nav-link " :href="'#tab_' + typetab.id" 
                           :class="(activetab == typetab.id) ? 'active' : ''"
                           @click="setActiveTab(typetab)"
                           data-toggle="tab">
                           {{ typetab.title }}
                    </a>
                </li>
            </ul> 

            <div class="tab-content"> 
                <!--                    1 Files -->
                <div class="tab-pane" id="'tab_1'"  
                     :class="(activetab == 1) ? 'active show' : ''">
                     <div class="px-2" v-for="subscription in media_subscriptions" >
                        <div class="row">
                            <div class="col-12">
                                <medium :subscription="subscription" :medium="match(subscription.medium_id)" ></medium>
                            </div>
                        </div>
                    </div>     
                </div><!-- /.tab-pane -->
                <!--                  2  References -->
                <div class="tab-pane" id="'tab_2'"  
                     :class="(activetab == 2) ? 'active show' : ''">
                     <references 
                        :reference_subscriptions="reference_subscriptions" 
                        :curricula_list="curricula_list" 
                        ></references>
                </div><!-- /.tab-pane -->
                <!--                  3  References -->
                <div class="tab-pane" id="'tab_3'"  
                     :class="(activetab == 3) ? 'active show' : ''">
                     <quotes 
                        :quote_subscriptions="quote_subscriptions" 
                        :quote_curricula_list="quote_curricula_list"
                        ></quotes>
                </div><!-- /.tab-pane -->

            </div> <!-- /.tab-content -->



        </div>
        <div class="card-footer">
            <div class="form-group m-2">
                <button type="button" 
                        class="btn btn-info" 
                        data-widget="remove" 
                        @click="close()">
                    {{ trans('global.cancel') }}
                </button>
            </div>
        </div>

    </div>
</modal>
</template>

<script>
    import Medium from '../media/Medium';
    import References from '../reference/References';
    import Quotes from '../quote/Quotes';


    export default {
        data() {
            return {
                objective: [],
                media: [],
                media_subscriptions: [],
                reference_subscriptions: [],
                curricula_list: [],
                quote_subscriptions: [],
                quote_curricula_list: [],
                typetabs: [],
                sharingLevels: {},
                activetab: null,
                setting: {
                    'last': null,

                },

                errors: {}
            }
        },
        methods: {
            filterMedia(typetab) {
                let filteredMedia = this.media_subscriptions; //to array

                filteredMedia = filteredMedia.filter(
                        t => t.sharing_level_id === typetab
                );
                //console.log(filteredMedia);
                return filteredMedia;
            },
            match(medium_id) {
                let medium = this.media.media; //to array
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

            beforeOpen(event) {
                if (event.params.content) {
                    this.typetabs = []; //reset
                    //console.log(event.params.content);
                    this.objective = event.params.content;
                    this.media = event.params.content;
                    this.media_subscriptions = event.params.content.media_subscriptions;

                    if (this.media.media_subscriptions.length != 0) {
                        this.typetabs = [... new Set([{'id': 1, 'title': 'Media'}])];
                        axios.get('/sharingLevels').then(response => {
                            this.sharingLevels = response.data.sharingLevel;
                        }).catch(e => {
                            this.errors = error.response.data.errors;
                        });

                        this.activetab = this.typetabs[0];
                    }

                    axios.get('/enablingObjectives/' + this.objective.id + '/referenceSubscriptionSiblings').then(response => {
                        if (response.data.siblings.length !== 0) {
                            this.reference_subscriptions = response.data.siblings;
                            this.curricula_list = response.data.curricula_list;
                            this.typetabs = this.typetabs.concat([{'id': 2, 'title': 'Reference'}]);
                            this.activetab = this.typetabs[0].id;
                        }
                        
                    }).catch(e => {
                        this.errors = error.response.data.errors;
                    });
                    
                    axios.get('/enablingObjectives/' + this.objective.id + '/quoteSubscriptions').then(response => {
                        if (response.data.quotes_subscriptions.length !== 0) {
                            this.quote_subscriptions = response.data.quotes_subscriptions;
                            this.quote_curricula_list = response.data.curricula_list;
                             this.typetabs = this.typetabs.concat([{'id': 3, 'title': 'Quotes'}]);
                             this.activetab = this.typetabs[0].id;
                         }
                        
                    }).catch(e => {
                        this.errors = error.response.data.errors;
                    });
                    
                    this.activetab = this.typetabs[0].id;
                }
            },

            close() {
                this.$modal.hide('objective-medium-modal');
            }
        },
        computed: {
            scr: function () {
                return '/media/' + this.media.media_subscriptions.medium_id;
            }
        },
        components: {
            Medium,
            References,
            Quotes
        }

    }
</script>