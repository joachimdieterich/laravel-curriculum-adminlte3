<template >
    <!--  v-if create terminal-->
    <div v-if="type === 'createterminal'"
         class="box box-objective"
         v-bind:style="{ 'background-color': '#fff'}">
        <h1 class="h5"
            style="position:absolute; top:20px; width:100%;text-align: center;">
            {{ trans("global.terminalObjective.title_singular") }}
        </h1>

        <div style="text-align: center; padding: 25px; font-size:100px;"
             @click.prevent="showModal('terminal-objective-modal', )">
            +
        </div>
    </div>

    <!--  v-else-if create enabling-->
    <div v-else-if="type === 'createenabling'"
         class="box box-objective"
         v-bind:style="{ 'background-color': backgroundcolor  }">
        <h1 class="h5"
            style="position:absolute; top:20px; width:100%;text-align: center; ">
            {{ trans("global.enablingObjective.title_singular") }}
        </h1>

        <div style="text-align: center; padding: 25px; font-size:100px;"
             @click.prevent="showModal('enabling-objective-modal')">
            +
        </div>
    </div>

    <!--  v-else-if render existing objective-->
    <div  v-bind:id="id" v-else
         class="box box-objective"
         v-bind:style="{ 'background-color': backgroundcolor, 'border-color': bordercolor, 'opacity': opacity, 'filter': filter }" >

        <Header :objective="objective"
                :type="type"
                :menuEntries="menuEntries"
                :settings="settings"
                :max_id="max_id"
                :textcolor="textcolor"
                @eventDelete="deleteEvent"
                @eventSort="sortEvent"
                ></Header>

        <div class="panel-body boxwrap pointer"
             @click.prevent="showDetails()">
            <div class="boxscroll hide-scrollbars"
                 v-bind:style="{'background': background, 'background-color': backgroundcolor, 'border-color': objective.color }">
                <div class="boxcontent"
                     v-bind:style="{ 'color': textcolor }"
                     v-html="objective.title">
                </div>
            </div>
        </div>

        <Footer :objective="objective"
                :textcolor="textcolor"
                :type="type"
                :settings="settings"></Footer>
    </div>
</template>


<script>
    import Header from './Header';
    import Footer from './Footer';

    export default {
        props: {
                objective: {},
                objective_type_id: {},
                type: {},
                settings: {},
                max_id: Number
              },
         data() {
            return {
                menuEntries:  [
                    {
                      title: 'Edit',
                      icon: 'fa fa-edit',
                      action: 'update',
                      model: this.type+'Objectives',
                      value: this.type+'-objective-modal'
                    },
                    {
                        title: 'Move',
                        icon: 'fa fa-arrows-alt',
                        action: 'move',
                        model: this.type+'Objectives',
                        value: 'move-'+this.type+'-objective-modal'
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
               visibility: 100,
               errors: {}
            }
        },
        methods: {
            showModal(modal) {
                this.$modal.show(modal, { 'objective': this.objective, 'method': 'post' , 'objective_type_id': this.objective_type_id});
            },
            async deleteEvent(object){
                try {
                    this.location = (await axios.delete('/'+this.type+'Objectives/'+this.objective.id)).data.message
                }
                catch(error) {
                    this.formerrors = error.response.data.errors;
                }
                 location.reload(true);
            },

            async sortEvent(amount) {
                let objective = {
                    'id': this.objective.id,
                    'order_id': this.objective.order_id + parseInt(amount)
                }

                try {
                    this.location = (await axios.patch('/'+this.type+'Objectives/'+this.objective.id, objective)).data.message;
                } catch(error) {
                    this.errors = error.response.data.errors;
                }
                window.location = this.location;
            },
            showDetails(modal) {
                location.href= '/'+this.type+'Objectives/'+this.objective.id;
            },

        },
        computed: {
            background: function () {
                return (this.type === 'terminal' ? 'none' : "");
            },
            backgroundcolor: function () {
                return (this.type === 'terminal' ? this.objective.color : "#fff");
            },
            bordercolor: function () {
                return (this.type === 'terminal' ? this.objective.color : this.objective.terminal_objective.color);
            },
            textcolor: function() {
                if (this.type === 'terminal'){
                    var color = (this.objective.color.charAt(0) === '#') ? this.objective.color.substring(1, 7) : this.objective.color;
                    var r = parseInt(color.substring(0, 2), 16); // hexToR
                    var g = parseInt(color.substring(2, 4), 16); // hexToG
                    var b = parseInt(color.substring(4, 6), 16); // hexToB
                    return (((r * 0.299) + (g * 0.587) + (b * 0.114)) > 140) ?
                      "#000" : "#fff";
                } else {
                    return "#000";
                }
            },
            id: function (){
                return this.type + '_' +this.objective.id;
            },
            opacity: function () {
                if (this.objective.visibility == false) {
                    this.visibility = 20/100;
                    return this.visibility;
                } else {
                    return this.visibility/100;
                }

            },
            filter: function () {
                return "alpha(opacity="+this.visibility+")";
            },
            cross_reference: function() {
                if (typeof this.settings !== "undefined"){
                    return this.settings.cross_reference_curriculum_id;
                } else {
                    return false;
                }
            },
        },
        watch: {
            cross_reference: function() {
                if ((this.settings.cross_reference_curriculum_id !== false) || (this.settings.cross_reference_curriculum_id === "") ){ // reset view with x button
                    this.visibility = 40;

                    if (typeof this.objective.referencing_curriculum_id !== "undefined" ){
                        if ( this.objective.referencing_curriculum_id !== null ){
                            if (this.objective.referencing_curriculum_id.includes(parseInt(this.settings.cross_reference_curriculum_id)))
                            {
                                this.visibility = 100;
                            }
                        }
                    }

//                    if (typeof this.objective.quote_subscriptions !== "undefined"){
//                        let check = this.objective.quote_subscriptions.find(c => c.siblings.find(s => s.quotable.curriculum_id == this.settings.cross_reference_curriculum_id))
//                        if (typeof check !== "undefined"){
//                              this.visibility = 100;
//                        }
//                    }

                } else {
                    this.visibility = 100;
                }

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
        mounted() {
            this.$nextTick(() => {
                MathJax.startup.defaultReady();
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

