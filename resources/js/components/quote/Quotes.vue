<template>
    <div>
        <div v-for="curriculum in quote_curricula_list">
              <span class="col-xs-12" 
                  data-toggle="collapse" 
                  :data-target="tagName(curriculum.id)" >
                <h4 class="text-black" >
                   {{curriculum.title}}
                    <small>
                    
                    </small>
                    <button class="btn btn-box-tool pull-right" 
                            style="padding-top:0;" 
                            type="button" 
                            data-toggle="collapse" 
                            :data-target="tagName(curriculum.id)" 
                            aria-expanded="true" 
                            title="Fach einklappen bzw. ausklappen">
                        <i class="fa fa-expand"></i>
                    </button>
                </h4>
            </span>
            <div v-for="filtered_quote in filterQuotes(curriculum.id)">
                <span id ="curriculum" class=""> 
                    <div id="" class="row">
                        <div class="col-xs-12" >
                            <dt>{{filtered_quote.quote.content.title}}</dt>
                            <div v-html="filtered_quote.quote.content.content"></div>>
                        </div>
                    </div>
                    <hr style="clear:both;">
                </span>
            </div>
        </div>
        </div>
          
        
    </div>
     
</template>


<script>
   
    
    export default {
        props: ['quote_subscriptions', 'quote_curricula_list'],
        data: function() {
            return {
             
               
            }
        },

        methods: {
           filterQuotes(curriculum_id) {
               
                let filterQuotes = this.quote_subscriptions;
                filterQuotes = filterQuotes.filter(
                    c => c.quote.content.subscriptions[0].subscribable_id === curriculum_id
                  );
                
                return filterQuotes;
            },     
           tagName: function(i){
                return '#quote_curriculum_'+i;
            },
           
            
           
           
        }, 
       
        components: {
         
        },
       
        }
</script>
