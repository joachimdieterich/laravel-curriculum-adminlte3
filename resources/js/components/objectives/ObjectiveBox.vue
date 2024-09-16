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
             @click.prevent="createTerminalObjective()">
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
             @click.prevent="createEnablingObjective()">
            +
        </div>
    </div>

    <!--  v-else-if render existing objective-->
    <div  v-bind:id="id" v-else
         class="box box-objective"
         v-bind:style="{ 'background-color': backgroundcolor, 'border-color': bordercolor, 'opacity': opacity, 'filter': filter }"
    >
        <!-- don't load Header if it isn't needed -->
        <Header v-if="settings?.edit == true"
            :objective="objective"
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
                     v-dompurify-html="objective.title">
                </div>
            </div>
        </div>

        <Footer :objective="objective"
                :textcolor="textcolor"
                :type="type"
                :settings="settings">
        </Footer>
    </div>
</template>


<script>
    import Header from './Header.vue';
    import Footer from './Footer.vue';

export default {
    props: {
        objective: {},
        objective_type_id: {},
        type: {},
        settings: {

        },
        editable: {
            default: false
        },
        max_id: Number,
    },
    data() {
        return {
            menuEntries:  [
                {
                    title: 'Edit',
                    icon: 'fa fa-pencil-alt',
                    action: 'edit',
                    model: this.type+'Objectives',
                    value: this.type+'-objective-modal'
                },
                {
                    title: 'Move',
                    icon: 'fa fa-repeat',
                    action: 'move',
                    model: this.type+'Objectives',
                    value: 'move-'+this.type+'-objective-modal'
                },
                {
                    hr: true,
                },
                {
                    title: 'Delete',
                    icon: 'fa fa-trash',
                    action: 'delete',
                    model: this.type+'Objectives',
                }
            ],
            visibility: 100,
            errors: {}
        }
    },
    methods: {
        createTerminalObjective(){
            this.$eventHub.emit('createTerminalObjectives', { 'objective': this.objective, 'method': 'post' , 'objective_type_id': this.objective_type_id});
        },
        createEnablingObjective(){
            this.$eventHub.emit('createEnablingObjectives', { 'objective': this.objective, 'method': 'post' });
        },
         deleteEvent(){
             axios.delete('/'+this.type+'Objectives/'+this.objective.id)
                 .then(res => {
                     this.$eventHub.emit('objective-deleted', {'objective': this.objective, 'type': this.type});
                 })
                 .catch(err => {
                     console.log(err.response);
                 });
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
        showDetails() {
            if (this.settings?.achievements === undefined || !this.editable) {
                location.href= '/'+this.type+'Objectives/'+this.objective.id;
            } else {
                //todo: change -> this.$modal.show('set-achievements-modal', { 'objective': this.objective });
            }
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
                return this.$textcolor(this.objective.color);
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
        this.$eventHub.on('deleteObjective', function(deletedObjective) {
            if (this.objective === deletedObjective){
                this.deleteEvent()
            }
        }.bind(this));
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
