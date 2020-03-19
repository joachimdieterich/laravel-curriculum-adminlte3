<template>
    <div class="col-12">      
        <div id="loading" class="overlay text-center" style="width:100% !important;">
            <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
            <span class="sr-only">Loading...</span>
        </div>
        <span v-for="media_subscription in media">
            <div v-for="medium in media_subscription"
                 :id="medium.node_id"
                 class="box box-objective pointer my-1" 
                 style="height: 300px !important; min-width: 200px !important; padding: 0; background-size: cover;"
                 :style="{'background-image':'url('+href(medium)+')'}"
                 @click="show(medium)">    
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

                    <i class="fa fa-photo-video text-white pt-2"></i>
                </div>
                <span 
                    v-can="'medium_delete'"
                    class="p-1 pointer_hand" 
                    accesskey="" style="position:absolute; top:0px; height: 30px; width:100%;">
                        <button 
                            id="delete-navigator-item"
                            type="submit" 
                            class="btn btn-danger btn-sm pull-right"
                            @click.stop="unlinkMedium(medium.node_id, medium.value);">
                            <small><i class="fa fa-unlink"></i></small>
                        </button>
                </span>   

                <span class="bg-white text-center p-1 overflow-auto " 
                      style="position:absolute; bottom:0px; height: 150px; width:100%;">
                    <h6 class="events-heading pt-1 hyphens" v-html="medium.title"></h6>
                    <p class=" text-muted small" v-html="medium.description"></p>
                </span>

            </div>
        </span>
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
                errors: {}
            }
        },
        methods: {
            
            async loader() {
                try {
                    this.media = (await axios.post('/repositorySubscriptions/getMedia', {
                        subscribable_type: this.subscribable_type(),
                        subscribable_id: this.model.id,
                        repository: 'edusharing'
                    })).data.message;
                    
                } catch(error) {
                    //this.errors = error.response.data.errors;
                }
            },
            async unlinkMedium(id, value) { //id of external reference and value in db
                try {
                    if (id !== value){
                        if (confirm("Diese Medium ist Teil einer Sammlung. Soll die gesamte Sammlung entfernt werden?") == false) {
                           return;
                        } 
                    }
                    await axios.post('/repositorySubscriptions/destroySubscription', {
                                value: value, 
                                subscribable_id: this.model.id, 
                                subscribable_type: this.subscribable_type(),
                                repository: 'edusharing' 
                            }).data;
                } catch(error) {
                    //this.errors = error.response.data.errors;
                }
                $("#"+id).removeClass( "bg-green" );
                $("#link_btn_"+id).removeClass( "invisible" );
                $("#unlink_btn_"+id).addClass( "invisible" );
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
        },
        computed: {
            
        },
        watch: {
            media: function (value, oldValue) { 
                $("#loading").remove();
            }
        },
        bevorOpen() {
            
        },
   
    }
</script>