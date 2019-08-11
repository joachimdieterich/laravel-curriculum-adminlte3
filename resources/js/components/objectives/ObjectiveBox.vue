<template >
    <!--  v-if create terminal-->
    <div v-if="type === 'createterminal'" 
         class="box box-objective" 
         v-bind:style="{ 'background-color': '#fff' }"> 
        <h5 style="position:absolute; top:20px; width:100%;text-align: center; ">
            {{ trans("global.terminalObjective.title_singular") }}
        </h5>

        <div style="text-align: center; padding: 25px; font-size:100px;"  
             @click.prevent="showModal('terminal-objective-modal')">
             +
        </div>
    </div>

    <!--  v-else-if create enabling-->
    <div  v-else-if="type === 'createenabling'" 
          class="box box-objective"
          v-bind:style="{ 'background-color': backgroundcolor  }"> 
        <h5 style="position:absolute; top:20px; width:100%;text-align: center; ">
            {{ trans("global.enablingObjective.title_singular") }}
        </h5>
                        
        <div style="text-align: center; padding: 25px; font-size:100px;"  
             @click.prevent="showModal('enabling-objective-modal')">
            +
        </div>
    </div>
    
    <!--  v-else-if render existing objective-->
    <div  v-bind:id="id" v-else 
         class="box box-objective" 
         v-bind:style="{ 'background-color': backgroundcolor, 'border-color': objective.color,  }" > 

        <Header :objective="objective" 
                :type="type" 
                :menuEntries="menuEntries" 
                :settings="settings"
                :textcolor="textcolor"
                @eventDelete="deleteEvent"
                @eventSort="sortEvent"
                ></Header>

        <div style="padding: 5px;" class="panel-body boxwrap" >
            <div class="boxscroll" 
                 v-bind:style="{ 'background-color': backgroundcolor, 'border-color': objective.color }"
                 >
                <div class="boxcontent" 
                     v-bind:style="{ 'color': textcolor }"
                     v-html="objective.title">
                </div>
            </div>
        </div>

        <Footer :objective="objective"
                :textcolor="textcolor"
                :type="type"></Footer>

    </div>
</template>


<script>
    import Header from './Header'
    import Footer from './Footer'
   
    export default {
        props: {
                objective: {},
                type: {},
                settings: {},
              },
         data() {
            return {
                menuEntries:  [
                    {
                      title: 'Add Content',
                      icon: 'fa fa-file',
                      action: 'create',
                      model: 'content'
                    },
                    {
                      title: 'Add Material',
                      icon: 'fa fa-plus',
                      action: 'create',
                      model: 'media',
                    },
                    {
                      hr: true,
                    },
                    {
                      title: 'Edit',
                      icon: 'fa fa-edit',
                      action: 'update',
                      model: this.type+'Objectives',
                      value: this.type+'-objective-modal'
                    },
                    {
                      hr: true,
                    },
                    {
                      title: 'Delete',
                      icon: 'fa fa-minus',
                      action: 'delete',
                      model: this.type+'Objectives',
                    }
               ],
                
                errors: {}
            }
        },
        methods: {
            showModal(modal) { 
                this.$modal.show(modal, { 'objective': this.objective, 'method': 'post' });
            },
            async deleteEvent(object){
                console.log('delete'+object);
                try {   
                    this.location = (await axios.delete('/'+this.type+'Objectives/'+this.objective.id)).data.message
                }
                catch(error) {
                    this.formerrors = error.response.data.errors;
                }
                 window.location = this.location;
            }, 
            
            async sortEvent(amount) {
                console.log(this.type +' id: '+ this.objective.id +' sort' + amount);
                
                let objective = {
                    'id': this.objective.id,
                    'order_id': this.objective.order_id + parseInt(amount)
                }
                console.log(JSON.stringify(objective));
                try {
                    this.location = (await axios.patch('/'+this.type+'Objectives/'+this.objective.id, objective)).data.message;
                } catch(error) {
                    this.errors = error.response.data.errors;
                } 
                window.location = this.location;
            },  
            
        },
        computed: {
            backgroundcolor: function () {
                return (this.type === 'terminal' ? this.objective.color : "#fff");
            },
            textcolor: function() {
                if (this.type === 'terminal'){
                    var color = (this.objective.color.charAt(0) === '#') ? this.objective.color.substring(1, 7) : this.objective.color;
                    var r = parseInt(color.substring(0, 2), 16); // hexToR
                    var g = parseInt(color.substring(2, 4), 16); // hexToG
                    var b = parseInt(color.substring(4, 6), 16); // hexToB
                    return (((r * 0.299) + (g * 0.587) + (b * 0.114)) > 186) ?
                      "#000" :  "#fff";
                } else {
                    return "#000";
                }
            },
            id: function (){
                return this.type + '_' +this.objective.id;
            }
        },
        created: function () {
            this.$root.$on('eventDelete', () => {
                this.deleteEvent()
            });
            this.$root.$on('eventSort', () => {
                this.sortEvent()
            })
        
        },
       
        beforeDestroy: function () {
            this.$root.$off('eventDelete');
            this.$root.$off('eventSort')
        },
          
        
        components: {
            Header, 
            Footer,
        }, 
    }
</script>

<style>
/*styles (xs, sm) */
@media (max-width: 990px) {
  .box.box-objective {
    float: left;
    width: 100%;
    height: 100%;
    position: relative;
    margin: 0px;
    padding-left: 5px;
    padding-bottom: 0px;
    border-bottom: 1px solid #eee;
    border: 1px solid #d2d6de;
  }
  .box .boxheader {
    margin: 0px;
    position: absolute;
    left: 0px;
    padding: 5px 2px 0 2px;
    height: 100px;
    width: 5px;
    text-align: center;
  }
  .box .boxscroll {
    /*width: 170px; */
  
    /*20px more than box to hide scrollbar*/
  
    padding-top: 5px;
    padding-right: 25px;
    /*for Firefox only*/
  
    overflow-x: hidden;
    overflow-y: auto;
    height: 100px!important;
  }
  .box .boxwrap {
    width: 100%;
    overflow: hidden;
    margin: 0px !important;
    padding: 0 0 0 20px;
  }
  .box .boxcontent {
    /*Hack: boxwrap, boxscroll and boxcontent used to hide scroll crossbrowser compatible*/
  
    margin: 0px;
    padding-left: 10px;
    padding-right: 60px;
    font-size: 14px;
    line-height: 18px;
    
  }
  .box .boxfooter {
    margin: -100px 0 0 15px;
    padding: 4px 0px 0 10px;
    position: absolute;
    right: 0px;
    width: 59px;
    height: 100px;
    overflow: auto;
    z-index: 1;
  }
  .box .box-sm-icon {
    display: inline;
    text-align: center;
    padding: 3px 1px 5px 1px;
    float: none !important;
  }
}
/* /.sm view */
/*styles (md, lg) */
@media (min-width: 991px) {
  .box.box-objective {
    float: left;
    height: 200px;
    width: 200px;
    position: relative;
    margin: 0px;
    margin-bottom: 10px;
    margin-right: 10px;
    border: 1px solid #d2d6de;
  }
  .box .boxheader {
    width: 100%;
    margin: 0px;
    margin-bottom: 20px;
    padding: 4px 10px 0 10px;
    height: 5px;
    font-size: 90%;
    position: relative;
    /* add top right radius*/
  
    -webkit-border-top-right-radius: 0 !important;
    -moz-border-top-right-radius: 0 !important;
    border-top-right-radius: 0 !important;
  }
  .box .boxheader > span,
  .box .boxheader > a > span,
  .box .padding-top-5 {
    padding-top: 5px;
  }
  .box .boxscroll {
    width: 200px;
    padding-top: 5px;
    padding-right: 25px;
    /*for Firefox only*/
  
    overflow-x: hidden;
    overflow-y: auto;
    height: 150px!important;
    background-repeat: no-repeat;
    background-color: white;
    background-size: 100% 40px, 100% 40px, 100% 14px, 100% 14px;
    /* Opera doesn't support this in the shorthand */
  
    background-attachment: local, local, scroll, scroll;
    height: 140px !important;
  }
  .box .boxwrap {
    width: 100%;
    overflow: hidden;
    margin: 0px !important;
    padding: 0px;
  }
  .box .boxcontent {
    /*Hack: boxwrap, boxscroll and boxcontent used to hide scroll crossbrowser compatible*/
  
    margin: 0px;
    width: 200px;
    padding-left: 10px;
    padding-right: 10px;
    font-size: 14px;
    line-height: 18px;
  }
  .box .boxfooter {
    position: absolute;
    margin: 0px;
    padding: 4px 10px 0 10px;
    top: 173px;
    bottom: 0px;
    left: 0px;
    right: 0px;
    height: 25px;
    width: 100%;
    font-size: 90%;
    text-align: left !important;
    /* get rid of top right radius*/
  
    -webkit-border-top-right-radius: 0 !important;
    -moz-border-top-right-radius: 0 !important;
    border-top-right-radius: 0 !important;
  }
}


</style>
