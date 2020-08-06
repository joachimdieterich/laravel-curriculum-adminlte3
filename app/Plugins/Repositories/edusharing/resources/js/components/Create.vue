<template>
    <div>    
        <div class="row">
            <div class="col-12">
                <div class="form-group "
                    :class="errors.title ? 'has-error' : ''"
                      >
                    <label for="title">{{ trans('global.externalRepositorySubscription.search') }}</label>
                    <input
                        type="text" id="search"
                        name="search"
                        class="form-control"
                        v-model="search"
                        required
                        @keyup.enter="getSearch()" 
                        />
                    
                     <p class="help-block" v-if="errors.searc" v-text="errors.search[0]"></p>
                </div>
                <div >
                    <ul class="nav flex-column">
                        <li v-for="medium in media" 
                            :id="medium.ref.id"
                            class="nav-item" >
                            <a href="#">{{medium.name}} 
                                <span :id="'link_btn_'+medium.ref.id" class="pull-right badge bg-blue" @click="linkMedium(medium.ref.id);">
                                    <i class="fa fa-link" ></i>
                                </span>
                                <span :id="'unlink_btn_'+medium.ref.id" class="pull-right badge bg-red invisible" @click="unlinkMedium(medium.ref.id);">
                                    <i class="fa fa-unlink" ></i>
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>


    </div>
</template>


<script>
    
    export default {
        props: {
                'model': {},
              },
        data() {
            return {
                media: null,
                errors: {},
                search: '',
                subscriptions: null
            }
        },
        methods: {
            async linkMedium(id) {
                try {
                    await axios.post('/repositorySubscriptions', {
                        value: id, 
                        subscribable_id: this.model.subscribable_id, 
                        subscribable_type: this.model.subscribable_type, 
                        repository: 'edusharing' 
                    }).data;
                } catch(error) {
                    //this.errors = error.response.data.errors;
                }
                $("#"+id).addClass( "bg-green" );
                $("#link_btn_"+id).addClass( "invisible" );
                $("#unlink_btn_"+id).removeClass( "invisible" );
            },
            async unlinkMedium(id) {
                try {
                    await axios.post('/repositorySubscriptions/destroySubscription', {
                        value: id, 
                        subscribable_id: this.model.subscribable_id, 
                        subscribable_type: this.model.subscribable_type, 
                        repository: 'edusharing' 
                    }).data;
                } catch(error) {
                    //this.errors = error.response.data.errors;
                }
                $("#"+id).removeClass( "bg-green" );
                $("#link_btn_"+id).removeClass( "invisible" );
                $("#unlink_btn_"+id).addClass( "invisible" );
            },
            
            async getSearch() {
                try {
                    this.media = (await axios.post('/repositorySubscriptions/searchRepository', {
                        value:this.search,
                        repository: 'edusharing'
                    })).data.nodes;
                } catch(error) {
                    //this.errors = error.response.data.errors;
                }
                
            },
            
            subscribable_type() {
                var reference_class = 'App\\TerminalObjective';
                if (typeof this.model.terminal_objective === 'object'){
                    reference_class = 'App\\EnablingObjective';
                } 

                return reference_class;
            },
            
            show(medium) {   
                window.open(medium.path, '_blank');
            },
            href(medium) {
                return medium.thumb;
            },
            ui(){
                this.subscriptions.forEach(function(item){
                    $(document.getElementById(item.value)).addClass( "bg-green" );
                    $(document.getElementById('link_btn_'+item.value)).addClass( "invisible" );
                    $(document.getElementById('unlink_btn_'+item.value)).removeClass( "invisible" );
                });
            }
        },
        updated() {
            this.ui();
        },
        computed: {
            
        },
       
        mounted() {
            axios.get('/repositorySubscriptions', {
                    'subscribable_id': this.model.subscribable_id, 
                    'subscribable_type': this.model.subscribable_typ })
                .then(response => {
                    this.subscriptions = response.data.subscriptions;
                })
                .catch(e => {
                this.errors = error.response.data.errors;
            });
        },
   
    }
</script>